"""Unit tests for the integrated BookStackClient without making network calls."""

from __future__ import annotations

import json
from types import SimpleNamespace

import pytest


class _FakeResponse:
    def __init__(self, status_code: int = 200, text: str = "{}", json_value=None, json_exc: Exception | None = None):
        self.status_code = status_code
        self.text = text
        self._json_value = json_value
        self._json_exc = json_exc

    def json(self):
        if self._json_exc is not None:
            raise self._json_exc
        return self._json_value


def test_build_url_adds_api_prefix():
    from bookstack_migrate import BookStackClient

    client = BookStackClient("https://example.com", "id", "secret")
    assert client._build_url("/pages") == "https://example.com/api/pages"
    assert client._build_url("pages") == "https://example.com/api/pages"


def test_parse_json_invalid_raises_bookstack_error():
    from bookstack_migrate import BookStackClient, BookStackError

    client = BookStackClient("https://example.com", "id", "secret")
    resp = _FakeResponse(
        status_code=200,
        text="not-json",
        json_exc=json.JSONDecodeError("bad", "not-json", 0),
    )

    with pytest.raises(BookStackError) as exc:
        client._parse_json(resp)  # type: ignore[arg-type]

    assert "Invalid JSON" in str(exc.value)


def test_request_http_error_raises_bookstack_error(monkeypatch):
    from bookstack_migrate import BookStackClient, BookStackError

    client = BookStackClient("https://example.com", "id", "secret")

    def fake_request(method, url, timeout=0, **kwargs):
        return _FakeResponse(status_code=500, text="server error")

    monkeypatch.setattr(client.session, "request", fake_request)

    with pytest.raises(BookStackError) as exc:
        client._request("GET", "/")

    assert "status=500" in str(exc.value)


def test_iter_pages_paginates_and_stops(monkeypatch):
    from bookstack_migrate import BookStackClient

    client = BookStackClient("https://example.com", "id", "secret")

    calls = {"n": 0}

    def fake_list_pages(page=1, count=50):
        calls["n"] += 1
        if calls["n"] == 1:
            return {
                "data": [
                    {"id": 1, "name": "A", "slug": "a", "book_id": 10, "chapter_id": None},
                    {"id": 2, "name": "B", "slug": "b", "book_id": 10, "chapter_id": 20},
                ],
                "next_page_url": "https://example.com/api/pages?page=2",
            }
        return {"data": [], "next_page_url": None}

    monkeypatch.setattr(client, "list_pages", fake_list_pages)

    pages = list(client.iter_pages(count=2))
    assert [p.id for p in pages] == [1, 2]
