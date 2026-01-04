# Python Migration Tool

## bookstack_migration.py

Interactive Python-based BookStack to DokuWiki migration script with comprehensive hand-holding.

### What it does

A user-friendly, interactive migration tool that combines all the functionality of Perl/PHP/Shell scripts into a single Python implementation:

- Interactive setup and configuration
- Package dependency management with helpful guidance
- Complete migration workflow with progress tracking
- Robust error handling with recovery suggestions
- Testing before execution
- Detailed logging and reporting

### Features

- **Interactive Mode**: Step-by-step guidance through the entire process
- **Dependency Management**: Helps with pip, venv, and package installation
- **Comprehensive Testing**: Validates everything before making changes
- **Error Recovery**: Provides clear error messages and recovery steps
- **Progress Tracking**: Real-time status updates during migration
- **Backup Management**: Automatic backups before any modifications

### Prerequisites

```bash
# Python 3.8 or higher
python3 --version

# Required packages (script will help you install these)
pip3 install pymysql beautifulsoup4 lxml requests
```

### Usage

```bash
# Make executable
chmod +x bookstack_migration.py

# Run interactively (recommended)
./bookstack_migration.py

# Or with python3
python3 bookstack_migration.py

# Show help
python3 bookstack_migration.py --help
```

### Interactive Mode

The script will guide you through:
1. Database connection setup
2. Output directory selection
3. Backup creation
4. Migration execution
5. Verification and testing

### Configuration

The script accepts:
- Interactive prompts (default)
- Environment variables
- Command-line arguments
- Configuration file

Environment variables:
```bash
export BOOKSTACK_DB_HOST=localhost
export BOOKSTACK_DB_PORT=3306
export BOOKSTACK_DB_NAME=bookstack
export BOOKSTACK_DB_USER=bookstack
export BOOKSTACK_DB_PASS=secret
```

### Output Structure

```
storage/
├── backups/
│   └── bookstack-backup-TIMESTAMP/
│       ├── database.sql
│       └── files.tar.gz
├── dokuwiki-export/
│   ├── pages/
│   ├── media/
│   └── attic/
└── logs/
    └── migration.log
```

### Troubleshooting

**Package Installation Issues:**
- The script will guide you through pip, venv, or --break-system-packages options
- Follow the interactive prompts for your specific situation

**Database Connection:**
- Verify credentials in your `.env` file or environment
- Check MySQL/MariaDB service is running
- Ensure user has proper permissions

**Disk Space:**
- Ensure at least 2x your database size is available
- Backups are created before migration

### Author

Alex Alvonellos  
*"I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage. kthxbai."*

---

This is the recommended tool if you prefer Python and want interactive guidance.
