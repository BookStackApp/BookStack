#!/usr/bin/env python3
"""
BookStack → DokuWiki Migration Tool
Integrated API client with intelligent data source selection (DB vs API).
"""

from __future__ import annotations

import argparse
import importlib
import json
import logging
import os
import subprocess
import sys
from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Tuple

import requests
import tarfile
import time
from datetime import datetime
import shutil
import secrets

__version__ = "1.0.0"


# ============================================================================
# VENV CHECK (Runtime Safety)
# ============================================================================

def check_venv_and_prompt() -> None:
    """Check if running in virtual environment; prompt to install if not."""
    in_venv = hasattr(sys, "real_prefix") or (hasattr(sys, "base_prefix") and sys.base_prefix != sys.prefix)
    
    if not in_venv:
        print("\n⚠️  WARNING: Not running in a virtual environment!")
        print("   It's recommended to use a venv to avoid conflicts:")
        print("   $ python3 -m venv venv")
        print("   $ source venv/bin/activate")
        print("   $ pip install -e .")
        print("   $ bookstack-migrate --help")
        print()
        response = input("Continue anyway? (y/n): ").strip().lower()
        if response not in {"y", "yes"}:
            print("Aborted.")
            sys.exit(0)

# Logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler("bookstack_migrate.log"),
    ],
)
logger = logging.getLogger(__name__)


# ============================================================================
# API CLIENT
# ============================================================================

API_PREFIX = "/api"
DEFAULT_TIMEOUT = 15
DEFAULT_SPEC_CACHE = Path.home() / ".cache" / "bookstack" / "openapi.json"


class BookStackError(Exception):
    """Raised when the BookStack API returns an error response."""

    def __init__(self, message: str, status: Optional[int] = None, body: Optional[str] = None):
        super().__init__(message)
        self.status = status
        self.body = body

    def __str__(self) -> str:
        suffix = f" (status={self.status})" if self.status is not None else ""
        return f"{super().__str__()}{suffix}"


class MigrationCheckpoint:
    """Manages checkpoints for resumable migrations."""

    def __init__(self, output_dir: Path):
        self.output_dir = Path(output_dir)
        self.checkpoint_file = self.output_dir / ".migration_checkpoint.json"
        self.timestamp = datetime.now().strftime("%Y%m%d")
        self.data: Dict[str, Any] = self._load()

    def _load(self) -> Dict[str, Any]:
        """Load checkpoint data if exists."""
        if self.checkpoint_file.exists():
            try:
                with open(self.checkpoint_file) as f:
                    return json.load(f)
            except Exception as e:
                logger.warning(f"Could not load checkpoint: {e}")
        return {"pages": [], "chapters": [], "books": [], "start_time": time.time()}

    def save(self) -> None:
        """Save checkpoint to disk."""
        self.checkpoint_file.parent.mkdir(parents=True, exist_ok=True)
        with open(self.checkpoint_file, "w") as f:
            json.dump(self.data, f, indent=2, default=str)
        logger.info(f"Checkpoint saved: {self.checkpoint_file}")

    def add_page(self, page_id: int, page_name: str) -> None:
        """Mark page as exported."""
        if {"id": page_id, "name": page_name} not in self.data["pages"]:
            self.data["pages"].append({"id": page_id, "name": page_name})
            self.save()

    def mark_incomplete(self) -> Optional[str]:
        """On interrupt, create _incomplete.tar.gz with current progress."""
        elapsed = time.time() - self.data["start_time"]
        archive_name = f"{self.timestamp}_bookstack_migrate_incomplete.tar.gz"
        archive_path = Path.home() / "Downloads" / archive_name

        try:
            archive_path.parent.mkdir(parents=True, exist_ok=True)
            with tarfile.open(archive_path, "w:gz") as tar:
                # Add output directory and checkpoint
                if self.output_dir.exists():
                    tar.add(self.output_dir, arcname=self.output_dir.name)
                if self.checkpoint_file.exists():
                    tar.add(self.checkpoint_file, arcname=self.checkpoint_file.name)
            
            logger.info(f"Incomplete migration archived: {archive_path}")
            print(f"\n💾 Incomplete migration saved: {archive_path}")
            print(f"   Pages exported: {len(self.data['pages'])}")
            print(f"   Elapsed time: {elapsed:.1f}s")
            print(f"   To resume: Extract archive and rerun with same parameters")
            return str(archive_path)
        except Exception as e:
            logger.error(f"Failed to create incomplete archive: {e}")
            return None


class SqlDumpImportError(BookStackError):
    pass


class SqlDumpImporter:
    """Import a MySQL/MariaDB .sql dump into a temporary MariaDB container.

    This is intended to let users migrate from a database dump without needing
    a running database server on the host.
    """

    def __init__(self, sql_file: Path, database: str = "bookstack"):
        self.sql_file = Path(sql_file)
        self.database = database
        self.container_id: Optional[str] = None
        self.root_password = secrets.token_urlsafe(18)
        self.host = "127.0.0.1"
        self.port: Optional[int] = None

    def _require_docker(self) -> None:
        if shutil.which("docker") is None:
            raise SqlDumpImportError(
                "Docker is required for --sql-file mode but was not found in PATH. "
                "Restore the dump into your MySQL/MariaDB server and use --host/--port/--db instead."
            )

    def _run(self, args: List[str], input_bytes: Optional[bytes] = None) -> str:
        try:
            res = subprocess.run(
                args,
                input=input_bytes,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                check=True,
            )
            return res.stdout.decode("utf-8", errors="replace").strip()
        except subprocess.CalledProcessError as e:
            msg = e.stderr.decode("utf-8", errors="replace").strip() or str(e)
            raise SqlDumpImportError(f"SQL import command failed: {' '.join(args)}\n{msg}")

    def start_and_import(self, timeout_seconds: int = 60) -> Tuple[str, int, str, str, str]:
        """Start a temp container, import dump, and return connection info.

        Returns: (host, port, db, user, password)
        """
        self._require_docker()

        if not self.sql_file.exists() or not self.sql_file.is_file():
            raise SqlDumpImportError(f"SQL file not found: {self.sql_file}")

        # Start MariaDB and publish 3306 to a random host port.
        out = self._run(
            [
                "docker",
                "run",
                "-d",
                "--rm",
                "-e",
                f"MARIADB_ROOT_PASSWORD={self.root_password}",
                "-e",
                f"MARIADB_DATABASE={self.database}",
                "-P",
                "mariadb:10.11",
            ]
        )
        self.container_id = out.splitlines()[-1].strip()
        logger.info(f"Started temp MariaDB container: {self.container_id}")

        # Wait for DB readiness.
        start = time.time()
        while time.time() - start < timeout_seconds:
            try:
                subprocess.run(
                    [
                        "docker",
                        "exec",
                        self.container_id,
                        "mariadb-admin",
                        "ping",
                        "-uroot",
                        f"-p{self.root_password}",
                    ],
                    stdout=subprocess.DEVNULL,
                    stderr=subprocess.DEVNULL,
                    check=True,
                )
                break
            except Exception:
                time.sleep(1)
        else:
            raise SqlDumpImportError("Timed out waiting for MariaDB container to be ready")

        # Determine host port mapping.
        port_out = self._run(["docker", "port", self.container_id, "3306/tcp"])
        # Example: 0.0.0.0:49154 or :::49154
        mapped = port_out.split(":")[-1]
        try:
            self.port = int(mapped)
        except ValueError:
            raise SqlDumpImportError(f"Could not determine mapped MariaDB port from: {port_out}")

        logger.info(f"MariaDB port mapping: {self.host}:{self.port}")

        # Import dump via stdin into mariadb client inside container.
        # Stream to avoid loading large dumps into memory.
        logger.info(f"Importing SQL dump into temp database '{self.database}'")
        cmd = [
            "docker",
            "exec",
            "-i",
            self.container_id,
            "mariadb",
            "-uroot",
            f"-p{self.root_password}",
            self.database,
        ]
        try:
            with open(self.sql_file, "rb") as f:
                proc = subprocess.Popen(cmd, stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
                assert proc.stdin is not None
                shutil.copyfileobj(f, proc.stdin)
                proc.stdin.close()
                out, err = proc.communicate()
                if proc.returncode != 0:
                    raise SqlDumpImportError(
                        f"SQL import command failed: {' '.join(cmd)}\n"
                        f"{err.decode('utf-8', errors='replace').strip()}"
                    )
        except SqlDumpImportError:
            raise
        except Exception as e:
            raise SqlDumpImportError(f"Failed to stream SQL dump into container: {e}")

        return (self.host, self.port, self.database, "root", self.root_password)

    def cleanup(self) -> None:
        if not self.container_id:
            return
        try:
            subprocess.run(
                ["docker", "stop", self.container_id],
                stdout=subprocess.DEVNULL,
                stderr=subprocess.DEVNULL,
                check=False,
            )
        finally:
            logger.info(f"Stopped temp MariaDB container: {self.container_id}")
            self.container_id = None


@dataclass
class PageRef:
    id: int
    name: str
    slug: str
    book_id: Optional[int] = None
    chapter_id: Optional[int] = None


@dataclass
class EnvConfig:
    base_url: str
    token_id: str
    token_secret: str
    spec_url: Optional[str] = None
    spec_cache: Path = DEFAULT_SPEC_CACHE


class BookStackClient:
    """REST API client for BookStack with automatic error handling."""

    def __init__(
        self,
        base_url: str,
        token_id: str,
        token_secret: str,
        timeout: int = DEFAULT_TIMEOUT,
    ) -> None:
        if not base_url:
            raise ValueError("base_url is required")
        self.base_url = base_url.rstrip("/")
        self.timeout = timeout
        self.session = requests.Session()
        self.session.headers.update(
            {
                "Authorization": f"Token {token_id}:{token_secret}",
                "Accept": "application/json",
                "Content-Type": "application/json",
            }
        )

    @classmethod
    def from_env(cls, timeout: int = DEFAULT_TIMEOUT) -> "BookStackClient":
        cfg = read_env_config()
        return cls(cfg.base_url, cfg.token_id, cfg.token_secret, timeout=timeout)

    def test_connection(self) -> bool:
        """Test if API is accessible."""
        try:
            self._get("/")
            return True
        except Exception:
            return False

    def list_books(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/books", params={"page": page, "count": count})

    def get_book(self, book_id: int) -> Dict[str, Any]:
        return self._get(f"/books/{book_id}")

    def list_chapters(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/chapters", params={"page": page, "count": count})

    def get_chapter(self, chapter_id: int) -> Dict[str, Any]:
        return self._get(f"/chapters/{chapter_id}")

    def list_shelves(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/shelves", params={"page": page, "count": count})

    def get_shelf(self, shelf_id: int) -> Dict[str, Any]:
        return self._get(f"/shelves/{shelf_id}")

    def list_shelf_books(self, shelf_id: int, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get(f"/shelves/{shelf_id}/books", params={"page": page, "count": count})

    def list_pages(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/pages", params={"page": page, "count": count})

    def get_total_pages(self) -> Optional[int]:
        """Best-effort total page count from API, if provided by server."""
        try:
            resp = self.list_pages(page=1, count=1)
            total = resp.get("total")
            if isinstance(total, int):
                return total
        except Exception:
            return None
        return None

    def list_book_pages(self, book_id: int, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get(f"/books/{book_id}/pages", params={"page": page, "count": count})

    def search(self, query: str, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/search", params={"query": query, "page": page, "count": count})

    def get_page(self, page_id: int) -> Dict[str, Any]:
        return self._get(f"/pages/{page_id}")

    def export_page_html(self, page_id: int) -> str:
        """Return rendered HTML for a page."""
        resp = self._request("GET", f"/pages/{page_id}/export/html")
        return resp.text

    def export_page_markdown(self, page_id: int) -> str:
        resp = self._request("GET", f"/pages/{page_id}/export/markdown")
        return resp.text

    def export_page_plaintext(self, page_id: int) -> str:
        resp = self._request("GET", f"/pages/{page_id}/export/plaintext")
        return resp.text

    def iter_pages(self, count: int = 50) -> Iterable[PageRef]:
        """Iterate through all pages using simple pagination."""
        page_num = 1
        while True:
            payload = self.list_pages(page=page_num, count=count)
            data = payload.get("data", []) or []
            for item in data:
                yield PageRef(
                    id=item.get("id"),
                    name=item.get("name"),
                    slug=item.get("slug"),
                    book_id=item.get("book_id"),
                    chapter_id=item.get("chapter_id"),
                )

            if not payload.get("next_page_url") or not data:
                break
            page_num += 1

    def iter_shelves(self, count: int = 50) -> Iterable[Dict[str, Any]]:
        page_num = 1
        while True:
            payload = self.list_shelves(page=page_num, count=count)
            data = payload.get("data", []) or []
            for item in data:
                if isinstance(item, dict):
                    yield item

            if not payload.get("next_page_url") or not data:
                break
            page_num += 1

    def _get(self, path: str, params: Optional[Dict[str, Any]] = None) -> Dict[str, Any]:
        resp = self._request("GET", path, params=params)
        return self._parse_json(resp)

    def _parse_json(self, resp: requests.Response) -> Dict[str, Any]:
        try:
            return resp.json()
        except json.JSONDecodeError as exc:
            raise BookStackError("Invalid JSON response", status=resp.status_code, body=resp.text) from exc

    def _request(self, method: str, path: str, **kwargs: Any) -> requests.Response:
        url = self._build_url(path)

        # Retry policy: keep default low to avoid hanging forever.
        max_retries = int(os.environ.get("BOOKSTACK_RETRIES", "2"))
        backoff = float(os.environ.get("BOOKSTACK_RETRY_BACKOFF", "0.25"))

        last_exc: Optional[Exception] = None
        for attempt in range(max_retries + 1):
            try:
                resp = self.session.request(method, url, timeout=self.timeout, **kwargs)

                # Retry on transient server errors and rate limits.
                if resp.status_code in {429} or 500 <= resp.status_code <= 599:
                    if attempt < max_retries:
                        time.sleep(backoff * (2 ** attempt))
                        continue

                if resp.status_code >= 400:
                    raise BookStackError(
                        f"BookStack API error {resp.status_code}",
                        status=resp.status_code,
                        body=resp.text,
                    )
                return resp
            except (requests.RequestException, BookStackError) as exc:
                last_exc = exc
                if attempt < max_retries:
                    time.sleep(backoff * (2 ** attempt))
                    continue
                raise

        # Should not reach here.
        raise BookStackError(f"BookStack API request failed: {last_exc}")

    def _build_url(self, path: str) -> str:
        if not path.startswith("/"):
            path = "/" + path
        return f"{self.base_url}{API_PREFIX}{path}"


def read_env_config() -> EnvConfig:
    """Read config from environment. Does not prompt."""
    base_url = os.environ.get("BOOKSTACK_BASE_URL") or os.environ.get("BOOKSTACK_URL") or "http://localhost:8000"
    token_id = os.environ.get("BOOKSTACK_TOKEN_ID") or os.environ.get("BOOKSTACK_API_TOKEN_ID")
    token_secret = os.environ.get("BOOKSTACK_TOKEN_SECRET") or os.environ.get("BOOKSTACK_API_TOKEN_SECRET")
    spec_url = os.environ.get("BOOKSTACK_SPEC_URL")
    spec_cache = Path(os.environ.get("BOOKSTACK_SPEC_CACHE") or DEFAULT_SPEC_CACHE)

    if not token_id or not token_secret:
        raise ValueError("BOOKSTACK_TOKEN_ID/BOOKSTACK_TOKEN_SECRET are required for API access")

    return EnvConfig(
        base_url=base_url.rstrip("/"),
        token_id=token_id,
        token_secret=token_secret,
        spec_url=spec_url,
        spec_cache=spec_cache,
    )


def fetch_openapi_spec(
    base_url: str,
    session: requests.Session,
    spec_url: Optional[str] = None,
    cache_path: Optional[Path] = None,
    force_refresh: bool = False,
) -> Dict[str, Any]:
    """Fetch OpenAPI JSON from the BookStack instance, optionally caching it."""

    if cache_path and cache_path.exists() and not force_refresh:
        try:
            return json.loads(cache_path.read_text())
        except Exception:
            pass

    candidates = []
    if spec_url:
        candidates.append(spec_url)
    base = base_url.rstrip("/")
    candidates.extend(
        [
            f"{base}/api/docs.json",
            f"{base}/api/docs?format=openapi",
            f"{base}/api/docs",
        ]
    )

    last_err: Optional[Exception] = None
    for url in candidates:
        try:
            resp = session.get(url, timeout=DEFAULT_TIMEOUT)
            if resp.status_code >= 400:
                last_err = BookStackError(
                    f"Spec fetch failed {resp.status_code}",
                    status=resp.status_code,
                    body=resp.text,
                )
                continue
            data = resp.json()
            if cache_path:
                cache_path.parent.mkdir(parents=True, exist_ok=True)
                cache_path.write_text(json.dumps(data, indent=2))
            return data
        except Exception as exc:
            last_err = exc
            continue

    if last_err:
        raise BookStackError(f"Failed to fetch OpenAPI spec: {last_err}") from last_err
    raise BookStackError("Failed to fetch OpenAPI spec: no candidates succeeded")


def load_spec_from_env(force_refresh: bool = False) -> Dict[str, Any]:
    """Fetch (and cache) the OpenAPI spec using environment config."""
    cfg = read_env_config()
    session = requests.Session()
    session.headers.update({"Authorization": f"Token {cfg.token_id}:{cfg.token_secret}"})
    return fetch_openapi_spec(
        base_url=cfg.base_url,
        session=session,
        spec_url=cfg.spec_url,
        cache_path=cfg.spec_cache,
        force_refresh=force_refresh,
    )


# ============================================================================
# MIGRATION LOGIC
# ============================================================================


@dataclass
class DokuWikiInstall:
    path: Path
    pages_dir: Path
    media_dir: Path
    install_type: str  # apt, manual, docker, custom
    writable: bool


@dataclass
class ExportOptions:
    db: Optional[str] = None
    user: Optional[str] = None
    password: Optional[str] = None
    host: str = "localhost"
    port: int = 3306
    output: Path = Path("./export")
    driver: Optional[str] = None
    prefer_api: bool = False
    sql_file: Optional[Path] = None
    sql_db: str = "bookstack"
    justdoit: bool = False


class DataSourceSelector:
    """Intelligently select between DB and API for data retrieval."""

    def __init__(
        self,
        db_available: bool,
        api_available: bool,
        prefer_api: bool = False,
        large_instance: bool = False,
    ):
        self.db_available = db_available
        self.api_available = api_available
        self.prefer_api = prefer_api
        self.large_instance = large_instance
        logger.info(
            f"DataSourceSelector: DB={db_available}, API={api_available}, prefer_api={prefer_api}, large={large_instance}"
        )

    def should_use_api(self) -> bool:
        """Determine if we should use API instead of DB."""
        if self.prefer_api and self.api_available:
            logger.info("Using API (preferred)")
            return True
        if not self.db_available and self.api_available:
            logger.info("Using API (DB not available)")
            return True
        if self.db_available:
            logger.info("Using database (preferred method)")
            return False
        logger.warning("No data source available!")
        return False

    def get_best_source(self) -> str:
        """Return 'api' or 'database' or 'none'."""
        # If instance is large and DB/SQL is available, force DB for performance.
        if self.large_instance and self.db_available:
            return "database"

        if self.db_available and (not self.prefer_api or not self.api_available):
            return "database"
        if self.api_available:
            return "api"
        return "none"


def is_large_instance(
    *,
    client: Optional[BookStackClient],
    sql_file: Optional[Path],
    large_pages_threshold: int,
    large_sql_mb_threshold: int,
) -> bool:
    """Heuristic for deciding when to avoid API mode for performance."""
    if sql_file is not None:
        try:
            size_mb = sql_file.stat().st_size / (1024 * 1024)
            if size_mb >= large_sql_mb_threshold:
                return True
        except Exception:
            pass

    if client is not None:
        total = client.get_total_pages()
        if isinstance(total, int) and total >= large_pages_threshold:
            return True

    return False


def detect_dokuwiki() -> List[DokuWikiInstall]:
    """Detect all DokuWiki installations on system."""
    search_paths = [
        "/var/www/dokuwiki",
        "/var/lib/dokuwiki",
        "/usr/share/dokuwiki",
        "/opt/dokuwiki",
        Path.home() / "dokuwiki",
    ]

    found: List[DokuWikiInstall] = []

    for path_str in search_paths:
        path = Path(path_str)
        if not path.exists():
            continue

        init_file = path / "inc" / "init.php"
        conf_dir = path / "conf"

        if init_file.exists() and conf_dir.exists():
            pages_dir = path / "data" / "pages"
            media_dir = path / "data" / "media"

            if pages_dir.exists() and media_dir.exists():
                writable = os.access(pages_dir, os.W_OK)

                if "var/lib" in str(path):
                    install_type = "apt"
                elif "var/www" in str(path):
                    install_type = "manual"
                else:
                    install_type = "custom"

                found.append(
                    DokuWikiInstall(
                        path=path,
                        pages_dir=pages_dir,
                        media_dir=media_dir,
                        install_type=install_type,
                        writable=writable,
                    )
                )

    return found


def _sanitize_namespace_part(value: str, fallback: str) -> str:
    """Sanitize a path segment for DokuWiki namespace/page file usage."""
    cleaned = (value or "").strip().lower()
    if not cleaned:
        return fallback
    out_chars: List[str] = []
    for ch in cleaned:
        if ch.isalnum() or ch in {"-", "_"}:
            out_chars.append(ch)
        elif ch.isspace() or ch in {"/", "\\", ":"}:
            out_chars.append("_")
        # else: drop
    out = "".join(out_chars).strip("_")
    return out or fallback


def _convert_markdown_to_dokuwiki(markdown: str, title: str) -> str:
    """Best-effort conversion from BookStack markdown/html-ish content to DokuWiki syntax."""
    content = markdown or ""

    # Normalize line endings
    content = content.replace("\r\n", "\n")

    # Headings: # -> ======
    import re

    content = re.sub(r"^######\s+(.+)$", r"= \1 =", content, flags=re.MULTILINE)
    content = re.sub(r"^#####\s+(.+)$", r"== \1 ==", content, flags=re.MULTILINE)
    content = re.sub(r"^####\s+(.+)$", r"=== \1 ===", content, flags=re.MULTILINE)
    content = re.sub(r"^###\s+(.+)$", r"==== \1 ====", content, flags=re.MULTILINE)
    content = re.sub(r"^##\s+(.+)$", r"===== \1 =====", content, flags=re.MULTILINE)
    content = re.sub(r"^#\s+(.+)$", r"====== \1 ======", content, flags=re.MULTILINE)

    # Links: [text](url) -> [[url|text]]
    content = re.sub(r"\[([^\]]+)\]\(([^\)]+)\)", r"[[\2|\1]]", content)

    # Images: ![alt](url) -> {{url|alt}}
    content = re.sub(r"!\[([^\]]*)\]\(([^\)]+)\)", r"{{\2|\1}}", content)

    # Bold/italic (keep simple)
    content = re.sub(r"\*\*([^\*]+)\*\*", r"**\1**", content)
    content = re.sub(r"__([^_]+)__", r"**\1**", content)
    content = re.sub(r"(?<!\*)\*([^\*]+)\*(?!\*)", r"//\1//", content)
    content = re.sub(r"(?<!_)_([^_]+)_(?!_)", r"//\1//", content)

    header = f"====== {title} ======\n\n"
    return header + content.strip() + "\n"


def _write_text_file(path: Path, content: str) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(content, encoding="utf-8")


def _ensure_start_page(dir_path: Path, title: str) -> None:
    start_file = dir_path / "start.txt"
    if start_file.exists():
        return
    _write_text_file(start_file, f"====== {title} ======\n")


def _page_id_from_parts(parts: List[str], page_slug: str) -> str:
    ns = ":".join([p for p in parts if p])
    if ns:
        return f"{ns}:{page_slug}"
    return page_slug


def _namespace_id_from_parts(parts: List[str]) -> str:
    return ":".join([p for p in parts if p])


def _write_namespace_index(
    *,
    file_path: Path,
    title: str,
    child_namespaces: List[Tuple[str, str]],
    child_pages: List[Tuple[str, str]],
) -> None:
    """Write a DokuWiki 'start.txt' index page.

    child_namespaces: List[(namespace_id, display_name)]
    child_pages:      List[(page_id, display_name)]
    """
    lines: List[str] = [f"====== {title} ======", ""]

    if child_namespaces:
        lines.append("===== Contents =====")
        lines.append("")
        for ns_id, name in sorted(child_namespaces, key=lambda x: x[1].lower()):
            # Link to namespace start page explicitly.
            lines.append(f"  * [[{ns_id}:start|{name}]]")
        lines.append("")

    if child_pages:
        if not child_namespaces:
            lines.append("===== Pages =====")
            lines.append("")
        for page_id, name in sorted(child_pages, key=lambda x: x[1].lower()):
            lines.append(f"  * [[{page_id}|{name}]]")
        lines.append("")

    _write_text_file(file_path, "\n".join(lines).rstrip() + "\n")


def _export_from_api(client: BookStackClient, options: ExportOptions, checkpoint: MigrationCheckpoint) -> None:
    pages_root = options.output / "pages"
    media_root = options.output / "media"
    pages_root.mkdir(parents=True, exist_ok=True)
    media_root.mkdir(parents=True, exist_ok=True)

    exported_ids = {p.get("id") for p in (checkpoint.data.get("pages") or []) if isinstance(p, dict)}
    book_cache: Dict[int, Dict[str, Any]] = {}
    chapter_cache: Dict[int, Dict[str, Any]] = {}

    # Shelf mapping (book_id -> list of shelf dicts)
    shelves: Dict[int, Dict[str, Any]] = {}
    book_to_shelves: Dict[int, List[Dict[str, Any]]] = {}
    try:
        for shelf in client.iter_shelves(count=50):
            shelf_id = shelf.get("id")
            if shelf_id is None:
                continue
            shelves[int(shelf_id)] = shelf
            # Pull books for this shelf
            page_num = 1
            while True:
                payload = client.list_shelf_books(int(shelf_id), page=page_num, count=50)
                data = payload.get("data", []) or []
                for b in data:
                    if not isinstance(b, dict) or b.get("id") is None:
                        continue
                    book_id = int(b.get("id"))
                    book_to_shelves.setdefault(book_id, []).append(shelf)
                if not payload.get("next_page_url") or not data:
                    break
                page_num += 1
    except Exception:
        # Shelf endpoints may be disabled/limited; export still works.
        book_to_shelves = {}

    # Track hierarchy for index generation.
    shelf_nodes: Dict[str, Dict[str, Any]] = {}
    book_nodes: Dict[Tuple[str, str], Dict[str, Any]] = {}
    chapter_nodes: Dict[Tuple[str, str, str], Dict[str, Any]] = {}

    def get_book(book_id: int) -> Dict[str, Any]:
        if book_id not in book_cache:
            book_cache[book_id] = client.get_book(book_id)
        return book_cache[book_id]

    def get_chapter(chapter_id: int) -> Dict[str, Any]:
        if chapter_id not in chapter_cache:
            chapter_cache[chapter_id] = client.get_chapter(chapter_id)
        return chapter_cache[chapter_id]

    exported_count = 0
    skipped_count = 0
    for page_ref in client.iter_pages(count=50):
        if not page_ref.id:
            continue
        if page_ref.id in exported_ids:
            skipped_count += 1
            continue

        # Determine namespace path: shelf > book > chapter
        parts: List[str] = []
        shelf_slug = "_no_shelf"
        shelf_name = "No Shelf"

        if page_ref.book_id:
            shelves_for_book = book_to_shelves.get(int(page_ref.book_id), [])
            if shelves_for_book:
                s = shelves_for_book[0]
                shelf_slug = _sanitize_namespace_part(str(s.get("slug") or s.get("name") or ""), f"shelf_{s.get('id')}")
                shelf_name = str(s.get("name") or shelf_slug)

        parts.append(shelf_slug)
        shelf_nodes.setdefault(shelf_slug, {"name": shelf_name, "books": {}})

        if page_ref.book_id:
            book = get_book(int(page_ref.book_id))
            book_slug = _sanitize_namespace_part(
                str(book.get("slug") or book.get("name") or ""),
                f"book_{page_ref.book_id}",
            )
            book_name = str(book.get("name") or book_slug)
            parts.append(book_slug)

            shelf_nodes[shelf_slug]["books"].setdefault(book_slug, book_name)
            book_nodes.setdefault((shelf_slug, book_slug), {"name": book_name, "chapters": {}, "pages": {}})

        if page_ref.chapter_id and page_ref.book_id:
            chapter = get_chapter(int(page_ref.chapter_id))
            chap_slug = _sanitize_namespace_part(
                str(chapter.get("slug") or chapter.get("name") or ""),
                f"chapter_{page_ref.chapter_id}",
            )
            chap_name = str(chapter.get("name") or chap_slug)
            parts.append(chap_slug)

            book_nodes[(shelf_slug, parts[1])]["chapters"].setdefault(chap_slug, chap_name)
            chapter_nodes.setdefault((shelf_slug, parts[1], chap_slug), {"name": chap_name, "pages": {}})

        if not page_ref.book_id:
            # Truly orphaned
            parts = ["_orphaned"]

        page_slug = _sanitize_namespace_part(str(page_ref.slug or page_ref.name or ""), f"page_{page_ref.id}")
        page_dir = pages_root.joinpath(*parts)
        page_path = page_dir / f"{page_slug}.txt"

        logger.info(f"Exporting page {page_ref.id}: {page_ref.name} -> {page_path}")
        raw_md = client.export_page_markdown(int(page_ref.id))

        # Best-effort: Download uploaded assets referenced in content.
        media_url_to_id: Dict[str, str] = {}
        try:
            import re

            urls = set(re.findall(r"https?://[^\s\)\]\"']+", raw_md))
            for url in list(urls)[:200]:
                if "/uploads/" not in url:
                    continue
                filename = url.split("/")[-1].split("?")[0]
                if not filename:
                    continue
                media_rel_dir = media_root.joinpath(*parts)
                media_rel_dir.mkdir(parents=True, exist_ok=True)
                target = media_rel_dir / filename
                if not target.exists():
                    resp = client.session.get(url, stream=True, timeout=client.timeout)
                    if resp.status_code >= 400:
                        continue
                    with open(target, "wb") as f:
                        for chunk in resp.iter_content(chunk_size=1024 * 128):
                            if chunk:
                                f.write(chunk)

                media_id = ":" + _namespace_id_from_parts(parts) + ":" + filename
                media_url_to_id[url] = media_id
        except Exception:
            media_url_to_id = {}

        doc = _convert_markdown_to_dokuwiki(raw_md, str(page_ref.name or page_slug))
        for url, media_id in media_url_to_id.items():
            doc = doc.replace(url, media_id)
        _write_text_file(page_path, doc)

        # Record in hierarchy for indexes.
        if parts and parts[0] == "_orphaned":
            pass
        elif len(parts) >= 2:
            shelf_slug2, book_slug2 = parts[0], parts[1]
            page_name = str(page_ref.name or page_slug)
            if len(parts) >= 3:
                chap_slug2 = parts[2]
                chapter_nodes[(shelf_slug2, book_slug2, chap_slug2)]["pages"].setdefault(page_slug, page_name)
            else:
                book_nodes[(shelf_slug2, book_slug2)]["pages"].setdefault(page_slug, page_name)

        checkpoint.add_page(int(page_ref.id), str(page_ref.name or page_slug))
        exported_count += 1
        if exported_count % 25 == 0:
            print(f"   📝 Exported {exported_count} pages...")

    print(f"\n✅ Exported {exported_count} pages (skipped {skipped_count} already done)")
    print(f"✅ Output written under: {options.output}")

    # Write indexes after export.
    for shelf_slug2, shelf_info in shelf_nodes.items():
        shelf_dir = pages_root / shelf_slug2
        shelf_title = str(shelf_info.get("name") or shelf_slug2)
        books = shelf_info.get("books") or {}
        ns_children = [(_namespace_id_from_parts([shelf_slug2, bslug]), bname) for bslug, bname in books.items()]
        _write_namespace_index(
            file_path=shelf_dir / "start.txt",
            title=shelf_title,
            child_namespaces=ns_children,
            child_pages=[],
        )

    for (shelf_slug2, book_slug2), info in book_nodes.items():
        book_dir = pages_root / shelf_slug2 / book_slug2
        book_title = str(info.get("name") or book_slug2)
        chapters = info.get("chapters") or {}
        pages = info.get("pages") or {}
        ns_children = [(_namespace_id_from_parts([shelf_slug2, book_slug2, cslug]), cname) for cslug, cname in chapters.items()]
        page_children = [(_page_id_from_parts([shelf_slug2, book_slug2], pslug), pname) for pslug, pname in pages.items()]
        _write_namespace_index(
            file_path=book_dir / "start.txt",
            title=book_title,
            child_namespaces=ns_children,
            child_pages=page_children,
        )

    for (shelf_slug2, book_slug2, chap_slug2), info in chapter_nodes.items():
        chap_dir = pages_root / shelf_slug2 / book_slug2 / chap_slug2
        chap_title = str(info.get("name") or chap_slug2)
        pages = info.get("pages") or {}
        page_children = [(_page_id_from_parts([shelf_slug2, book_slug2, chap_slug2], pslug), pname) for pslug, pname in pages.items()]
        _write_namespace_index(
            file_path=chap_dir / "start.txt",
            title=chap_title,
            child_namespaces=[],
            child_pages=page_children,
        )


def _db_cursor_dict(driver_module: object, conn: object):
    # mysql.connector supports dictionary=True, mariadb supports dictionary=True as well.
    try:
        return conn.cursor(dictionary=True)
    except TypeError:
        return conn.cursor()


def _export_from_database(driver_module: object, options: ExportOptions, checkpoint: MigrationCheckpoint) -> None:
    pages_root = options.output / "pages"
    pages_root.mkdir(parents=True, exist_ok=True)

    if driver_module.__name__.startswith("mysql"):
        conn = driver_module.connect(
            host=options.host,
            user=options.user,
            password=options.password,
            database=options.db,
            port=options.port,
        )
    else:
        conn = driver_module.connect(
            host=options.host,
            user=options.user,
            password=options.password,
            database=options.db,
            port=options.port,
        )

    cursor = _db_cursor_dict(driver_module, conn)

    def fetchall(query: str, params: Tuple[Any, ...] = ()) -> List[Dict[str, Any]]:
        cursor.execute(query, params)
        rows = cursor.fetchall()
        if isinstance(rows, list) and rows and not isinstance(rows[0], dict):
            # Convert tuples to dict via description
            cols = [d[0] for d in cursor.description]
            return [dict(zip(cols, r)) for r in rows]
        return rows or []

    def table_columns(table: str) -> List[str]:
        cols = fetchall(f"SHOW COLUMNS FROM `{table}`")
        return [c.get("Field") for c in cols if isinstance(c, dict) and c.get("Field")]

    # Determine schema style
    tables = fetchall("SHOW TABLES")
    table_names = set()
    for row in tables:
        if isinstance(row, dict):
            table_names.update(row.values())

    use_entities = "entities" in table_names and "entity_page_data" in table_names

    # Shelf mapping (legacy tables)
    shelf_by_book: Dict[int, Tuple[str, str]] = {}
    if "bookshelves" in table_names and "bookshelf_books" in table_names:
        try:
            shelves = fetchall("SELECT id, name, slug FROM `bookshelves`")
            shelves_by_id = {int(r["id"]): r for r in shelves if r.get("id") is not None}
            pivots = fetchall("SELECT bookshelf_id, book_id FROM `bookshelf_books`")
            # Pick first shelf per book.
            for r in pivots:
                if r.get("book_id") is None or r.get("bookshelf_id") is None:
                    continue
                book_id = int(r.get("book_id"))
                shelf_id = int(r.get("bookshelf_id"))
                if book_id in shelf_by_book:
                    continue
                shelf = shelves_by_id.get(shelf_id) or {}
                sslug = _sanitize_namespace_part(str(shelf.get("slug") or shelf.get("name") or ""), f"shelf_{shelf_id}")
                sname = str(shelf.get("name") or sslug)
                shelf_by_book[book_id] = (sslug, sname)
        except Exception:
            shelf_by_book = {}

    books: Dict[int, Dict[str, Any]] = {}
    chapters: Dict[int, Dict[str, Any]] = {}
    shelf_nodes: Dict[str, Dict[str, Any]] = {}
    book_nodes: Dict[Tuple[str, str], Dict[str, Any]] = {}
    chapter_nodes: Dict[Tuple[str, str, str], Dict[str, Any]] = {}

    if use_entities:
        entities = fetchall(
            "SELECT * FROM entities WHERE deleted_at IS NULL ORDER BY type, book_id, chapter_id, priority"
        )
        page_data_rows = fetchall("SELECT * FROM entity_page_data")
        page_data = {int(r.get("page_id")): r for r in page_data_rows if r.get("page_id") is not None}
        container_rows = fetchall("SELECT * FROM entity_container_data") if "entity_container_data" in table_names else []
        container_data = {int(r.get("entity_id")): (r.get("description") or "") for r in container_rows if r.get("entity_id") is not None}

        for e in entities:
            if e.get("type") != "book":
                continue
            book_id = int(e.get("id"))
            slug = _sanitize_namespace_part(str(e.get("slug") or e.get("name") or ""), f"book_{book_id}")
            name = str(e.get("name") or slug)
            shelf_slug = shelf_by_book.get(book_id, ("_no_shelf", "No Shelf"))[0]
            shelf_name = shelf_by_book.get(book_id, ("_no_shelf", "No Shelf"))[1]
            shelf_nodes.setdefault(shelf_slug, {"name": shelf_name, "books": {}})
            shelf_nodes[shelf_slug]["books"].setdefault(slug, name)
            book_nodes.setdefault((shelf_slug, slug), {"name": name, "chapters": {}, "pages": {}})

            book_dir = pages_root / shelf_slug / slug
            book_dir.mkdir(parents=True, exist_ok=True)
            _ensure_start_page(book_dir, name)
            books[book_id] = {"slug": slug, "name": name, "path": book_dir}

        for e in entities:
            if e.get("type") != "chapter":
                continue
            chap_id = int(e.get("id"))
            book_id = e.get("book_id")
            slug = _sanitize_namespace_part(str(e.get("slug") or e.get("name") or ""), f"chapter_{chap_id}")
            name = str(e.get("name") or slug)
            if book_id and int(book_id) in books:
                chap_dir = books[int(book_id)]["path"] / slug
                shelf_slug = books[int(book_id)]["path"].parts[-2]
                book_slug = books[int(book_id)]["slug"]
                book_nodes[(shelf_slug, book_slug)]["chapters"].setdefault(slug, name)
                chapter_nodes.setdefault((shelf_slug, book_slug, slug), {"name": name, "pages": {}})
            else:
                chap_dir = pages_root / "_orphaned" / slug
            chap_dir.mkdir(parents=True, exist_ok=True)
            _ensure_start_page(chap_dir, name)
            chapters[chap_id] = {"slug": slug, "name": name, "path": chap_dir, "book_id": book_id}

        exported = 0
        exported_ids = {p.get("id") for p in (checkpoint.data.get("pages") or []) if isinstance(p, dict)}
        for e in entities:
            if e.get("type") != "page":
                continue
            page_id = int(e.get("id"))
            if page_id in exported_ids:
                continue
            name = str(e.get("name") or f"page_{page_id}")
            slug = _sanitize_namespace_part(str(e.get("slug") or name), f"page_{page_id}")
            chapter_id = e.get("chapter_id")
            book_id = e.get("book_id")
            if chapter_id and int(chapter_id) in chapters:
                target_dir = chapters[int(chapter_id)]["path"]
                # indexes
                shelf_slug = target_dir.parts[-3]
                book_slug = target_dir.parts[-2]
                chap_slug = target_dir.parts[-1]
                chapter_nodes[(shelf_slug, book_slug, chap_slug)]["pages"].setdefault(slug, name)
            elif book_id and int(book_id) in books:
                target_dir = books[int(book_id)]["path"]
                shelf_slug = target_dir.parts[-2]
                book_slug = target_dir.parts[-1]
                book_nodes[(shelf_slug, book_slug)]["pages"].setdefault(slug, name)
            else:
                target_dir = pages_root / "_orphaned"
                target_dir.mkdir(parents=True, exist_ok=True)

            pdata = page_data.get(page_id, {})
            content = pdata.get("markdown") or pdata.get("text") or pdata.get("html") or ""
            doc = _convert_markdown_to_dokuwiki(str(content), name)
            _write_text_file(target_dir / f"{slug}.txt", doc)
            checkpoint.add_page(page_id, name)
            exported += 1

        print(f"\n✅ Exported {exported} pages from database")

        # Write indexes
        for shelf_slug2, shelf_info in shelf_nodes.items():
            shelf_dir = pages_root / shelf_slug2
            shelf_title = str(shelf_info.get("name") or shelf_slug2)
            books_map = shelf_info.get("books") or {}
            ns_children = [(_namespace_id_from_parts([shelf_slug2, bslug]), bname) for bslug, bname in books_map.items()]
            _write_namespace_index(
                file_path=shelf_dir / "start.txt",
                title=shelf_title,
                child_namespaces=ns_children,
                child_pages=[],
            )

        for (shelf_slug2, book_slug2), info in book_nodes.items():
            book_dir = pages_root / shelf_slug2 / book_slug2
            book_title = str(info.get("name") or book_slug2)
            chapters_map = info.get("chapters") or {}
            pages_map = info.get("pages") or {}
            ns_children = [(_namespace_id_from_parts([shelf_slug2, book_slug2, cslug]), cname) for cslug, cname in chapters_map.items()]
            page_children = [(_page_id_from_parts([shelf_slug2, book_slug2], pslug), pname) for pslug, pname in pages_map.items()]
            _write_namespace_index(
                file_path=book_dir / "start.txt",
                title=book_title,
                child_namespaces=ns_children,
                child_pages=page_children,
            )

        for (shelf_slug2, book_slug2, chap_slug2), info in chapter_nodes.items():
            chap_dir = pages_root / shelf_slug2 / book_slug2 / chap_slug2
            chap_title = str(info.get("name") or chap_slug2)
            pages_map = info.get("pages") or {}
            page_children = [(_page_id_from_parts([shelf_slug2, book_slug2, chap_slug2], pslug), pname) for pslug, pname in pages_map.items()]
            _write_namespace_index(
                file_path=chap_dir / "start.txt",
                title=chap_title,
                child_namespaces=[],
                child_pages=page_children,
            )

    else:
        # Legacy BookStack schema
        if "books" in table_names:
            cols = set(table_columns("books"))
            select_cols = [c for c in ("id", "name", "slug", "description", "description_html") if c in cols]
            rows = fetchall(f"SELECT {', '.join('`'+c+'`' for c in select_cols)} FROM `books`")
            for r in rows:
                book_id = int(r.get("id"))
                slug = _sanitize_namespace_part(str(r.get("slug") or r.get("name") or ""), f"book_{book_id}")
                name = str(r.get("name") or slug)
                shelf_slug, shelf_name = shelf_by_book.get(book_id, ("_no_shelf", "No Shelf"))
                shelf_nodes.setdefault(shelf_slug, {"name": shelf_name, "books": {}})
                shelf_nodes[shelf_slug]["books"].setdefault(slug, name)
                book_nodes.setdefault((shelf_slug, slug), {"name": name, "chapters": {}, "pages": {}})

                book_dir = pages_root / shelf_slug / slug
                book_dir.mkdir(parents=True, exist_ok=True)
                _ensure_start_page(book_dir, name)
                books[book_id] = {"slug": slug, "name": name, "path": book_dir}

        if "chapters" in table_names:
            cols = set(table_columns("chapters"))
            select_cols = [c for c in ("id", "book_id", "name", "slug", "description", "description_html") if c in cols]
            rows = fetchall(f"SELECT {', '.join('`'+c+'`' for c in select_cols)} FROM `chapters`")
            for r in rows:
                chap_id = int(r.get("id"))
                book_id = r.get("book_id")
                slug = _sanitize_namespace_part(str(r.get("slug") or r.get("name") or ""), f"chapter_{chap_id}")
                name = str(r.get("name") or slug)
                if book_id and int(book_id) in books:
                    chap_dir = books[int(book_id)]["path"] / slug
                    shelf_slug2 = books[int(book_id)]["path"].parts[-2]
                    book_slug2 = books[int(book_id)]["slug"]
                    book_nodes[(shelf_slug2, book_slug2)]["chapters"].setdefault(slug, name)
                    chapter_nodes.setdefault((shelf_slug2, book_slug2, slug), {"name": name, "pages": {}})
                else:
                    chap_dir = pages_root / "_orphaned" / slug
                chap_dir.mkdir(parents=True, exist_ok=True)
                _ensure_start_page(chap_dir, name)
                chapters[chap_id] = {"slug": slug, "name": name, "path": chap_dir, "book_id": book_id}

        exported = 0
        if "pages" in table_names:
            cols = set(table_columns("pages"))
            select_cols = [c for c in ("id", "book_id", "chapter_id", "name", "slug", "markdown", "text", "html") if c in cols]
            rows = fetchall(f"SELECT {', '.join('`'+c+'`' for c in select_cols)} FROM `pages`")
            exported_ids = {p.get("id") for p in (checkpoint.data.get("pages") or []) if isinstance(p, dict)}
            for r in rows:
                page_id = int(r.get("id"))
                if page_id in exported_ids:
                    continue
                name = str(r.get("name") or f"page_{page_id}")
                slug = _sanitize_namespace_part(str(r.get("slug") or name), f"page_{page_id}")
                chap_id = r.get("chapter_id")
                book_id = r.get("book_id")
                if chap_id and int(chap_id) in chapters:
                    target_dir = chapters[int(chap_id)]["path"]
                    shelf_slug2 = target_dir.parts[-3]
                    book_slug2 = target_dir.parts[-2]
                    chap_slug2 = target_dir.parts[-1]
                    chapter_nodes[(shelf_slug2, book_slug2, chap_slug2)]["pages"].setdefault(slug, name)
                elif book_id and int(book_id) in books:
                    target_dir = books[int(book_id)]["path"]
                    shelf_slug2 = target_dir.parts[-2]
                    book_slug2 = target_dir.parts[-1]
                    book_nodes[(shelf_slug2, book_slug2)]["pages"].setdefault(slug, name)
                else:
                    target_dir = pages_root / "_orphaned"
                    target_dir.mkdir(parents=True, exist_ok=True)
                content = r.get("markdown") or r.get("text") or r.get("html") or ""
                doc = _convert_markdown_to_dokuwiki(str(content), name)
                _write_text_file(target_dir / f"{slug}.txt", doc)
                checkpoint.add_page(page_id, name)
                exported += 1

        print(f"\n✅ Exported {exported} pages from database")

        # Write indexes
        for shelf_slug2, shelf_info in shelf_nodes.items():
            shelf_dir = pages_root / shelf_slug2
            shelf_title = str(shelf_info.get("name") or shelf_slug2)
            books_map = shelf_info.get("books") or {}
            ns_children = [(_namespace_id_from_parts([shelf_slug2, bslug]), bname) for bslug, bname in books_map.items()]
            _write_namespace_index(
                file_path=shelf_dir / "start.txt",
                title=shelf_title,
                child_namespaces=ns_children,
                child_pages=[],
            )

        for (shelf_slug2, book_slug2), info in book_nodes.items():
            book_dir = pages_root / shelf_slug2 / book_slug2
            book_title = str(info.get("name") or book_slug2)
            chapters_map = info.get("chapters") or {}
            pages_map = info.get("pages") or {}
            ns_children = [(_namespace_id_from_parts([shelf_slug2, book_slug2, cslug]), cname) for cslug, cname in chapters_map.items()]
            page_children = [(_page_id_from_parts([shelf_slug2, book_slug2], pslug), pname) for pslug, pname in pages_map.items()]
            _write_namespace_index(
                file_path=book_dir / "start.txt",
                title=book_title,
                child_namespaces=ns_children,
                child_pages=page_children,
            )

        for (shelf_slug2, book_slug2, chap_slug2), info in chapter_nodes.items():
            chap_dir = pages_root / shelf_slug2 / book_slug2 / chap_slug2
            chap_title = str(info.get("name") or chap_slug2)
            pages_map = info.get("pages") or {}
            page_children = [(_page_id_from_parts([shelf_slug2, book_slug2, chap_slug2], pslug), pname) for pslug, pname in pages_map.items()]
            _write_namespace_index(
                file_path=chap_dir / "start.txt",
                title=chap_title,
                child_namespaces=[],
                child_pages=page_children,
            )

    try:
        conn.close()
    except Exception:
        pass


def cmd_detect() -> int:
    """Detect DokuWiki installations."""
    logger.info("Running detect command")
    installs = detect_dokuwiki()

    if not installs:
        logger.error("No DokuWiki installations found")
        print("❌ No DokuWiki installations found")
        return 1

    print(f"\n✅ Found {len(installs)} DokuWiki installation(s):\n")
    logger.info(f"Found {len(installs)} DokuWiki installation(s)")

    for i, inst in enumerate(installs, 1):
        access = "✅ writable" if inst.writable else "❌ read-only"
        print(f"{i}. {inst.path}")
        print(f"   Type: {inst.install_type}")
        print(f"   Pages: {inst.pages_dir}")
        print(f"   Media: {inst.media_dir}")
        print(f"   Access: {access}\n")
        logger.info(f"   [{i}] {inst.path} ({inst.install_type}, writable={inst.writable})")

    return 0


def cmd_export(options: ExportOptions) -> int:
    """Export BookStack to DokuWiki using best available source."""
    logger.info(f"Running export command: db={options.db}, driver={options.driver}")
    print("📤 Export BookStack to DokuWiki")

    # Initialize checkpoint for resumable migrations
    checkpoint = MigrationCheckpoint(options.output)
    importer: Optional[SqlDumpImporter] = None
    
    try:
        # Test API availability
        api_available = False
        client = None
        try:
            timeout = int(os.environ.get("BOOKSTACK_TIMEOUT", str(DEFAULT_TIMEOUT)))
            client = BookStackClient.from_env(timeout=timeout)
            api_available = client.test_connection()
            logger.info("✅ API connection successful")
        except Exception as e:
            logger.warning(f"API not available: {e}")

        # Test DB availability only if we have DB connection details.
        db_available = bool(options.db and options.user and options.password)
        driver_name = None
        if db_available:
            try:
                driver, driver_name = get_db_driver(preferred=options.driver)
                db_available = driver is not None
                if db_available:
                    logger.info(f"✅ Database driver available: {driver_name}")
            except Exception as e:
                db_available = False
                logger.warning(f"Database driver not available: {e}")

        # Large-instance heuristic: if large and DB/SQL available, force DB for performance.
        large_pages_threshold = int(os.environ.get("BOOKSTACK_LARGE_PAGES_THRESHOLD", "5000"))
        large_sql_mb_threshold = int(os.environ.get("BOOKSTACK_LARGE_SQL_MB_THRESHOLD", "500"))
        large_instance = is_large_instance(
            client=client if api_available else None,
            sql_file=options.sql_file,
            large_pages_threshold=large_pages_threshold,
            large_sql_mb_threshold=large_sql_mb_threshold,
        )

        # Select best source (used only for ordering; we will still fall back).
        selector = DataSourceSelector(
            db_available,
            api_available,
            prefer_api=options.prefer_api,
            large_instance=large_instance,
        )
        source = selector.get_best_source()

        if source == "none":
            logger.error("No data source available (no DB driver and no API)")
            print("❌ No data source available. Tried DB and API.")
            return 1

        print(f"✅ Using data source: {source}")
        logger.info(f"Selected data source: {source}")

        if source == "database":
            if not (options.db and options.user and options.password):
                raise BookStackError("Database selected but missing DB connection details")
            if driver_name:
                print(f"✅ Using database driver: {driver_name}")
            print(
                f"Database: {options.db}@{options.host}:{options.port} as {options.user}\n"
                f"Output: {options.output}"
            )
            logger.info(f"Database connection: {options.db}@{options.host}:{options.port}")

        if source == "api" and client:
            print(f"✅ Using BookStack REST API at: {client.base_url}")
            logger.info(f"API base URL: {client.base_url}")
            try:
                # Try to fetch OpenAPI spec for reference
                spec = load_spec_from_env()
                paths_count = len(spec.get("paths", {}))
                print(f"✅ API spec loaded (paths: {paths_count})")
                logger.info(f"API spec loaded with {paths_count} paths")

                # List pages from API as example
                pages_resp = client.list_pages(count=5)
                pages_count = len(pages_resp.get("data", []))
                print(f"✅ Sample pages retrieved: {pages_count}")
                logger.info(f"Sample API response: {pages_count} pages")
            except Exception as e:
                logger.warning(f"Could not load full API spec: {e}")

        print(f"✅ Output directory: {options.output}")
        options.output.mkdir(parents=True, exist_ok=True)
        logger.info(f"Created output directory: {options.output}")
        
        # Check for previous checkpoint
        if checkpoint.data.get("pages"):
            print(f"\n📋 Resuming previous migration: {len(checkpoint.data['pages'])} pages already exported")
            logger.info(f"Resuming migration with {len(checkpoint.data['pages'])} pages")

        # Try strategies in order, with fallbacks: API -> DB -> SQL dump (DB via temp container)
        last_error: Optional[Exception] = None
        strategies: List[str] = []

        if api_available and client is not None:
            strategies.append("api")
        if db_available:
            strategies.append("database")
        if options.sql_file is not None:
            strategies.append("sql")

        # If the selector says database is best (large instance), prioritize DB but still allow API fallback.
        if source == "database" and "database" in strategies:
            strategies = ["database"] + [s for s in strategies if s != "database"]

        for strat in strategies:
            try:
                if strat == "api":
                    assert client is not None
                    _export_from_api(client, options, checkpoint)
                    last_error = None
                    break

                if strat == "database":
                    driver, _ = get_db_driver(preferred=options.driver)
                    if driver is None:
                        raise BookStackError("No database driver available")
                    _export_from_database(driver, options, checkpoint)
                    last_error = None
                    break

                if strat == "sql":
                    importer = SqlDumpImporter(options.sql_file, database=options.sql_db)  # type: ignore[arg-type]
                    host, port, db, user, password = importer.start_and_import()
                    options.host = host
                    options.port = port
                    options.db = db
                    options.user = user
                    options.password = password
                    driver, _ = get_db_driver(preferred=options.driver)
                    if driver is None:
                        raise BookStackError("No database driver available for SQL dump import")
                    _export_from_database(driver, options, checkpoint)
                    last_error = None
                    break

            except Exception as exc:
                last_error = exc
                logger.warning(f"Export strategy '{strat}' failed: {exc}")
                continue

        if last_error is not None:
            raise last_error

        checkpoint.save()
        return 0
        
    except KeyboardInterrupt:
        print("\n⚠️  Migration interrupted by user")
        checkpoint.mark_incomplete()
        logger.warning("Migration interrupted")
        return 130  # Standard interrupt exit code
    except Exception as e:
        print(f"\n❌ Export error: {e}")
        checkpoint.mark_incomplete()
        logger.error(f"Export error: {e}", exc_info=True)
        return 1
    finally:
        if importer is not None:
            importer.cleanup()


def cmd_version() -> int:
    """Show version."""
    print(f"BookStack Migration Tool v{__version__}")
    logger.info(f"Version: {__version__}")
    return 0


def get_db_driver(preferred: Optional[str] = None) -> Tuple[Optional[object], Optional[str]]:
    """Select a DB driver. Preference order:
    1) preferred argument (if provided)
    2) DB_DRIVER env (mysql|mariadb)
    3) mysql-connector-python
    4) mariadb
    Returns: (module, name) or (None, None) on failure.
    """
    env_driver = os.environ.get("DB_DRIVER", "").strip().lower()
    candidates: List[str] = []

    if preferred and preferred in {"mysql", "mariadb"}:
        candidates.append(preferred)
    if env_driver in {"mysql", "mariadb"} and env_driver not in candidates:
        candidates.append(env_driver)

    candidates.extend([d for d in ("mysql", "mariadb") if d not in candidates])

    for driver in candidates:
        mod = load_driver(driver)
        if mod:
            return mod

    logger.error("No database driver found. Tried mysql-connector and mariadb.")
    print("❌ No database driver found. Tried mysql-connector and mariadb.")
    print("   Attempted auto-install; if it failed, install manually:")
    print("   pip install mysql-connector-python")
    print("   pip install mariadb")
    print("Or set DB_DRIVER=mysql|mariadb to choose explicitly.")
    return None, None


def load_driver(driver: str) -> Optional[Tuple[object, str]]:
    """Try to import a driver; auto-install if missing.

    Returns (module, name) or None on failure.
    """
    mapping = {
        "mysql": ("mysql.connector", "mysql-connector-python"),
        "mariadb": ("mariadb", "mariadb"),
    }
    if driver not in mapping:
        return None

    module_name, package = mapping[driver]

    try:
        return importlib.import_module(module_name), driver
    except ImportError:
        pass

    logger.info(f"Installing {package} (driver: {driver})...")
    print(f"ℹ️  Installing {package} (driver: {driver})...")
    result = subprocess.run(
        [sys.executable, "-m", "pip", "install", "--user", package],
        capture_output=True,
        text=True,
    )
    if result.returncode != 0:
        logger.error(f"Failed to install {package}: {result.stderr.strip() or result.stdout.strip()}")
        print(f"❌ Failed to install {package}: {result.stderr.strip() or result.stdout.strip()}")
        return None

    try:
        return importlib.import_module(module_name), driver
    except ImportError as exc:
        logger.error(f"Installed {package} but could not import: {exc}")
        print(f"❌ Installed {package} but could not import: {exc}")
        return None


def cmd_help() -> int:
    """Show help."""
    build_parser().print_help()
    return 0


def main() -> int:
    """Main entry point."""
    parser = build_parser()
    args = parser.parse_args()

    # Check venv only for export runs (avoid breaking help/version/detect and automation).
    if (
        args.command == "export"
        and sys.stdin.isatty()
        and os.environ.get("CI") is None
        and os.environ.get("BOOKSTACK_MIGRATE_SKIP_VENV_CHECK") is None
        and not getattr(args, "justdoit", False)
    ):
        check_venv_and_prompt()

    logger.info(f"Command: {args.command}")

    if args.command == "detect":
        return cmd_detect()

    if args.command == "export":
        export_opts = ExportOptions(
            db=args.db,
            user=args.user,
            password=args.password,
            host=args.host,
            port=args.port,
            output=Path(args.output),
            driver=args.driver,
            prefer_api=getattr(args, "prefer_api", False),
            sql_file=Path(args.sql_file) if getattr(args, "sql_file", None) else None,
            sql_db=getattr(args, "sql_db", "bookstack"),
            justdoit=getattr(args, "justdoit", False),
        )
        return cmd_export(export_opts)

    if args.command == "version":
        return cmd_version()

    if args.command in {"help", None}:
        parser.print_help()
        return 0

    parser.error(f"Unknown command: {args.command}")
    return 1


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        prog="bookstack-migrate",
        description="BookStack → DokuWiki Migration Tool",
    )
    sub = parser.add_subparsers(dest="command")

    sub.add_parser("detect", help="Find DokuWiki installations")

    export = sub.add_parser(
        "export",
        help="Export BookStack content into DokuWiki-compatible format",
    )
    export.add_argument("--db", required=False, help="BookStack database name")
    export.add_argument("--user", required=False, help="Database user")
    export.add_argument("--password", required=False, help="Database password")
    export.add_argument("--host", default="localhost", help="Database host")
    export.add_argument("--port", type=int, default=3306, help="Database port")
    export.add_argument(
        "--driver",
        choices=["mysql", "mariadb"],
        help="Database driver override (default: auto)",
    )
    export.add_argument(
        "--output",
        default="./export",
        help="Output directory for DokuWiki content",
    )
    export.add_argument(
        "--sql-file",
        help="Path to a MySQL/MariaDB .sql dump to import (requires Docker)",
    )
    export.add_argument(
        "--sql-db",
        default="bookstack",
        help="Database name to use when importing --sql-file (default: bookstack)",
    )
    export.add_argument(
        "--prefer-api",
        action="store_true",
        help="Prefer API over database if both available",
    )

    export.add_argument(
        "--justdoit",
        action="store_true",
        help="Best-effort non-interactive mode (skips prompts; tries DB/SQL/API automatically)",
    )

    sub.add_parser("version", help="Show version and exit")
    sub.add_parser("help", help="Show help and exit")

    return parser


if __name__ == "__main__":
    sys.exit(main() or 0)
