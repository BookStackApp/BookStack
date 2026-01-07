"""Logic-focused unit tests to keep coverage reasonable in the monolithic module."""

from __future__ import annotations

from pathlib import Path
from unittest import mock

import pytest


def test_data_source_selector_scenarios():
    from bookstack_migrate import DataSourceSelector

    assert DataSourceSelector(db_available=True, api_available=True, prefer_api=False).get_best_source() == "database"
    assert DataSourceSelector(db_available=True, api_available=True, prefer_api=True).get_best_source() == "api"
    assert DataSourceSelector(db_available=False, api_available=True, prefer_api=False).get_best_source() == "api"
    assert DataSourceSelector(db_available=True, api_available=False, prefer_api=False).get_best_source() == "database"
    assert DataSourceSelector(db_available=False, api_available=False, prefer_api=False).get_best_source() == "none"


def test_sql_dump_requires_docker():
    from bookstack_migrate import SqlDumpImporter, SqlDumpImportError

    with mock.patch("bookstack_migrate.shutil.which", return_value=None):
        imp = SqlDumpImporter(Path("/tmp/does-not-matter.sql"))
        with pytest.raises(SqlDumpImportError):
            imp.start_and_import()


def test_checkpoint_mark_incomplete_creates_archive(tmp_path: Path):
    from bookstack_migrate import MigrationCheckpoint

    output_dir = tmp_path / "export"
    output_dir.mkdir(parents=True)
    (output_dir / "dummy.txt").write_text("hello")

    checkpoint = MigrationCheckpoint(output_dir)
    checkpoint.add_page(123, "Example")

    fake_home = tmp_path / "home"
    (fake_home / "Downloads").mkdir(parents=True)

    with mock.patch("bookstack_migrate.Path.home", return_value=fake_home):
        archive = checkpoint.mark_incomplete()

    assert archive is not None
    assert archive.endswith("_bookstack_migrate_incomplete.tar.gz")
    assert Path(archive).exists()
