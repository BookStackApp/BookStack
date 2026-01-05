#!/usr/bin/env python3
"""
BookStack to DokuWiki Migration Tool

Automates complete migration from BookStack to DokuWiki format including:
- Database schema inspection and table identification
- Full database backup with mysqldump
- Content export to DokuWiki format (.txt files)
- Metadata export (books/chapters as JSON)
- Archive creation for portability
- Complete logging and verification

Usage:
    python3 migrate.py                    # Interactive mode
    python3 migrate.py --full             # Full migration (non-interactive)
    python3 migrate.py --diagnose         # Check system health
    python3 migrate.py --backup           # Backup only
    python3 migrate.py --export           # Export only
"""

import sys
import os
import subprocess
import json
import time
import shutil
import re
import logging
import argparse
import tarfile
from pathlib import Path
from typing import Dict, List, Tuple, Optional, Any
from dataclasses import dataclass
from datetime import datetime

# ============================================================================
# SETUP & CONFIGURATION
# ============================================================================

def setup_logging():
    """Setup logging to both file and console"""
    log_dir = Path('./migration_logs')
    log_dir.mkdir(exist_ok=True)
    
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    log_file = log_dir / f'migration_{timestamp}.log'
    
    logger = logging.getLogger('bookstack_migration')
    logger.setLevel(logging.DEBUG)
    
    # File handler
    file_handler = logging.FileHandler(log_file, encoding='utf-8')
    file_handler.setLevel(logging.DEBUG)
    file_formatter = logging.Formatter(
        '%(asctime)s - %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )
    file_handler.setFormatter(file_formatter)
    
    # Console handler
    console_handler = logging.StreamHandler()
    console_handler.setLevel(logging.INFO)
    console_formatter = logging.Formatter('%(message)s')
    console_handler.setFormatter(console_formatter)
    
    logger.addHandler(file_handler)
    logger.addHandler(console_handler)
    
    return logger

logger = setup_logging()

@dataclass
class DatabaseConfig:
    """Database configuration"""
    host: str
    database: str
    user: str
    password: str
    port: int = 3306

# ============================================================================
# DEPENDENCY MANAGEMENT
# ============================================================================

REQUIRED_PACKAGES = {
    'mysql-connector-python': 'mysql.connector',
    'pymysql': 'pymysql',
}

def check_and_install_dependencies():
    """Auto-install dependencies with multiple fallback strategies"""
    logger.info("Checking dependencies...")
    print("\n🔧 Checking Python packages...")
    
    missing = []
    for package, import_name in REQUIRED_PACKAGES.items():
        try:
            __import__(import_name)
            print(f"   ✅ {package}")
        except ImportError:
            missing.append(package)
            print(f"   ❌ {package} (missing)")
    
    if not missing:
        print("\n✅ All packages installed!")
        return True
    
    print(f"\n⚠️  Missing packages: {', '.join(missing)}")
    print("   Attempting auto-installation...\n")
    
    for pkg in missing:
        installed = False
        
        # Try pip3
        if subprocess.run(['pip3', 'install', pkg], 
                         stdout=subprocess.DEVNULL, 
                         stderr=subprocess.DEVNULL).returncode == 0:
            print(f"   ✅ Installed {pkg} via pip3")
            installed = True
        # Try python3 -m pip
        elif subprocess.run([sys.executable, '-m', 'pip', 'install', pkg],
                           stdout=subprocess.DEVNULL,
                           stderr=subprocess.DEVNULL).returncode == 0:
            print(f"   ✅ Installed {pkg} via python -m pip")
            installed = True
        # Try with --user
        elif subprocess.run([sys.executable, '-m', 'pip', 'install', '--user', pkg],
                           stdout=subprocess.DEVNULL,
                           stderr=subprocess.DEVNULL).returncode == 0:
            print(f"   ✅ Installed {pkg} via python -m pip --user")
            installed = True
        
        if not installed:
            print(f"\n   ❌ Could not auto-install {pkg}")
            print(f"   Try manually: pip3 install {pkg}")
            logger.error(f"Failed to auto-install {pkg}")
            return False
    
    print("\n✅ Dependencies installed!")
    return True

# ============================================================================
# ENVIRONMENT LOADING
# ============================================================================

def load_env_file() -> Dict[str, str]:
    """Load .env file from standard BookStack locations"""
    paths_to_try = [
        '/var/www/bookstack/.env',
        '/var/www/html/.env',
        '.env',
        '../.env',
        '../../.env',
    ]
    
    env = {}
    
    for path in paths_to_try:
        if os.path.exists(path):
            try:
                with open(path, 'r') as f:
                    for line in f:
                        line = line.strip()
                        if not line or line.startswith('#') or '=' not in line:
                            continue
                        
                        key, value = line.split('=', 1)
                        value = value.strip('\'"')
                        env[key] = value
                
                logger.info(f"Loaded .env from: {path}")
                print(f"\n✅ Found .env: {path}")
                return env
            except Exception as e:
                logger.debug(f"Error reading {path}: {e}")
                continue
    
    logger.info("No .env file found in standard locations")
    return env

def get_database_config() -> Optional[DatabaseConfig]:
    """Get database config from .env or prompt user"""
    env = load_env_file()
    
    # Check if we have all required fields
    if all(k in env for k in ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']):
        config = DatabaseConfig(
            host=env.get('DB_HOST', 'localhost'),
            database=env['DB_DATABASE'],
            user=env['DB_USERNAME'],
            password=env['DB_PASSWORD'],
            port=int(env.get('DB_PORT', 3306))
        )
        logger.info(f"Using config from .env: {config.host}/{config.database}")
        return config
    
    # Prompt user
    print("\n📋 Database Configuration")
    print("   (Could not find .env, please enter credentials)\n")
    
    try:
        host = input("Database host [localhost]: ").strip() or 'localhost'
        database = input("Database name: ").strip()
        user = input("Database user: ").strip()
        password = input("Database password: ").strip()
        
        if not all([database, user, password]):
            print("\n❌ Database credentials required!")
            return None
        
        config = DatabaseConfig(host, database, user, password)
        logger.info(f"Using user-provided config: {host}/{database}")
        return config
    except KeyboardInterrupt:
        print("\n\n⚠️  Cancelled")
        return None

# ============================================================================
# DATABASE CONNECTION & INSPECTION
# ============================================================================

def connect_to_db(config: DatabaseConfig) -> Optional[Any]:
    """Connect to database with fallback drivers"""
    try:
        import mysql.connector
        conn = mysql.connector.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        logger.info(f"Connected to {config.host}/{config.database} via mysql.connector")
        return conn
    except ImportError:
        pass
    except Exception as e:
        logger.warning(f"mysql.connector connection failed: {e}")
    
    try:
        import pymysql
        conn = pymysql.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        logger.info(f"Connected to {config.host}/{config.database} via pymysql")
        return conn
    except ImportError:
        logger.error("Neither mysql.connector nor pymysql available")
    except Exception as e:
        logger.error(f"Database connection failed: {e}")
    
    return None

def inspect_schema(conn) -> Dict[str, Any]:
    """Inspect real database schema"""
    print("\n🔍 Inspecting database schema...")
    logger.info("Starting schema inspection")
    
    cursor = conn.cursor(dictionary=True) if hasattr(conn, 'cursor') else conn.cursor()
    
    try:
        cursor.execute("SHOW TABLES")
    except:
        cursor.execute("SHOW TABLES")
    
    tables = []
    if hasattr(cursor, 'fetchall'):
        for row in cursor.fetchall():
            tables.append(row[0] if isinstance(row, tuple) else list(row.values())[0])
    
    print(f"\n   Found {len(tables)} tables:")
    
    schema = {}
    
    for table in tables:
        cursor.execute(f"DESCRIBE `{table}`")
        columns = cursor.fetchall()
        
        cursor.execute(f"SELECT COUNT(*) as cnt FROM `{table}`")
        count_row = cursor.fetchone()
        row_count = count_row[0] if isinstance(count_row, tuple) else count_row.get('cnt', 0) if hasattr(count_row, 'get') else 0
        
        schema[table] = {
            'columns': columns,
            'row_count': row_count
        }
        
        print(f"      • {table}: {row_count} rows")
        logger.info(f"Table {table}: {row_count} rows, {len(columns)} columns")
    
    return schema

def identify_tables(schema: Dict[str, Any]) -> Dict[str, str]:
    """Identify content tables by column patterns"""
    print("\n🤔 Identifying content tables...")
    logger.info("Identifying content tables")
    
    identified = {}
    
    # Column patterns that must ALL be present
    patterns = {
        'pages': ['id', 'name', 'slug'],
        'books': ['id', 'name', 'slug'],
        'chapters': ['id', 'name', 'slug'],
    }
    
    for table, info in schema.items():
        col_names = []
        for col in info['columns']:
            if isinstance(col, dict):
                col_names.append(col.get('Field', col.get('0', '')))
            elif isinstance(col, tuple):
                col_names.append(col[0])
        
        col_set = set(col_names)
        
        # Try to match patterns
        for pattern_name, required_cols in patterns.items():
            if pattern_name in identified:
                continue
            
            if all(col in col_set for col in required_cols):
                identified[pattern_name] = table
                print(f"      ✅ Found {pattern_name}: {table}")
                logger.info(f"Identified {pattern_name} → {table}")
    
    if not identified:
        print("      ⚠️  No content tables identified!")
        logger.warning("No content tables matched expected patterns")
        print("\n   Available tables in database:")
        for table in sorted(schema.keys()):
            print(f"      • {table}")
    
    return identified

# ============================================================================
# BACKUP FUNCTIONALITY
# ============================================================================

def create_backup(config: DatabaseConfig, backup_dir: str = './backups') -> bool:
    """Create database and file backup"""
    print("\n💾 Creating backup...")
    logger.info("Starting backup")
    
    start_time = time.time()
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_path = Path(backup_dir) / f'bookstack_backup_{timestamp}'
    backup_path.mkdir(parents=True, exist_ok=True)
    
    # Database dump
    print("\n   📦 Backing up database...")
    db_file = backup_path / 'database.sql'
    
    try:
        cmd = [
            'mysqldump',
            f'--host={config.host}',
            f'--user={config.user}',
            f'--password={config.password}',
            config.database
        ]
        
        with open(db_file, 'w') as f:
            result = subprocess.run(cmd, stdout=f, stderr=subprocess.PIPE, timeout=300)
        
        if result.returncode == 0 and db_file.stat().st_size > 0:
            print(f"      ✅ Database backup: {db_file.stat().st_size / 1024 / 1024:.1f}MB")
            logger.info(f"Database backup successful: {db_file}")
        else:
            print(f"      ⚠️  mysqldump had issues (return code: {result.returncode})")
            logger.warning(f"mysqldump returned code: {result.returncode}")
    
    except FileNotFoundError:
        print("      ⚠️  mysqldump not found, skipping database backup")
        logger.warning("mysqldump not found on system")
    except Exception as e:
        print(f"      ⚠️  Backup error: {e}")
        logger.error(f"Backup error: {e}")
    
    # File backups
    print("\n   📁 Backing up files...")
    for dir_name in ['storage/uploads', 'public/uploads', '.env']:
        if os.path.exists(dir_name):
            try:
                dest = backup_path / dir_name
                if os.path.isfile(dir_name):
                    dest.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(dir_name, dest)
                else:
                    shutil.copytree(dir_name, dest, dirs_exist_ok=True)
                print(f"      ✅ Backed up: {dir_name}")
                logger.info(f"Backed up: {dir_name}")
            except Exception as e:
                logger.warning(f"Could not backup {dir_name}: {e}")
    
    # Create archive
    print("\n   📦 Creating archive...")
    archive_path = Path(backup_dir) / f'bookstack_backup_{timestamp}.tar.gz'
    
    try:
        with tarfile.open(archive_path, 'w:gz') as tar:
            tar.add(backup_path, arcname=backup_path.name)
        
        size_mb = archive_path.stat().st_size / 1024 / 1024
        print(f"      ✅ Archive: {archive_path.name} ({size_mb:.1f}MB)")
        logger.info(f"Backup archive created: {archive_path}")
    except Exception as e:
        logger.warning(f"Archive creation failed: {e}")
    
    duration = time.time() - start_time
    print(f"\n✅ Backup complete in {duration:.1f}s: {backup_path.parent.absolute()}")
    logger.info(f"Backup completed in {duration:.1f}s")
    
    return True

# ============================================================================
# EXPORT FUNCTIONALITY
def sanitize_filename(name: str) -> str:
    """Convert a name to a valid filesystem path component"""
    if not name:
        return 'unnamed'
    safe = re.sub(r'[^a-z0-9\s_\-.]', '', name.lower())
    safe = re.sub(r'\s+', '_', safe)
    safe = re.sub(r'_+', '_', safe)
    safe = safe.strip('_')
    return safe or 'unnamed'

# ============================================================================

def convert_to_dokuwiki(content: str, title: str) -> str:
    """Convert HTML/Markdown to DokuWiki format"""
    dokuwiki = f"====== {title} ======\n\n"
    
    if not content:
        return dokuwiki
    
    # Remove HTML tags
    content = re.sub(r'<br\s*/?>', '\n', content)
    content = re.sub(r'<p>', '\n', content)
    content = re.sub(r'</p>', '\n', content)
    content = re.sub(r'<[^>]+>', '', content)
    
    # Convert bold
    content = re.sub(r'\*\*(.+?)\*\*', r'**\1**', content)
    content = re.sub(r'__(.+?)__', r'**\1**', content)
    
    # Convert italic
    content = re.sub(r'\*(.+?)\*', r'//\1//', content)
    content = re.sub(r'_(.+?)_', r'//\1//', content)
    
    # Convert headers
    content = re.sub(r'^# (.+)$', r'====== \1 ======', content, flags=re.MULTILINE)
    content = re.sub(r'^## (.+)$', r'===== \1 =====', content, flags=re.MULTILINE)
    content = re.sub(r'^### (.+)$', r'==== \1 ====', content, flags=re.MULTILINE)
    
    dokuwiki += content.strip()
    
    return dokuwiki

def export_to_dokuwiki(conn, schema: Dict[str, Any], tables: Dict[str, str], 
                       output_dir: str = './dokuwiki_export') -> int:
    """Export BookStack to DokuWiki format with folder hierarchy (shelf/book/chapter/page)"""
    print("\n📤 Exporting to DokuWiki format...")
    logger.info("Starting export")
    
    start_time = time.time()
    
    if not tables:
        print("   ❌ No tables selected!")
        logger.error("Export aborted - no tables selected")
        return 0
    
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)
    
    print(f"\n   Output directory: {output_path.absolute()}")
    logger.info(f"Export directory: {output_path.absolute()}")
    
    cursor = conn.cursor(dictionary=True) if hasattr(conn, 'cursor') else conn.cursor()
    exported = 0
    
    # Export pages
    if 'pages' in tables:
        pages_table = tables['pages']
        print(f"\n   📄 Exporting pages from {pages_table}...")
        
        # Get columns
        cursor.execute(f"DESCRIBE `{pages_table}`")
        columns = cursor.fetchall()
        col_names = [c['Field'] if isinstance(c, dict) else c[0] for c in columns]
        col_set = set(col_names)
        
        # Build SELECT with all possible content and hierarchy columns
        select_cols = []
        for col in ['id', 'name', 'slug', 'chapter_id', 'book_id', 'text',
                    'markdown', 'html', 'raw_html']:
            if col in col_set:
                select_cols.append(col)
        
        query = f"SELECT {', '.join(select_cols)} FROM `{pages_table}`"
        if 'deleted_at' in col_set:
            query += " WHERE deleted_at IS NULL"
        
        cursor.execute(query)
        pages = cursor.fetchall()
        
        # Build hierarchy lookup
        books_lookup = {}
        chapters_lookup = {}
        shelves_lookup = {}
        book_to_shelves = {}
        
        # Get books info if available
        if 'books' in tables:
            books_table = tables['books']
            try:
                cursor.execute(f"SELECT id, name, slug FROM `{books_table}`")
                for book in cursor.fetchall():
                    bid = book.get('id') if isinstance(book, dict) else book[0]
                    bname = book.get('name') if isinstance(book, dict) else book[1]
                    bslug = book.get('slug') if isinstance(book, dict) else book[2]
                    books_lookup[bid] = {'name': bname, 'slug': bslug}
            except Exception as e:
                logger.debug(f"Books lookup failed: {e}")
        
        # Get shelves info via pivot (bookshelves_books)
        shelves_table = 'bookshelves' if 'bookshelves' in schema else 'bookshelf' if 'bookshelf' in schema else None
        pivot_table = 'bookshelves_books' if 'bookshelves_books' in schema else None
        if shelves_table:
            try:
                cursor.execute(f"SELECT id, name, slug FROM `{shelves_table}`")
                for shelf in cursor.fetchall():
                    sid = shelf.get('id') if isinstance(shelf, dict) else shelf[0]
                    sname = shelf.get('name') if isinstance(shelf, dict) else shelf[1]
                    sslug = shelf.get('slug') if isinstance(shelf, dict) else (shelf[2] if len(shelf) > 2 else '')
                    shelves_lookup[sid] = {'name': sname, 'slug': sslug}
            except Exception as e:
                logger.debug(f"Shelves lookup failed: {e}")
        if pivot_table and shelves_lookup:
            try:
                cursor.execute(f"SELECT bookshelf_id, book_id FROM `{pivot_table}`")
                for row in cursor.fetchall():
                    sid = row.get('bookshelf_id') if isinstance(row, dict) else row[0]
                    bid = row.get('book_id') if isinstance(row, dict) else row[1]
                    book_to_shelves.setdefault(bid, []).append(sid)
            except Exception as e:
                logger.debug(f"Pivot lookup failed: {e}")
        
        # Get chapters info if available
        if 'chapters' in tables:
            chapters_table = tables['chapters']
            try:
                cursor.execute(f"SELECT id, name, slug, book_id FROM `{chapters_table}`")
                for chapter in cursor.fetchall():
                    cid = chapter.get('id') if isinstance(chapter, dict) else chapter[0]
                    cname = chapter.get('name') if isinstance(chapter, dict) else chapter[1]
                    cslug = chapter.get('slug') if isinstance(chapter, dict) else chapter[2]
                    bid = chapter.get('book_id') if isinstance(chapter, dict) else (chapter[3] if len(chapter) > 3 else None)
                    chapters_lookup[cid] = {'name': cname, 'slug': cslug, 'book_id': bid}
            except Exception as e:
                logger.debug(f"Chapters lookup failed: {e}")
        
        # Process each page
        for page in pages:
            page_id = page.get('id') if isinstance(page, dict) else page[0]
            page_name = page.get('name') if isinstance(page, dict) else ''
            page_slug = page.get('slug') if isinstance(page, dict) else ''
            chapter_id = page.get('chapter_id') if isinstance(page, dict) else None
            book_id = page.get('book_id') if isinstance(page, dict) else None
            
            # Get content - try multiple columns in order
            content = ''
            for col in ['markdown', 'text', 'html', 'raw_html']:
                if isinstance(page, dict) and col in page and page[col]:
                    content = page[col]
                    break
                if not isinstance(page, dict) and len(page) > 0:
                    # tuple fallback: assume select order matches select_cols
                    pass
            
            # Build directory path: shelf/book/chapter/page
            path_parts = [output_path]
            shelf_slug = None
            if book_id and book_id in book_to_shelves and book_to_shelves[book_id]:
                sid = book_to_shelves[book_id][0]
                if sid in shelves_lookup:
                    shelf_slug = shelves_lookup[sid]['slug']
            if shelf_slug:
                path_parts.append(sanitize_filename(shelf_slug))
            elif shelves_lookup:
                path_parts.append('unshelved')
            
            if book_id and book_id in books_lookup:
                book_slug = books_lookup[book_id]['slug']
                path_parts.append(sanitize_filename(book_slug))
            elif book_id:
                path_parts.append(f"book_{book_id}")
            
            if chapter_id and chapter_id in chapters_lookup:
                chapter_slug = chapters_lookup[chapter_id]['slug']
                path_parts.append(sanitize_filename(chapter_slug))
            elif chapter_id:
                path_parts.append(f"chapter_{chapter_id}")
            
            if not page_slug:
                page_slug = f"page_{page_id}"
            page_slug = sanitize_filename(page_slug)
            
            page_dir = Path(*path_parts)
            page_dir.mkdir(parents=True, exist_ok=True)
            
            file_path = page_dir / f"{page_slug}.txt"
            dokuwiki_content = convert_to_dokuwiki(content, page_name or page_slug)
            
            try:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(dokuwiki_content)
                
                if not file_path.exists():
                    logger.error(f"File not created: {file_path}")
                    continue
                
                exported += 1
                
                if exported % 50 == 0:
                    print(f"      📝 {exported} pages exported...")
                    logger.info(f"Exported {exported} pages so far")
            
            except Exception as e:
                logger.error(f"Error writing {file_path}: {e}")
        
        print(f"\n      ✅ Pages exported: {exported}")
        logger.info(f"Pages export complete: {exported} files")

    # Export books as JSON
    if 'books' in tables:
        books_table = tables['books']
        print(f"\n   📚 Exporting books...")
        
        cursor.execute(f"SELECT * FROM `{books_table}`")
        books = cursor.fetchall()
        
        if books:
            books_file = output_path / '_books.json'
            with open(books_file, 'w') as f:
                json.dump(books, f, indent=2, default=str)
            
            print(f"      ✅ Books metadata: {len(books)} items")
            logger.info(f"Books export: {len(books)} items")
    
    # Export chapters as JSON
    if 'chapters' in tables:
        chapters_table = tables['chapters']
        print(f"\n   📖 Exporting chapters...")
        
        cursor.execute(f"SELECT * FROM `{chapters_table}`")
        chapters = cursor.fetchall()
        
        if chapters:
            chapters_file = output_path / '_chapters.json'
            with open(chapters_file, 'w') as f:
                json.dump(chapters, f, indent=2, default=str)
            
            print(f"      ✅ Chapters metadata: {len(chapters)} items")
            logger.info(f"Chapters export: {len(chapters)} items")
    
    # Create archive
    print(f"\n   📦 Creating archive...")
    archive_path = Path(output_dir).parent / f"{Path(output_dir).name}.tar.gz"
    
    try:
        with tarfile.open(archive_path, 'w:gz') as tar:
            tar.add(output_path, arcname=output_path.name)
        
        size_mb = archive_path.stat().st_size / 1024 / 1024
        print(f"      ✅ Archive: {archive_path.name} ({size_mb:.1f}MB)")
        logger.info(f"Export archive created: {archive_path}")
    except Exception as e:
        logger.warning(f"Archive creation failed: {e}")
    
    # Verify files (recursive to include nested structure)
    txt_files = list(output_path.rglob('*.txt'))
    print(f"\n   ✅ Files created: {len(txt_files)}")
    logger.info(f"Export complete: {len(txt_files)} files")
    
    duration = time.time() - start_time
    
    print(f"\n✅ Export complete in {duration:.1f}s")
    print(f"   Output: {output_path.absolute()}")
    logger.info(f"Export completed in {duration:.1f}s to {output_path.absolute()}")
    
    return exported

# ============================================================================
# MAIN FLOW
# ============================================================================

def main():
    """Main entry point"""
    
    parser = argparse.ArgumentParser(
        description='BookStack to DokuWiki Migration',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python3 migrate.py                # Interactive mode
  python3 migrate.py --full         # Full migration (non-interactive)
  python3 migrate.py --diagnose     # Check system health
  python3 migrate.py --backup       # Backup only
  python3 migrate.py --export       # Export only
        """
    )
    
    parser.add_argument('--full', action='store_true', help='Full migration (backup + export)')
    parser.add_argument('--diagnose', action='store_true', help='Diagnose system')
    parser.add_argument('--backup', action='store_true', help='Backup only')
    parser.add_argument('--export', action='store_true', help='Export only')
    parser.add_argument('--output', default='./dokuwiki_export', help='Export directory')
    parser.add_argument('--backup-dir', default='./backups', help='Backup directory')
    
    args = parser.parse_args()
    
    # Banner
    print(__doc__)
    print("\n" + "="*80)
    print("Starting BookStack Migration")
    print("="*80)
    
    # Check dependencies
    if not check_and_install_dependencies():
        print("\n❌ Failed to install dependencies")
        sys.exit(1)
    
    # Get database config
    config = get_database_config()
    if not config:
        print("\n❌ Could not get database configuration")
        sys.exit(1)
    
    # Connect to database
    print("\n🔗 Connecting to database...")
    conn = connect_to_db(config)
    if not conn:
        print("\n❌ Could not connect to database")
        sys.exit(1)
    
    print(f"   ✅ Connected to {config.host}/{config.database}")
    logger.info(f"Connected to {config.host}/{config.database}")
    
    # Inspect schema
    schema = inspect_schema(conn)
    if not schema:
        print("\n❌ Could not inspect database schema")
        sys.exit(1)
    
    # Identify tables
    identified = identify_tables(schema)
    
    # Determine what to do
    do_backup = args.backup or args.full or (not args.diagnose and not args.export)
    do_export = args.export or args.full or (not args.diagnose and not args.backup)
    
    if args.diagnose:
        print("\n✅ Diagnostics complete. All systems ready.")
        logger.info("Diagnostics completed successfully")
        conn.close()
        return
    
    # Ask user to confirm tables if interactive
    selected_tables = identified
    if sys.stdin.isatty() and (do_backup or do_export):
        print("\n" + "="*80)
        print("TABLE SELECTION")
        print("="*80)
        
        print("\nIdentified tables:")
        for ctype, table in identified.items():
            print(f"   {ctype}: {table}")
        
        confirm = input("\nUse these tables? (yes/no): ").strip().lower()
        
        if confirm not in ['yes', 'y', '']:
            print("\nManual selection:")
            selected_tables = {}
            
            for ctype in ['pages', 'books', 'chapters']:
                tables_list = sorted(schema.keys())
                print(f"\n{ctype} table:")
                for i, t in enumerate(tables_list, 1):
                    print(f"   {i}. {t}")
                
                choice = input(f"Select (1-{len(tables_list)}, 0=skip): ").strip()
                try:
                    idx = int(choice)
                    if idx > 0:
                        selected_tables[ctype] = tables_list[idx - 1]
                except:
                    pass
    
    # Perform backup
    if do_backup:
        create_backup(config, args.backup_dir)
    
    # Perform export
    if do_export:
        export_to_dokuwiki(conn, schema, selected_tables, args.output)
    
    conn.close()
    
    print("\n" + "="*80)
    print("✅ MIGRATION COMPLETE")
    print("="*80)
    print(f"\n📝 Logs: ./migration_logs/")
    print(f"📤 Export: {Path(args.output).absolute()}")
    print(f"💾 Backup: {Path(args.backup_dir).absolute()}")
    print("\n🎉 Done! Your BookStack data is safe and ready for DokuWiki.")
    logger.info("Migration completed successfully")

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Interrupted by user")
        logger.warning("User interrupted migration")
        sys.exit(0)
    except Exception as e:
        print(f"\n\n❌ Error: {e}")
        logger.exception("Unexpected error")
        import traceback
        print(traceback.format_exc())
        sys.exit(1)
