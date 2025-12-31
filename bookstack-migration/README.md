# BookStack to DokuWiki Migration (Experimental)

This folder holds a pile of experimental exporters and helpers for moving
BookStack content into DokuWiki-style files. The previous stack of READMEs,
cheat sheets, and staging notes has been removed; this file is the single
source of truth for the toolkit as it stands today.

## Status and cautions
- Not maintained or tested; expect breakage and review every script before use.
- Some helpers try to install packages or restart services. Run only in a
  throwaway environment and take your own backups first.
- You need BookStack database credentials (DB_HOST, DB_DATABASE, DB_USERNAME,
  DB_PASSWORD) and a path to write exported files.

## What's here
- `AUTO_INSTALL_EVERYTHING.sh` — attempts to install/validate Perl, Python,
  Java, Rust, MySQL client, and build toolchain requirements in one go.
- `bookstack_migration.py` — interactive Python exporter that writes logs to
  `migration_logs/`.
- `tools/one_script_to_rule_them_all.pl` — Perl CLI with flags
  (`--diagnose`, `--backup`, `--export`, `--full`, `--db-host`, `--db-name`,
  `--db-user`, `--db-pass`, `--output`, `--backup-dir`, `--dry-run`,
  `--verbose`). If `/etc/mysql/my.cnf` exists, it is read automatically for
  defaults (client group) in addition to the provided flags.
- `help_me_fix_my_mistake.sh` — menu wrapper around install, backup, and export
  flows.
- `AUTO_INSTALL_EVERYTHING.sh` and `scripts/*.sh` — helper scripts for
  dependency install, diagnostics, backups, and migration orchestration. They
  may install system packages or restart MySQL.
- `tools/ExportToDokuWiki.php`, `tools/DokuWikiExporter.java`,
  `tools/bookstack2dokuwiki.c`, `rust/` — alternative prototypes that have not
  been vetted.
- `docker-compose.test.yml`, `test-data/`, `tests/` — scaffolding intended for
  isolated experiments.

## Minimal usage (if you still want to experiment)
1) Work in a disposable environment and make your own database and uploads
   backups first.
2) (Optional but recommended) Run `./AUTO_INSTALL_EVERYTHING.sh` to install
   Perl/Python/Java/Rust tooling, MySQL client bits, and supporting utilities.  
3) Provide DB connection details from `.env` and decide where exports should be
   written.
4) Option A: Python
   - `python3 bookstack_migration.py`
   - Follow prompts, then check `migration_logs/` and the exported directory.
5) Option B: Perl (explicit flags)
   - `perl tools/one_script_to_rule_them_all.pl --full --db-host <host> --db-name <db> --db-user <user> --db-pass <pass> --output ./dokuwiki_export`
   - Add `--dry-run` to inspect actions without writing.
6) Manually review the exported `./dokuwiki_export` tree before copying
   anything into a DokuWiki instance (`data/pages`, `data/media`, etc.).

## Expectations
- No automated tests cover these scripts; validate results by hand.
- Do not run directly against production without backups and an isolated dry
  run.
- If you keep iterating here, add targeted tests and strip out any
  system-changing steps that are not strictly required for export.
