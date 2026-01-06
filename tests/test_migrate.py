"""Tests for bookstack_migrate CLI."""
import subprocess
import sys


def test_help():
    """Test help command."""
    result = subprocess.run(
        [sys.executable, "bookstack_migrate.py", "help"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 0
    assert "BookStack → DokuWiki" in result.stdout


def test_version():
    """Test version command."""
    result = subprocess.run(
        [sys.executable, "bookstack_migrate.py", "version"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 0
    assert "1.0.0" in result.stdout


def test_detect_no_dokuwiki():
    """Test detect command when no DokuWiki is installed."""
    result = subprocess.run(
        [sys.executable, "bookstack_migrate.py", "detect"],
        capture_output=True,
        text=True,
    )
    assert result.returncode == 1
    assert "No DokuWiki" in result.stdout


def test_export_missing_args():
    """Test export command requires arguments."""
    result = subprocess.run(
        [sys.executable, "bookstack_migrate.py", "export"],
        capture_output=True,
        text=True,
    )
    assert result.returncode != 0
    assert "required" in result.stderr or "required" in result.stdout


def test_checkpoint_creation():
    """Test checkpoint system creates and saves state."""
    from bookstack_migrate import MigrationCheckpoint
    import tempfile
    from pathlib import Path
    
    with tempfile.TemporaryDirectory() as tmpdir:
        output_dir = Path(tmpdir)
        checkpoint = MigrationCheckpoint(output_dir)
        
        # Test initial state
        assert checkpoint.data["pages"] == []
        assert "start_time" in checkpoint.data
        
        # Test adding page
        checkpoint.add_page(1, "Test Page")
        assert len(checkpoint.data["pages"]) == 1
        assert checkpoint.data["pages"][0]["id"] == 1
        
        # Test checkpoint file exists
        assert (output_dir / ".migration_checkpoint.json").exists()
        
        # Test loading existing checkpoint
        checkpoint2 = MigrationCheckpoint(output_dir)
        assert len(checkpoint2.data["pages"]) == 1
        assert checkpoint2.data["pages"][0]["name"] == "Test Page"
