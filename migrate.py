#!/usr/bin/env python3
"""
╔══════════════════════════════════════════════════════════════════════╗
║                                                                      ║
║      📦 BOOKSTACK TO DOKUWIKI MIGRATION - PYTHON EDITION 📦         ║
║                                                                      ║
║  The ONE script because Python is what people actually use           ║
║                                                                      ║
║  I use Norton as my antivirus. My WinRAR isn't insecure,            ║
║  it's vintage. kthxbai.                                             ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝

Features:
- Combines ALL Perl/PHP/Shell functionality into Python
- Overly accommodating when you mess up package installation (gently)
- Provides intimate guidance through pip/venv/--break-system-packages
- Tests everything before running
- Robust error handling (because you WILL break it)
- Interactive hand-holding through the entire process

Usage:
    python3 bookstack_migration.py [--help]
    
Or just run it and let it hold your hand:
    chmod +x bookstack_migration.py
    ./bookstack_migration.py

Alex Alvonellos
I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage. kthxbai.
"""

import sys
import os
import subprocess
import json
import time
import hashlib
import shutil
import re
import logging
from pathlib import Path
from typing import Dict, List, Tuple, Optional, Any
from dataclasses import dataclass
from datetime import datetime

# ============================================================================
# LOGGING SETUP - Because we need intimate visibility into operations
# ============================================================================

def setup_logging():
    """Setup logging to both file and console"""
    log_dir = Path('./migration_logs')
    log_dir.mkdir(exist_ok=True)
    
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    log_file = log_dir / f'migration_{timestamp}.log'
    
    # Create logger
    logger = logging.getLogger('bookstack_migration')
    logger.setLevel(logging.DEBUG)
    
    # File handler - everything
    file_handler = logging.FileHandler(log_file, encoding='utf-8')
    file_handler.setLevel(logging.DEBUG)
    file_formatter = logging.Formatter(
        '%(asctime)s - %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )
    file_handler.setFormatter(file_formatter)
    
    # Console handler - info and above
    console_handler = logging.StreamHandler()
    console_handler.setLevel(logging.INFO)
    console_formatter = logging.Formatter('%(message)s')
    console_handler.setFormatter(console_formatter)
    
    logger.addHandler(file_handler)
    logger.addHandler(console_handler)
    
    logger.info(f"📝 Logging to: {log_file}")
    
    return logger

# Initialize logger
logger = setup_logging()

# ============================================================================
# DEPENDENCY MANAGEMENT - Gloating Edition
# ============================================================================

REQUIRED_PACKAGES = {
    'mysql-connector-python': 'mysql.connector',
    'pymysql': 'pymysql',
}

def gloat_about_python_packages():
    """Gloat about Python's package management situation (it's complicated)"""
    logger.info("Checking Python package management situation...")
    print("""
╔══════════════════════════════════════════════════════════════════════╗
║                  🐍 PYTHON PACKAGE MANAGEMENT 🐍                    ║
║                                                                      ║
║  Ah yes, Python. The language where:                                 ║
║  • pip breaks system packages                                        ║
║  • venv is "recommended" but nobody uses it                          ║
║  • --break-system-packages is a REAL FLAG                           ║
║  • Everyone has 47 versions of Python installed                      ║
║  • pip install works on your machine but nowhere else                ║
║                                                                      ║
║  But hey, at least it's not JavaScript! *nervous laughter*           ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
""")

def check_dependencies() -> Tuple[bool, List[str]]:
    """Check if required packages are installed - My precious, my precious!"""
    missing = []
    
    for package, import_name in REQUIRED_PACKAGES.items():
        try:
            __import__(import_name)
        except ImportError:
            missing.append(package)
            logger.debug(f"Missing package: {package}")
    
    return len(missing) == 0, missing

def try_install_package_least_invasive(pkg: str) -> bool:
    """
    Try to install package, least invasive option first - precious strategy!
    My precious, we try gently... then aggressively. That's the way.
    """
    logger.info(f"Trying to install {pkg} (least invasive first)...")
    
    # Option 1: Try pip3 with normal install
    try:
        logger.debug(f"  Attempt 1: pip3 install {pkg}")
        subprocess.check_call(
            ['pip3', 'install', pkg],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
        logger.info(f"✅ {pkg} installed via pip3")
        return True
    except (subprocess.CalledProcessError, FileNotFoundError) as e:
        logger.debug(f"    pip3 failed: {type(e).__name__}")
    
    # Option 2: Try pip (in case pip3 doesn't exist)
    try:
        logger.debug(f"  Attempt 2: pip install {pkg}")
        subprocess.check_call(
            ['pip', 'install', pkg],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
        logger.info(f"✅ {pkg} installed via pip")
        return True
    except (subprocess.CalledProcessError, FileNotFoundError) as e:
        logger.debug(f"    pip failed: {type(e).__name__}")
    
    # Option 3: Try python3 -m pip (most portable)
    try:
        logger.debug(f"  Attempt 3: python3 -m pip install {pkg}")
        subprocess.check_call(
            [sys.executable, '-m', 'pip', 'install', pkg],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
        logger.info(f"✅ {pkg} installed via python3 -m pip")
        return True
    except subprocess.CalledProcessError as e:
        logger.debug(f"    python3 -m pip failed: {e}")
    
    # Option 4: Try --user flag (per-user install, less invasive)
    try:
        logger.debug(f"  Attempt 4: pip3 install --user {pkg}")
        subprocess.check_call(
            ['pip3', 'install', '--user', pkg],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
        logger.info(f"✅ {pkg} installed via pip3 --user")
        return True
    except (subprocess.CalledProcessError, FileNotFoundError) as e:
        logger.debug(f"    pip3 --user failed: {type(e).__name__}")
    
    # Option 5: Try python3 -m pip --user
    try:
        logger.debug(f"  Attempt 5: python3 -m pip install --user {pkg}")
        subprocess.check_call(
            [sys.executable, '-m', 'pip', 'install', '--user', pkg],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
        logger.info(f"✅ {pkg} installed via python3 -m pip --user")
        return True
    except subprocess.CalledProcessError as e:
        logger.debug(f"    python3 -m pip --user failed: {e}")
    
    # Last resort: --break-system-packages (only if user explicitly allows)
    logger.warning(f"❌ All gentle installation attempts failed for {pkg}")
    return False

def offer_to_install_packages(missing: List[str]) -> bool:
    """
    Offer to install packages - We hisses at the dependencies, my precious!
    Tries automatic installation, then asks user what to do.
    """
    print(f"\n❌ Missing packages: {', '.join(missing)}")
    logger.warning(f"Missing packages: {', '.join(missing)}")
    print("\nOh no! You don't have the required packages installed!")
    print("But don't worry, my precious... we can fix this...\n")
    
    # Try automatic installation (least invasive options)
    print("🤔 Let me try to install these automatically...\n")
    
    all_installed = True
    for pkg in missing:
        if not try_install_package_least_invasive(pkg):
            all_installed = False
            logger.error(f"⚠️  Failed to auto-install {pkg}")
    
    if all_installed:
        print("\n✅ All packages installed successfully!")
        return True
    
    # If automatic installation failed, ask user
    print("\nAutomatic installation failed. Let me show you the options:\n")
    print("1. 💀 --break-system-packages (NOT RECOMMENDED - nuclear option)")
    print("2. 🎁 Create venv (proper way, install once and reuse)")
    print("3. 📝 Just show me the command (I'll do it myself)")
    print("4. 🚪 Exit and give up")
    print()
    
    while True:
        choice = input("Please choose (1-4): ").strip()
        
        if choice == '1':
            print("\n⚠️  WARNING: Using --break-system-packages WILL modify system Python!")
            print("   This can break other Python tools on your system.")
            confirm = input("   Are you REALLY sure? Type 'yes' to continue: ").strip().lower()
            
            if confirm == 'yes':
                print("\n💀 Using --break-system-packages... *at your own risk*")
                for pkg in missing:
                    try:
                        subprocess.check_call([
                            sys.executable, '-m', 'pip', 'install',
                            '--break-system-packages', pkg
                        ])
                        logger.info(f"✅ {pkg} installed via --break-system-packages")
                    except subprocess.CalledProcessError as e:
                        print(f"\n❌ Even --break-system-packages failed for {pkg}: {e}")
                        logger.error(f"--break-system-packages failed for {pkg}: {e}")
                        return False
                return True
            else:
                print("   Smart choice. Try option 2 instead.\n")
                continue
            
        elif choice == '2':
            print("\n🎓 Creating virtual environment (the RIGHT way)...")
            venv_path = Path.cwd() / 'migration_venv'
            try:
                subprocess.check_call([sys.executable, '-m', 'venv', str(venv_path)])
                pip_path = venv_path / 'bin' / 'pip'
                
                print("   Installing packages into venv...")
                for pkg in missing:
                    subprocess.check_call([str(pip_path), 'install', pkg])
                
                print(f"\n✅ Packages installed in venv!")
                print(f"\nNow activate it and run migration:")
                print(f"  source {venv_path}/bin/activate")
                print(f"  python3 {sys.argv[0]}")
                print()
                logger.info("Venv created successfully")
                return False  # They need to rerun in venv
                
            except subprocess.CalledProcessError as e:
                print(f"\n❌ venv creation failed: {e}")
                logger.error(f"venv creation failed: {e}")
                return False
                
        elif choice == '3':
            print("\n📝 Here's what you need to run:\n")
            for pkg in missing:
                print(f"pip3 install {pkg}")
                print(f"  or")
                print(f"pip install --user {pkg}")
                print()
            print("Or use venv (safest):")
            print(f"python3 -m venv migration_venv")
            print(f"source migration_venv/bin/activate")
            print(f"pip install {' '.join(missing)}")
            print()
            sys.exit(1)
            
        elif choice == '4':
            print("\n😢 Understood. Can't work without packages though.")
            logger.error("User chose to exit")
            sys.exit(1)
        else:
            print("❌ Invalid choice. Please choose 1-4.")

# ============================================================================
# OS DETECTION AND INSULTS
# ============================================================================

def detect_os_and_insult():
    """Detect OS and appropriately roast the user"""
    os_name = sys.platform
    
    if os_name.startswith('linux'):
        print("\n💻 Linux detected.")
        print("   You should switch to Windows for better gaming performance.")
        print("   Just kidding - you're doing great, sweetie. 🐧")
        return 'linux'
        
    elif os_name == 'darwin':
        print("\n🍎 macOS detected.")
        print("   Real twink boys make daddy buy them a new one when it breaks.")
        print("   But at least your Unix shell works... *chef's kiss* 💋")
        return 'macos'
        
    elif os_name == 'win32':
        print("\n🪟 Windows detected.")
        print("   You should switch to Mac for that sweet, sweet Unix terminal.")
        print("   Or just use WSL like everyone else who got stuck on Windows.")
        return 'windows'
        
    else:
        print(f"\n❓ Unknown OS: {os_name}")
        print("   What exotic system are you running? FreeBSD? TempleOS?")
        return 'unknown'

# ============================================================================
# MEAN GIRLS GLOATING
# ============================================================================

def gloat_regina_george(task_name: str, duration: float):
    """Gloat like Regina George when something takes too long"""
    if duration > 5.0:
        print(f"\n💅 {task_name} took {duration:.1f} seconds?")
        print("   Stop trying to make fetch happen! It's not going to happen!")
        print("   (But seriously, that's quite sluggish)")
    elif duration > 10.0:
        print(f"\n💅 {task_name} took {duration:.1f} seconds...")
        print("   Is butter a carb? Because this migration sure is slow.")
    elif duration > 30.0:
        print(f"\n💅 {task_name} took {duration:.1f} seconds!?")
        print("   On Wednesdays we wear pink. On other days we wait for migrations.")

# ============================================================================
# DATABASE CONNECTION
# ============================================================================

@dataclass
class DatabaseConfig:
    """Database configuration"""
    host: str
    database: str
    user: str
    password: str
    port: int = 3306

def load_env_file(env_path: str = None) -> Dict[str, str]:
    """Load Laravel .env file from standard BookStack location or fallback paths"""
    paths_to_try = []
    
    # If user provided path, try it first
    if env_path:
        paths_to_try.append(env_path)
    
    # Standard paths in priority order
    paths_to_try.extend([
        '/var/www/bookstack/.env',      # Standard BookStack location (most likely)
        '/var/www/html/.env',           # Alternative standard location
        '.env',                         # Current directory
        '../.env',                      # Parent directory
        '../../.env'                    # Two levels up
    ])
    
    env = {}
    found_file = None
    
    # Try each path
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
                
                found_file = path
                logger.info(f"✓ Loaded .env from: {path}")
                break
            except Exception as e:
                logger.debug(f"Error reading {path}: {e}")
                continue
    
    if not found_file and env_path is None:
        logger.info("No .env file found in standard locations")
    
    return env

def get_database_config() -> Optional[DatabaseConfig]:
    """Get database configuration from .env or prompt user"""
    env = load_env_file()
    
    # Try to get from .env
    if all(k in env for k in ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']):
        return DatabaseConfig(
            host=env['DB_HOST'],
            database=env['DB_DATABASE'],
            user=env['DB_USERNAME'],
            password=env['DB_PASSWORD'],
            port=int(env.get('DB_PORT', 3306))
        )
    
    # Prompt user
    print("\n📋 Database Configuration")
    print("(I couldn't find a .env file, so I need your help... 🥺)")
    print()
    
    host = input("Database host [localhost]: ").strip() or 'localhost'
    database = input("Database name: ").strip()
    user = input("Database user: ").strip()
    password = input("Database password: ").strip()
    
    if not all([database, user, password]):
        print("\n❌ You need to provide database credentials!")
        return None
    
    return DatabaseConfig(host, database, user, password)

def test_database_connection(config: DatabaseConfig) -> Tuple[bool, str]:
    """Test database connection"""
    try:
        import mysql.connector
        
        conn = mysql.connector.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        conn.close()
        return True, "Connected successfully!"
        
    except ImportError:
        try:
            import pymysql
            
            conn = pymysql.connect(
                host=config.host,
                user=config.user,
                password=config.password,
                database=config.database,
                port=config.port
            )
            conn.close()
            return True, "Connected successfully (using pymysql)!"
            
        except ImportError:
            return False, "No MySQL driver installed!"
            
    except Exception as e:
        return False, f"Connection failed: {str(e)}"

# ============================================================================
# BACKUP FUNCTIONALITY
# ============================================================================

def create_backup(config: DatabaseConfig, output_dir: str = './backup') -> bool:
    """Create backup of database and files"""
    print("\n💾 Creating backup...")
    print("(Because you WILL need this later, trust me)")
    
    start_time = time.time()
    
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_path = Path(output_dir) / f'bookstack_backup_{timestamp}'
    backup_path.mkdir(parents=True, exist_ok=True)
    
    # Database backup
    print("\n📦 Backing up database...")
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
            subprocess.run(cmd, stdout=f, check=True, stderr=subprocess.PIPE)
        
        print(f"   ✅ Database backed up to: {db_file}")
        
    except subprocess.CalledProcessError as e:
        print(f"   ❌ Database backup failed: {e.stderr.decode()}")
        print("\n   Would you like me to try a different approach? 🥺")
        
        if input("   Try Python-based backup? (yes/no): ").lower() == 'yes':
            # Fallback to Python-based dump
            print("   💝 Let me handle that for you...")
            return python_database_backup(config, db_file)
        return False
    
    # File backup
    print("\n📁 Backing up files...")
    for dir_name in ['storage/uploads', 'public/uploads', '.env']:
        if os.path.exists(dir_name):
            dest = backup_path / dir_name
            
            try:
                if os.path.isfile(dir_name):
                    dest.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(dir_name, dest)
                else:
                    shutil.copytree(dir_name, dest, dirs_exist_ok=True)
                print(f"   ✅ Backed up: {dir_name}")
            except Exception as e:
                print(f"   ⚠️  Failed to backup {dir_name}: {e}")
    
    duration = time.time() - start_time
    gloat_regina_george("Backup", duration)
    
    print(f"\n✅ Backup complete: {backup_path}")
    return True

def python_database_backup(config: DatabaseConfig, output_file: Path) -> bool:
    """Python-based database backup fallback"""
    try:
        import mysql.connector
        
        conn = mysql.connector.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        
        cursor = conn.cursor()
        
        with open(output_file, 'w') as f:
            # Get all tables
            cursor.execute("SHOW TABLES")
            tables = [table[0] for table in cursor.fetchall()]

            for table in tables:
                f.write(f"\n-- Table: {table}\n")
                f.write(f"DROP TABLE IF EXISTS {quote_ident(table)};\n")

                # Get CREATE TABLE
                cursor.execute(f"SHOW CREATE TABLE {quote_ident(table)}")
                create_table = cursor.fetchone()[1]
                f.write(f"{create_table};\n\n")

                # Get data
                cursor.execute(f"SELECT * FROM {quote_ident(table)}")
                rows = cursor.fetchall()

                if rows:
                    columns = [col[0] for col in cursor.description]      
                    f.write(f"INSERT INTO {quote_ident(table)} ({', '.join(quote_ident(c) for c in columns)}) VALUES\n")
                    
                    for i, row in enumerate(rows):
                        values = []
                        for val in row:
                            if val is None:
                                values.append('NULL')
                            elif isinstance(val, str):
                                escaped = val.replace("'", "\\'")
                                values.append(f"'{escaped}'")
                            else:
                                values.append(str(val))
                        
                        sep = ',' if i < len(rows) - 1 else ';'
                        f.write(f"({', '.join(values)}){sep}\n")
        
        conn.close()
        print("   ✅ Python backup successful!")
        return True
        
    except Exception as e:
        print(f"   ❌ Python backup also failed: {e}")
        return False

# ============================================================================  
# SQL IDENTIFIER QUOTING
# ============================================================================  

def quote_ident(name: str) -> str:
    """Quote MySQL identifiers to avoid reserved word conflicts"""
    safe = name.replace("`", "``")
    return f"`{safe}`"

# ============================================================================  
# SCHEMA INSPECTION - NO MORE HALLUCINATING
# ============================================================================  

def inspect_database_schema(config: DatabaseConfig) -> Dict[str, Any]:
    """Actually inspect the real database schema (no assumptions)"""
    print("\n🔍 Inspecting database schema...")
    print("(Let's see what you ACTUALLY have, not what I assume)")
    
    try:
        import mysql.connector
        
        conn = mysql.connector.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        
        cursor = conn.cursor(dictionary=True)
        
        # Get all tables
        cursor.execute("SHOW TABLES")
        tables = [list(row.values())[0] for row in cursor.fetchall()]

        print(f"\n📋 Found {len(tables)} tables:")

        schema = {}

        for table in tables:
            # Get column info
            cursor.execute(f"DESCRIBE {quote_ident(table)}")
            columns = cursor.fetchall()

            # Get row count
            cursor.execute(f"SELECT COUNT(*) as count FROM {quote_ident(table)}")
            row_count = cursor.fetchone()['count']

            schema[table] = {
                'columns': columns,
                'row_count': row_count
            }
            
            print(f"   • {table}: {row_count} rows")
        
        conn.close()
        
        return schema
        
    except Exception as e:
        print(f"\n❌ Schema inspection failed: {e}")
        return {}

def identify_content_tables(schema: Dict[str, Any]) -> Dict[str, str]:
    """Try to identify which tables contain content"""
    print("\n🤔 Trying to identify content tables...")
    
    content_tables = {}
    
    # BookStack v24+ uses unified entity model
    # Look for 'entities' table with 'type' column
    for table_name, table_info in schema.items():
        column_names = [col['Field'] for col in table_info['columns']]
        
        # Check for unified entities table (BookStack v24+)
        if table_name == 'entities' and 'type' in column_names:
            content_tables['entities'] = table_name
            print(f"   ✅ Found unified entities table: {table_name}")
            
            # Check for page data table
            if 'entity_page_data' in schema:
                content_tables['page_data'] = 'entity_page_data'
                print(f"   ✅ Found page data table: entity_page_data")
            
            # Check for container data (book/chapter descriptions)
            if 'entity_container_data' in schema:
                content_tables['container_data'] = 'entity_container_data'
                print(f"   ✅ Found container data table: entity_container_data")
            
            break
    
    # If no unified table, look for legacy separate tables
    if 'entities' not in content_tables:
        table_patterns = {
            'pages': ['id', 'name', 'slug', 'html', 'markdown'],
            'books': ['id', 'name', 'slug', 'description'],
            'chapters': ['id', 'name', 'slug', 'description', 'book_id'],
        }
        
        for table_name, table_info in schema.items():
            column_names = [col['Field'] for col in table_info['columns']]
            
            for pattern_name, required_cols in table_patterns.items():
                if all(col in column_names for col in required_cols[:2]):
                    content_tables[pattern_name] = table_name
                    print(f"   ✅ Found {pattern_name} table: {table_name}")
                    break
    
    return content_tables

def prompt_user_for_tables(schema: Dict[str, Any], identified: Dict[str, str]) -> Dict[str, str]:
    """Auto-select tables based on identification"""
    print("\n" + "="*70)
    print("TABLE SELECTION")
    print("="*70)
    
    print("\nIdentified content tables:")
    for content_type, table_name in identified.items():
        row_count = schema.get(table_name, {}).get('row_count', '?')
        print(f"   ✅ {content_type}: {table_name} ({row_count} rows)")
    
    # For unified entities table, auto-confirm
    if 'entities' in identified:
        print("\n✅ Using BookStack unified entity model (v24+)")
        print("   Hierarchical structure will be created automatically.")
        return identified
    
    # For legacy schema, also auto-confirm
    if identified:
        print("\n✅ Using legacy BookStack schema (separate tables)")
        print("   Hierarchical structure will be created automatically.")
        return identified
    
    # No tables identified - show all and ask for manual selection
    print("\n⚠️  Could not automatically identify content tables.")
    print("\nAll available tables:")
    for i, table_name in enumerate(sorted(schema.keys()), 1):
        row_count = schema[table_name]['row_count']
        print(f"   {i}. {table_name} ({row_count} rows)")
    
    print("\nWould you like to manually select tables?")
    confirm = input("Manual selection? (yes/no): ").strip().lower()
    
    if confirm != 'yes':
        return {}
    
    # Let user manually select
    print("\nOkay, let's do this manually...")
    
    tables = sorted(schema.keys())
    selected = {}
    
    for content_type in ['pages', 'books', 'chapters']:
        print(f"\n📋 Which table contains {content_type}?")
        print("Available tables:")
        for i, table_name in enumerate(tables, 1):
            print(f"   {i}. {table_name}")
        print("   0. Skip (no table for this)")
        
        while True:
            choice = input(f"Select {content_type} table (0-{len(tables)}): ").strip()
            
            try:
                idx = int(choice)
                if idx == 0:
                    break
                if 1 <= idx <= len(tables):
                    selected[content_type] = tables[idx - 1]
                    print(f"   ✅ Using {tables[idx - 1]} for {content_type}")
                    break
                else:
                    print(f"   ❌ Invalid choice. Pick 0-{len(tables)}")
            except ValueError:
                print("   ❌ Enter a number")
    
    return selected

# ============================================================================
# EXPORT FUNCTIONALITY - USING REAL SCHEMA
# ============================================================================

def export_to_dokuwiki(config: DatabaseConfig, output_dir: str = './dokuwiki_export') -> bool:
    """Export BookStack data to DokuWiki format with recursive directory structure"""
    print("\n📤 Exporting to DokuWiki format...")
    print("(Using ACTUAL schema with proper hierarchical structure)")
    
    start_time = time.time()
    
    try:
        import mysql.connector
        
        # First, inspect the schema
        schema = inspect_database_schema(config)
        
        if not schema:
            print("\n❌ Could not inspect database schema")
            return False
        
        # Identify content tables
        identified = identify_content_tables(schema)
        
        # Let user confirm
        tables = prompt_user_for_tables(schema, identified)
        
        if not tables:
            print("\n❌ No tables selected. Cannot export.")
            return False
        
        # Now do the actual export
        conn = mysql.connector.connect(
            host=config.host,
            user=config.user,
            password=config.password,
            database=config.database,
            port=config.port
        )
        
        cursor = conn.cursor(dictionary=True)
        
        export_path = Path(output_dir)
        export_path.mkdir(parents=True, exist_ok=True)
        
        # Check if using unified entities table (BookStack v24+)
        if 'entities' in tables:
            return export_unified_entities(cursor, schema, tables, export_path, start_time)
        
        # Otherwise use legacy export
        return export_legacy_schema(cursor, schema, tables, export_path, start_time)
        
    except Exception as e:
        print(f"\n❌ Export failed: {e}")
        print("\n   Oh no! Something went wrong... 😢")
        print("   Would you like me to show you the full error?")
        
        if input("   Show full error? (yes/no): ").lower() == 'yes':
            import traceback
            print("\n" + traceback.format_exc())
        
        return False

def export_unified_entities(cursor, schema, tables, export_path, start_time) -> bool:
    """Export using BookStack v24+ unified entities schema"""
    print("\n📦 Using unified entities model (BookStack v24+)")
    
    books_dict = {}
    chapters_dict = {}
    
    # Load all entities
    print("\n📚 Loading entities...")
    cursor.execute("""
        SELECT * FROM entities 
        WHERE deleted_at IS NULL 
        ORDER BY type, book_id, chapter_id, priority
    """)
    entities = cursor.fetchall()
    
    # Load container data (descriptions for books/chapters)
    container_data = {}
    if 'container_data' in tables:
        cursor.execute("SELECT * FROM entity_container_data")
        for row in cursor.fetchall():
            container_data[row['entity_id']] = row.get('description', '')
    
    # Load page data
    page_data = {}
    if 'page_data' in tables:
        cursor.execute("SELECT * FROM entity_page_data")
        for row in cursor.fetchall():
            page_data[row['page_id']] = row
    
    # First pass: create books
    print("\n📚 Creating book directories...")
    for entity in entities:
        if entity['type'] != 'book':
            continue
            
        book_id = entity['id']
        slug = entity['slug'] or f"book_{book_id}"
        name = entity['name'] or slug
        description = container_data.get(book_id, '')
        
        books_dict[book_id] = {
            'slug': slug,
            'name': name,
            'description': description,
            'path': export_path / slug
        }
        
        # Create book directory
        books_dict[book_id]['path'].mkdir(parents=True, exist_ok=True)
        
        # Create book start page
        book_content = convert_to_dokuwiki(description, name)
        start_file = books_dict[book_id]['path'] / 'start.txt'
        with open(start_file, 'w', encoding='utf-8') as f:
            f.write(book_content)
    
    print(f"   ✅ Created {len(books_dict)} book directories")
    
    # Second pass: create chapters
    print("\n📖 Creating chapter directories...")
    for entity in entities:
        if entity['type'] != 'chapter':
            continue
            
        chapter_id = entity['id']
        book_id = entity['book_id']
        slug = entity['slug'] or f"chapter_{chapter_id}"
        name = entity['name'] or slug
        description = container_data.get(chapter_id, '')
        
        # Find parent book
        if book_id and book_id in books_dict:
            book_path = books_dict[book_id]['path']
            chapter_path = book_path / slug
        else:
            # Orphaned chapter
            chapter_path = export_path / '_orphaned' / slug
        
        chapters_dict[chapter_id] = {
            'slug': slug,
            'name': name,
            'description': description,
            'path': chapter_path,
            'book_id': book_id
        }
        
        # Create chapter directory
        chapter_path.mkdir(parents=True, exist_ok=True)
        
        # Create chapter start page
        chapter_content = convert_to_dokuwiki(description, name)
        start_file = chapter_path / 'start.txt'
        with open(start_file, 'w', encoding='utf-8') as f:
            f.write(chapter_content)
    
    print(f"   ✅ Created {len(chapters_dict)} chapter directories")
    
    # Third pass: export pages
    print("\n📄 Exporting pages...")
    exported_count = 0
    orphaned_count = 0
    
    for entity in entities:
        if entity['type'] != 'page':
            continue
            
        page_id = entity['id']
        slug = entity['slug'] or f"page_{page_id}"
        name = entity['name'] or slug
        chapter_id = entity['chapter_id']
        book_id = entity['book_id']
        
        # Get page content
        data = page_data.get(page_id, {})
        content = (
            data.get('markdown') or 
            data.get('text') or 
            data.get('html') or 
            ''
        )
        
        # Determine target directory
        target_dir = None
        
        if chapter_id and chapter_id in chapters_dict:
            target_dir = chapters_dict[chapter_id]['path']
        elif book_id and book_id in books_dict:
            target_dir = books_dict[book_id]['path']
        else:
            target_dir = export_path / '_orphaned'
            target_dir.mkdir(parents=True, exist_ok=True)
            orphaned_count += 1
        
        # Create page file
        file_path = target_dir / f"{slug}.txt"
        dokuwiki_content = convert_to_dokuwiki(content, name)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(dokuwiki_content)
        
        exported_count += 1
        if exported_count % 10 == 0:
            print(f"   📝 Exported {exported_count} pages...")
    
    print(f"\n✅ Exported {exported_count} pages!")
    if orphaned_count > 0:
        print(f"   ⚠️  {orphaned_count} orphaned pages in _orphaned/")
    
    duration = time.time() - start_time
    gloat_regina_george("Export", duration)
    
    print(f"\n✅ Export complete: {export_path}")
    print("\n📂 Directory structure created:")
    print(f"   • Books: {len(books_dict)} directories")
    print(f"   • Chapters: {len(chapters_dict)} subdirectories")
    print(f"   • Pages: {exported_count} files")
    
    # Show sample structure
    if books_dict:
        print("\n📋 Sample structure:")
        for book_id, book_info in list(books_dict.items())[:3]:
            print(f"   📚 {book_info['slug']}/")
            print(f"      └─ start.txt (book index)")
            
            book_chapters = [c for c in chapters_dict.values() if c.get('book_id') == book_id]
            for chapter_info in book_chapters[:2]:
                print(f"      └─ {chapter_info['slug']}/")
                print(f"         └─ start.txt (chapter index)")
                print(f"         └─ *.txt (pages)")
    
    return True

def export_legacy_schema(cursor, schema, tables, export_path, start_time) -> bool:
    """Export using legacy separate tables schema (BookStack < v24)"""
    print("\n📦 Using legacy schema (separate tables)")
    
    books_dict = {}
    chapters_dict = {}
    
    # First, load books
    if 'books' in tables:
        print(f"\n📚 Loading books from {tables['books']}...")
        books_table = tables['books']
        book_cols = [col['Field'] for col in schema[books_table]['columns']]
        
        query = f"SELECT * FROM {quote_ident(books_table)}"
        if 'deleted_at' in book_cols:
            query += " WHERE `deleted_at` IS NULL"
        
        cursor.execute(query)
        books = cursor.fetchall()
        
        for book in books:
            book_id = book.get('id')
            slug = book.get('slug') or f"book_{book_id}"
            name = book.get('name') or slug
            description = book.get('description', '')
            
            books_dict[book_id] = {
                'slug': slug,
                'name': name,
                'description': description,
                'path': export_path / slug
            }
            
            # Create book directory
            books_dict[book_id]['path'].mkdir(parents=True, exist_ok=True)
            
            # Create book start page (index)
            book_content = convert_to_dokuwiki(description, name)
            start_file = books_dict[book_id]['path'] / 'start.txt'
            with open(start_file, 'w', encoding='utf-8') as f:
                f.write(book_content)
        
        print(f"   ✅ Created {len(books_dict)} book directories")
    
    # Second, load chapters
    if 'chapters' in tables:
        print(f"\n📖 Loading chapters from {tables['chapters']}...")
        chapters_table = tables['chapters']
        chapter_cols = [col['Field'] for col in schema[chapters_table]['columns']]
        
        query = f"SELECT * FROM {quote_ident(chapters_table)}"
        if 'deleted_at' in chapter_cols:
            query += " WHERE `deleted_at` IS NULL"
        
        cursor.execute(query)
        chapters = cursor.fetchall()
        
        for chapter in chapters:
            chapter_id = chapter.get('id')
            book_id = chapter.get('book_id')
            slug = chapter.get('slug') or f"chapter_{chapter_id}"
            name = chapter.get('name') or slug
            description = chapter.get('description', '')
            
            # Find parent book
            if book_id and book_id in books_dict:
                book_path = books_dict[book_id]['path']
                chapter_path = book_path / slug
            else:
                # Orphaned chapter - put in root
                chapter_path = export_path / slug
            
            chapters_dict[chapter_id] = {
                'slug': slug,
                'name': name,
                'description': description,
                'path': chapter_path,
                'book_id': book_id
            }
            
            # Create chapter directory
            chapter_path.mkdir(parents=True, exist_ok=True)
            
            # Create chapter start page (index)
            chapter_content = convert_to_dokuwiki(description, name)
            start_file = chapter_path / 'start.txt'
            with open(start_file, 'w', encoding='utf-8') as f:
                f.write(chapter_content)
        
        print(f"   ✅ Created {len(chapters_dict)} chapter directories")
    
    # Finally, export pages into the hierarchy
    if 'pages' in tables:
        print(f"\n📄 Exporting pages from {tables['pages']}...")
        pages_table = tables['pages']
        page_cols = [col['Field'] for col in schema[pages_table]['columns']]
        
        # Build query based on actual columns
        select_cols = []
        for col_name in ['id', 'name', 'slug', 'html', 'markdown', 'text', 'book_id', 'chapter_id']:
            if col_name in page_cols:
                select_cols.append(quote_ident(col_name))
        
        query = f"SELECT {', '.join(select_cols)} FROM {quote_ident(pages_table)}"
        if 'deleted_at' in page_cols:
            query += " WHERE `deleted_at` IS NULL"
        
        cursor.execute(query)
        pages = cursor.fetchall()
        
        exported_count = 0
        orphaned_count = 0
        
        for page in pages:
            page_id = page.get('id')
            slug = page.get('slug') or f"page_{page_id}"
            name = page.get('name') or slug
            chapter_id = page.get('chapter_id')
            book_id = page.get('book_id')
            
            # Get content from whatever column exists
            content = (
                page.get('markdown') or 
                page.get('text') or 
                page.get('html') or 
                ''
            )
            
            # Determine target directory based on hierarchy
            target_dir = None
            
            # Try chapter first (most specific)
            if chapter_id and chapter_id in chapters_dict:
                target_dir = chapters_dict[chapter_id]['path']
            # Then try book (if no chapter)
            elif book_id and book_id in books_dict:
                target_dir = books_dict[book_id]['path']
            # Otherwise, orphaned page goes to root
            else:
                target_dir = export_path / '_orphaned'
                target_dir.mkdir(parents=True, exist_ok=True)
                orphaned_count += 1
            
            # Create page file
            file_path = target_dir / f"{slug}.txt"
            dokuwiki_content = convert_to_dokuwiki(content, name)
            
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(dokuwiki_content)
            
            exported_count += 1
            if exported_count % 10 == 0:
                print(f"   📝 Exported {exported_count}/{len(pages)} pages...")
        
        print(f"\n✅ Exported {exported_count} pages!")
        if orphaned_count > 0:
            print(f"   ⚠️  {orphaned_count} orphaned pages in _orphaned/")
    else:
        exported_count = 0
        print("\n⚠️  No pages table selected, skipping pages export")
    
    duration = time.time() - start_time
    gloat_regina_george("Export", duration)
    
    print(f"\n✅ Export complete: {export_path}")
    print("\n📂 Directory structure created:")
    print(f"   • Books: {len(books_dict)} directories")
    print(f"   • Chapters: {len(chapters_dict)} subdirectories")
    print(f"   • Pages: {exported_count} files")
    
    # Show sample structure
    if books_dict:
        print("\n📋 Sample structure:")
        for book_id, book_info in list(books_dict.items())[:3]:
            print(f"   📚 {book_info['slug']}/")
            print(f"      └─ start.txt (book index)")
            
            book_chapters = [c for c in chapters_dict.values() if c.get('book_id') == book_id]
            for chapter_info in book_chapters[:2]:
                print(f"      └─ {chapter_info['slug']}/")
                print(f"         └─ start.txt (chapter index)")
                print(f"         └─ *.txt (pages)")
    
    return True

def convert_to_dokuwiki(content: str, title: str) -> str:
    """Convert HTML/Markdown to DokuWiki format"""
    # This is a simplified conversion
    # For production, use proper parsers
    
    dokuwiki = f"====== {title} ======\n\n"
    
    # Remove HTML tags (very basic)
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

# ============================================================================
# DIAGNOSTIC FUNCTIONALITY
# ============================================================================

def run_diagnostics() -> Dict[str, Any]:
    """Run comprehensive diagnostics"""
    print("\n🔍 Running diagnostics...")
    print("(Checking what needs attention)")
    
    diag = {
        'timestamp': datetime.now().isoformat(),
        'python_version': sys.version,
        'os': detect_os_and_insult(),
        'packages': {},
        'database': None,
        'disk_space': None,
    }
    
    # Check packages
    print("\n📦 Checking Python packages...")
    for package, import_name in REQUIRED_PACKAGES.items():
        try:
            __import__(import_name)
            diag['packages'][package] = 'installed'
            print(f"   ✅ {package}")
        except ImportError:
            diag['packages'][package] = 'missing'
            print(f"   ❌ {package} (MISSING)")
    
    # Check database
    print("\n🗄️  Checking database connection...")
    config = get_database_config()
    if config:
        success, message = test_database_connection(config)
        diag['database'] = {'success': success, 'message': message}
        
        if success:
            print(f"   ✅ {message}")
        else:
            print(f"   ❌ {message}")
    
    # Check disk space
    print("\n💾 Checking disk space...")
    try:
        stat = shutil.disk_usage('.')
        free_gb = stat.free / (1024**3)
        diag['disk_space'] = f"{free_gb:.2f} GB free"
        print(f"   💽 {free_gb:.2f} GB free")
        
        if free_gb < 1.0:
            print("   ⚠️  Less than 1GB free! You might run out of space!")
    except Exception as e:
        diag['disk_space'] = f"error: {e}"
        print(f"   ❌ Could not check disk space: {e}")
    
    print("\n✅ Diagnostics complete!")
    
    return diag

# ============================================================================
# MAIN MENU
# ============================================================================

def show_main_menu():
    """Show interactive main menu"""
    print("""
╔══════════════════════════════════════════════════════════════════════╗
║                    📦 MAIN MENU 📦                                   ║
╚══════════════════════════════════════════════════════════════════════╝

1. 🔍 Run Diagnostics
2. �️  Inspect Database Schema (see what you actually have)
3. 🧪 Dry Run Export (see what WOULD happen)
4. 💾 Create Backup
5. 📤 Export to DokuWiki
6. 🚀 Full Migration (Backup + Export)
7. 📖 Show Documentation
8. 🆘 Help (I'm lost)
9. 🚪 Exit

""")

def main():
    """Main entry point - The One Script to rule them all, precious!"""
    
    # Show banner
    print(__doc__)
    
    # Detect OS and insult
    detect_os_and_insult()
    
    # Gloat about Python (my precious Python!)
    logger.info("Starting migration tool - Sméagol mode engaged")
    gloat_about_python_packages()
    
    # Check dependencies - We needs them, my precious dependencies!
    logger.info("Checking dependencies...")
    has_deps, missing = check_dependencies()
    
    if not has_deps:
        logger.warning(f"Missing dependencies: {missing}")
        if not offer_to_install_packages(missing):
            print("\n❌ Dependencies not installed. Cannot continue.")
            print("   Sméagol is so sad... he cannot work without his precious packages...")
            logger.error("Dependencies not satisfied")
            sys.exit(1)
    
    print("\n✅ All dependencies satisfied!")
    logger.info("All dependencies ready")
    
    # Main loop - Sméagol's interactive dance
    while True:
        show_main_menu()
        
        choice = input("Choose an option (1-9): ").strip()
        
        if choice == '1':
            diag = run_diagnostics()
            print("\n📋 Diagnostic report generated")
            
        elif choice == '2':
            config = get_database_config()
            if config:
                schema = inspect_database_schema(config)
                
                print("\n" + "="*70)
                print("DATABASE SCHEMA DETAILS")
                print("="*70)
                
                for table_name, info in sorted(schema.items()):
                    print(f"\n📋 {table_name} ({info['row_count']} rows)")
                    print("   Columns:")
                    for col in info['columns']:
                        null = "NULL" if col['Null'] == 'YES' else "NOT NULL"
                        key = f" [{col['Key']}]" if col['Key'] else ""
                        print(f"      • {col['Field']}: {col['Type']} {null}{key}")
        
        elif choice == '3':
            config = get_database_config()
            if config:
                print("\n🧪 DRY RUN MODE - Nothing will be exported")
                print("="*70)
                
                schema = inspect_database_schema(config)
                identified = identify_content_tables(schema)
                tables = prompt_user_for_tables(schema, identified)
                
                if tables:
                    print("\n✅ DRY RUN SUMMARY:")
                    print(f"   Selected tables: {list(tables.keys())}")
                    
                    for content_type, table_name in tables.items():
                        row_count = schema[table_name]['row_count']
                        print(f"   • {content_type}: {table_name} ({row_count} items)")
                    
                    print("\n📝 This would export:")
                    total_files = sum(schema[t]['row_count'] for t in tables.values() if t in schema)
                    print(f"   • Approximately {total_files} files")
                    print(f"   • To directory: ./dokuwiki_export/")
                    print("\n✅ Dry run complete. No files were created.")
                else:
                    print("\n❌ No tables selected.")
            
        elif choice == '4':
            config = get_database_config()
            if config:
                create_backup(config)
            
        elif choice == '5':
            config = get_database_config()
            if config:
                export_to_dokuwiki(config)
            
        elif choice == '6':
            config = get_database_config()
            if config:
                print("\n🚀 Starting full migration...")
                print("(This will take a while. Stop trying to make fetch happen!)")
                
                if create_backup(config):
                    export_to_dokuwiki(config)
                    print("\n✅ Migration complete!")
                else:
                    print("\n❌ Backup failed. Not continuing with export.")
            
        elif choice == '7':
            print("\n📖 Documentation:")
            print("   README: ./bookstack-migration/README.md")
            print("   (Single source of truth; legacy docs were removed)")
            print()
            
        elif choice == '8':
            print("""
🆘 HELP

This script does everything you need:
1. Run diagnostics to check your setup
2. Inspect database schema (see what tables you actually have)
3. Dry run export (see what would happen without doing it)
4. Create a backup (DO THIS FIRST!)
5. Export your BookStack data to DokuWiki format
6. Full migration does both backup and export

If something breaks:
- Run diagnostics (option 1)
- Inspect schema (option 2)
- Try dry run (option 3)
- Copy the output
- Paste it to Claude AI or ChatGPT
- Ask for help

I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage. kthxbai.
""")
            
        elif choice == '9':
            print("\n👋 Goodbye! Come back when you're ready!")
            print("\nI use Norton as my antivirus. My WinRAR isn't insecure,")
            print("it's vintage. kthxbai.")
            break
            
        else:
            print("\n❌ Invalid choice. Try again.")
            print("(I know, making decisions is hard... 🥺)")
        
        input("\nPress ENTER to continue...")

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Interrupted by user")
        print("I understand... this is overwhelming. Take a break! 💕")
        sys.exit(0)
    except Exception as e:
        print(f"\n\n💀 Unexpected error: {e}")
        print("\nOh no! Something went terribly wrong! 😱")
        print("Would you like me to show you the full error?")
        
        if input("Show full error? (yes/no): ").lower() == 'yes':
            import traceback
            print("\n" + traceback.format_exc())
        
        sys.exit(1)
