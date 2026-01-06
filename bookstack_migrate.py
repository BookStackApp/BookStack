#!/usr/bin/env python3
"""
BookStack → DokuWiki Migration Tool
Module entry point for packaging & console script.
"""

import argparse
import importlib
import os
import subprocess
import sys
from dataclasses import dataclass
from pathlib import Path
from typing import List, Optional, Tuple

from bookstack_api import BookStackError, load_spec_from_env

__version__ = "1.0.0"


@dataclass
class DokuWikiInstall:
    path: Path
    pages_dir: Path
    media_dir: Path
    install_type: str  # apt, manual, docker, custom
    writable: bool


@dataclass
class ExportOptions:
    db: str
    user: str
    password: str
    host: str = "localhost"
    port: int = 3306
    output: Path = Path("./export")
    driver: Optional[str] = None


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
    installs = detect_dokuwiki()

    if not installs:
        print("❌ No DokuWiki installations found")
        return 1

    print(f"\n✅ Found {len(installs)} DokuWiki installation(s):\n")

    for i, inst in enumerate(installs, 1):
        access = "✅ writable" if inst.writable else "❌ read-only"
        print(f"{i}. {inst.path}")
        print(f"   Type: {inst.install_type}")
        print(f"   Pages: {inst.pages_dir}")
        print(f"   Media: {inst.media_dir}")
        print(f"   Access: {access}\n")

    return 0


def cmd_export(options: ExportOptions) -> int:
    """Export BookStack to DokuWiki."""
    print("📤 Export BookStack to DokuWiki")

    driver, driver_name = get_db_driver(preferred=options.driver)
    if not driver:
        return 1

    print(f"✅ Using database driver: {driver_name}")
    print(
        f"Database: {options.db}@{options.host}:{options.port} as {options.user}\n"
        f"Output: {options.output}"
    )

    # Optional: verify API accessibility & cache spec for downstream steps
    try:
        spec = load_spec_from_env()
        paths_count = len(spec.get("paths", {}))
        print(f"✅ API spec loaded (paths: {paths_count})")
    except BookStackError as exc:
        print(f"⚠️  API spec not available: {exc}")

    # TODO: add full export implementation using `driver`
    return 0


def cmd_version() -> int:
    """Show version."""
    print(f"BookStack Migration Tool v{__version__}")
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

    print(f"ℹ️  Installing {package} (driver: {driver})...")
    result = subprocess.run(
        [sys.executable, "-m", "pip", "install", "--user", package],
        capture_output=True,
        text=True,
    )
    if result.returncode != 0:
        print(f"❌ Failed to install {package}: {result.stderr.strip() or result.stdout.strip()}")
        return None

    try:
        return importlib.import_module(module_name), driver
    except ImportError as exc:
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
    export.add_argument("--db", required=True, help="BookStack database name")
    export.add_argument("--user", required=True, help="Database user")
    export.add_argument("--password", required=True, help="Database password")
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

    sub.add_parser("version", help="Show version and exit")
    sub.add_parser("help", help="Show help and exit")

    return parser


if __name__ == "__main__":
    sys.exit(main() or 0)
