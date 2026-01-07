"""Tests for API/config pieces in the consolidated module."""
import pytest

from bookstack_migrate import EnvConfig, PageRef, BookStackError, read_env_config


def test_page_ref():
    """Test PageRef dataclass."""
    page = PageRef(id=1, name="Test", slug="test")
    assert page.id == 1
    assert page.name == "Test"
    assert page.slug == "test"
    assert page.book_id is None


def test_bookstack_error():
    """Test BookStackError exception."""
    err = BookStackError("Test error", status=404)
    assert str(err) == "Test error (status=404)"


def test_env_config_missing_token():
    """Test env config raises if token is missing."""
    import os
    
    # Save current env
    old_id = os.environ.pop("BOOKSTACK_TOKEN_ID", None)
    old_secret = os.environ.pop("BOOKSTACK_TOKEN_SECRET", None)
    old_api_id = os.environ.pop("BOOKSTACK_API_TOKEN_ID", None)
    old_api_secret = os.environ.pop("BOOKSTACK_API_TOKEN_SECRET", None)
    
    try:
        with pytest.raises(ValueError, match="BOOKSTACK_TOKEN"):
            read_env_config()
    finally:
        # Restore env
        if old_id:
            os.environ["BOOKSTACK_TOKEN_ID"] = old_id
        if old_secret:
            os.environ["BOOKSTACK_TOKEN_SECRET"] = old_secret
        if old_api_id:
            os.environ["BOOKSTACK_API_TOKEN_ID"] = old_api_id
        if old_api_secret:
            os.environ["BOOKSTACK_API_TOKEN_SECRET"] = old_api_secret
