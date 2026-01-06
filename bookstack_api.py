"""
Lightweight BookStack REST API helper.

Uses API tokens (id + secret) via the standard Authorization header:
    Authorization: Token <token_id>:<token_secret>

Minimal convenience methods for listing books/pages and fetching single items.
"""
from __future__ import annotations

import json
import os
from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Iterable, Optional

import requests


API_PREFIX = "/api"
DEFAULT_TIMEOUT = 15
DEFAULT_SPEC_CACHE = Path.home() / ".cache" / "bookstack" / "openapi.json"


class BookStackError(Exception):
    """Raised when the BookStack API returns an error response."""

    def __init__(self, message: str, status: Optional[int] = None, body: Optional[str] = None):
        super().__init__(message)
        self.status = status
        self.body = body

    def __str__(self) -> str:  # pragma: no cover - trivial
        suffix = f" (status={self.status})" if self.status is not None else ""
        return f"{super().__str__()}{suffix}"


@dataclass
class PageRef:
    id: int
    name: str
    slug: str
    book_id: Optional[int] = None
    chapter_id: Optional[int] = None


class BookStackClient:
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

    def list_books(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/books", params={"page": page, "count": count})

    def list_pages(self, page: int = 1, count: int = 50) -> Dict[str, Any]:
        return self._get("/pages", params={"page": page, "count": count})

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
        except json.JSONDecodeError as exc:  # pragma: no cover - network dependent
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


# ---------- Environment & OpenAPI helpers ----------


@dataclass
class EnvConfig:
    base_url: str
    token_id: str
    token_secret: str
    spec_url: Optional[str] = None
    spec_cache: Path = DEFAULT_SPEC_CACHE


def read_env_config() -> EnvConfig:
    """Read config from environment. Does not prompt.

    Expected env vars:
      BOOKSTACK_BASE_URL (or BOOKSTACK_URL) – defaults to http://localhost:8000
      BOOKSTACK_TOKEN_ID
      BOOKSTACK_TOKEN_SECRET
      BOOKSTACK_SPEC_URL (optional override for OpenAPI JSON)
      BOOKSTACK_SPEC_CACHE (optional cache path)
    """

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
        except Exception as exc:  # pragma: no cover - network dependent
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
