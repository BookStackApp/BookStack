"""Tests for bookstack_migrate CLI."""
import subprocess
import sys


def test_help():
    """Test help command."""
    result = subprocess.run(
        [sys.executable, "bookstack-migrate", "help"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 0
    assert "BookStack → DokuWiki" in result.stdout


def test_version():
    """Test version command."""
    result = subprocess.run(
        [sys.executable, "bookstack-migrate", "version"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 0
    assert "1.0.0" in result.stdout


def test_detect_no_dokuwiki():
    """Test detect command when no DokuWiki is installed."""
    result = subprocess.run(
        [sys.executable, "bookstack-migrate", "detect"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 1
    assert "No DokuWiki" in result.stdout


def test_export_missing_args():
    """Test export command requires arguments."""
    result = subprocess.run(
        [sys.executable, "bookstack-migrate", "export"],
        capture_output=True,
        text=True,
    )
    assert result.returncode != 0
    assert "required" in result.stderr or "required" in result.stdout
