"""
Tests for BookStack migration tool
"""
import subprocess
import sys

def test_detect_command():
    """Test detect command runs"""
    result = subprocess.run(
        [sys.executable, 'bookstack-migrate', 'detect'],
        capture_output=True,
        text=True
    )
    assert result.returncode in [0, 1]  # 0=found, 1=not found

def test_help_command():
    """Test help command"""
    result = subprocess.run(
        [sys.executable, 'bookstack-migrate', 'help'],
        capture_output=True,
        text=True
    )
    assert result.returncode == 0
    assert 'Usage' in result.stdout or 'detect' in result.stdout

def test_version_command():
    """Test version command"""
    result = subprocess.run(
        [sys.executable, 'bookstack-migrate', 'version'],
        capture_output=True,
        text=True
    )
    assert result.returncode == 0
    assert '1.0.0' in result.stdout
