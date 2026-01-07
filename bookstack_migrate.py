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
# API CLIENT (formerly bookstack_api.py)
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
        resp = self.session.request(method, url, timeout=self.timeout, **kwargs)
        if resp.status_code >= 400:
            raise BookStackError(
                f"BookStack API error {resp.status_code}",
                status=resp.status_code,
                body=resp.text,
            )
        return resp

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

        # If provided a SQL dump, import into a temp DB container and use that connection.
        if options.sql_file is not None:
            importer = SqlDumpImporter(options.sql_file, database=options.sql_db)
            host, port, db, user, password = importer.start_and_import()
            options.host = host
            options.port = port
            options.db = db
            options.user = user
            options.password = password
            logger.info(f"SQL dump imported; temp DB available at {host}:{port}/{db}")

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

        # Select best source
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

        # TODO: Full export implementation
        logger.info("Export command completed (stub implementation)")
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
