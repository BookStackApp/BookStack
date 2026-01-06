# DokuWiki Export Guide

> Complete guide to exporting BookStack content to DokuWiki format.

**Note:** The bundled exporter is experimental and focused on text content (books, chapters, pages). It does **not** move attachments/media or rewrite internal links. Use it in staging and validate results before touching production data.

---

## 📖 Chapter 1: Overview & Features

### What is DokuWiki Export?

The BookStack to DokuWiki migration tool (`migrate.py`) exports your entire BookStack installation into DokuWiki's flat-file structure while preserving the hierarchical organization.

### Key Features & Limits

✅ **Hierarchical Structure Preservation**
- Books → Directories with `start.txt`
- Chapters → Subdirectories with `start.txt`
- Pages → Individual `.txt` files

✅ **Automatic Schema Detection**
- Detects BookStack v24+ unified entity model
- Falls back to legacy separate-tables schema
- Manual selection offered if detection fails

✅ **Content Conversion (basic)**
- HTML/Markdown → DokuWiki-ish markup (simplified)
- Headers, bold, italics, lists
- **Attachments/media are not exported or rewritten**

✅ **Safety Features**
- Optional backup step before export
- Dry-run mode for testing
- Logging to `migration_logs/`

### Limitations

- Does not move or rewrite attachments/media; copy uploads manually if needed.
- Does not rewrite internal links; review and fix links after import.
- Conversion is lightweight and may lose complex formatting or embeds.

---

## 📖 Chapter 2: Installation & Setup

### Prerequisites

**System Requirements:**
- Python 3.8 or higher
- MySQL/MariaDB client
- Access to BookStack database

**Python Packages:**
```bash
# Install via pip
pip3 install mysql-connector-python

# Or alternative
pip3 install pymysql
```

### Quick Setup

1. **Download the script:**
   ```bash
   cd /path/to/bookstack
   # Script already included: migrate.py
   ```

2. **Verify dependencies:**
   ```bash
   python3 migrate.py
   # Will auto-check and offer to install missing packages
   ```

3. **Configure database access:**
   - Script auto-detects `.env` file at `/var/www/bookstack/.env`
   - Or manually provide credentials when prompted

### Environment File

The script automatically searches for `.env` in these locations:
1. `/var/www/bookstack/.env` (standard)
2. `/var/www/html/.env` (alternative)
3. Current directory
4. Parent directories

**Manual Configuration:**
If no `.env` found, provide interactively:
- Database host (default: `localhost`)
- Database name
- Database user
- Database password
- Database port (default: `3306`)

---

## 📖 Chapter 3: Usage & Configuration

### Interactive Menu

Run the script to access the interactive menu:

```bash
python3 migrate.py
```

**Menu Options:**
```
1. 🔍 Run Diagnostics
2. 🗄️  Inspect Database Schema
3. 🧪 Dry Run Export
4. 💾 Create Backup
5. 📤 Export to DokuWiki
6. 🚀 Full Migration (Backup + Export)
7. 📖 Show Documentation
8. 🆘 Help
9. 🚪 Exit
```

### Option 1: Run Diagnostics

Tests your system setup:
- Python version check
- Package availability
- Database connectivity
- Disk space verification

### Option 2: Inspect Database Schema

Examines your BookStack database:
- Lists all tables with row counts
- Shows column definitions
- Identifies content tables
- Displays relationships

**Output Example:**
```
entities: 341 rows
  • id: bigint unsigned NOT NULL [PRI]
  • type: varchar(10) NOT NULL [PRI]
  • name: varchar(191) NOT NULL
  • slug: varchar(191) NOT NULL [MUL]
  • book_id: bigint unsigned NULL [MUL]
  • chapter_id: bigint unsigned NULL [MUL]
  ...
```

### Option 3: Dry Run Export

Simulates export without creating files:
- Shows which tables will be used
- Displays item counts
- Estimates output size
- No changes made to filesystem

**Use Case:** Verify configuration before actual export

### Option 4: Create Backup

Backs up your BookStack data:
- Database dump via `mysqldump`
- Copies file uploads
- Saves `.env` file
- Creates timestamped directory

**Output:**
```
./backup/bookstack_backup_20260106_120000/
├── database.sql
├── storage/
│   └── uploads/
├── public/
│   └── uploads/
└── .env
```

### Option 5: Export to DokuWiki

Performs the actual export:

```bash
# Select option 5 from menu
# Or programmatic:
python3 << EOF
from migrate import DatabaseConfig, export_to_dokuwiki

config = DatabaseConfig(
    host='localhost',
    database='bookstack',
    user='bookstack_user',
    password='your_password'
)

export_to_dokuwiki(config, './dokuwiki_export')
EOF
```

**Export Process:**
1. ✅ Inspect database schema
2. ✅ Identify content tables (auto-detected)
3. ✅ Load all entities (books, chapters, pages)
4. ✅ Create directory structure
5. ✅ Export text content to DokuWiki format
6. ✅ Generate `start.txt` index files
7. ⚠️  Attachments/media are **not** exported; copy uploads manually if needed

**Output Structure:**
```
dokuwiki_export/
├── getting-started/              (Book)
│   ├── start.txt                (Book index)
│   ├── installation/            (Chapter)
│   │   ├── start.txt           (Chapter index)
│   │   ├── requirements.txt    (Page)
│   │   └── setup.txt           (Page)
│   └── configuration/          (Chapter)
│       ├── start.txt
│       └── settings.txt
└── user-guide/                 (Book)
    ├── start.txt
    └── basics.txt              (Page without chapter)
```

### Option 6: Full Migration

Combines backup and export:
1. Creates full backup
2. Waits for confirmation
3. Performs export
4. Provides completion summary

**Recommended for production use**

---

## 📖 Chapter 4: Advanced Configuration

### Custom Export Directory

Specify output location:

```python
export_to_dokuwiki(config, '/var/www/dokuwiki/data/pages/')
```

### Schema-Specific Handling

**BookStack v24+ (Unified Entities):**
- Auto-detected: `entities`, `entity_page_data`, `entity_container_data`
- No user prompts required
- Hierarchical structure automatic

**Legacy BookStack (Separate Tables):**
- Auto-detected: `books`, `chapters`, `pages`
- Falls back gracefully
- Same hierarchical output

**Unknown Schema:**
- Manual table selection offered
- Guided configuration
- Flexible mapping

### Content Conversion Details

**HTML to DokuWiki:**
```
<h1>Title</h1>          → ====== Title ======
<p>Paragraph</p>        → (plain text)
<strong>Bold</strong>   → **Bold**
<em>Italic</em>         → //Italic//
<br>                    → (newline)
```

**Markdown to DokuWiki:**
```
# Title                 → ====== Title ======
## Subtitle             → ===== Subtitle =====
**Bold**                → **Bold**
*Italic*                → //Italic//
```

### Handling Orphaned Content

Pages without book/chapter assignment:
- Placed in `_orphaned/` directory
- Maintains original slug as filename
- Preserves all content
- Easy to reorganize later

---

## 📖 Chapter 5: Troubleshooting

### Common Issues

#### Issue: "No MySQL driver installed"

**Symptom:**
```
ImportError: No module named 'mysql.connector'
ImportError: No module named 'pymysql'
```

**Solution:**
```bash
# Try auto-install (script will offer this)
python3 migrate.py

# Or manual install
pip3 install mysql-connector-python

# Or alternative
pip3 install pymysql
```

#### Issue: "Connection refused"

**Symptom:**
```
Connection failed: Can't connect to MySQL server on 'localhost'
```

**Solutions:**
1. Verify MySQL is running:
   ```bash
   sudo systemctl status mysql
   ```

2. Check connection details:
   ```bash
   mysql -h localhost -u bookstack_user -p
   ```

3. Verify port (default 3306):
   ```bash
   netstat -tlnp | grep mysql
   ```

4. Check firewall rules

#### Issue: "Permission denied"

**Symptom:**
```
PermissionError: [Errno 13] Permission denied: './dokuwiki_export'
```

**Solutions:**
```bash
# Create directory with correct permissions
mkdir -p dokuwiki_export
chmod 755 dokuwiki_export

# Or run with sudo (not recommended)
sudo python3 migrate.py
```

#### Issue: "Could not identify content tables"

**Symptom:**
```
⚠️ Could not automatically identify content tables.
```

**Solution:**
- Choose manual selection (option in menu)
- Inspect schema first (option 2)
- Verify BookStack version
- Check database integrity

#### Issue: "Export failed: disk space"

**Symptom:**
```
OSError: [Errno 28] No space left on device
```

**Solutions:**
```bash
# Check disk space
df -h

# Clear old exports
rm -rf dokuwiki_export_old/

# Use different directory
export_to_dokuwiki(config, '/mnt/large_drive/export')
```

### Verification

After export, verify the structure:

```bash
# Count files
find dokuwiki_export -name "*.txt" | wc -l

# Check directory structure
tree -L 3 dokuwiki_export

# Verify file sizes
du -sh dokuwiki_export/*

# Sample content
head dokuwiki_export/*/start.txt
```

### Logs

Check logs for detailed information:

```bash
# Latest log
ls -lt migration_logs/ | head -1

# View log
cat migration_logs/migration_YYYYMMDD_HHMMSS.log

# Search for errors
grep -i error migration_logs/*.log
```

---

## 📖 Chapter 6: Production Deployment

### Pre-Migration Checklist

- [ ] **Backup complete**: Database + files
- [ ] **Test export**: Dry run successful
- [ ] **Disk space**: 2x database size available
- [ ] **Downtime**: Schedule maintenance window
- [ ] **Permissions**: Write access to export directory
- [ ] **Dependencies**: Python packages installed
- [ ] **Verification**: Test import into DokuWiki

### Migration Steps

1. **Maintenance mode:**
   ```bash
   php artisan down
   ```

2. **Final backup:**
   ```bash
   python3 migrate.py
   # Choose option 4
   ```

3. **Export:**
   ```bash
   python3 migrate.py
   # Choose option 5 or 6
   ```

4. **Verify export:**
   ```bash
   tree -L 2 dokuwiki_export
   ```

5. **Copy to DokuWiki:**
   ```bash
   rsync -av dokuwiki_export/ /var/www/dokuwiki/data/pages/
   ```

6. **Test DokuWiki:**
   - Browse to DokuWiki installation
   - Verify pages load
   - Check formatting
   - Test search

7. **Cleanup:**
   ```bash
   php artisan up  # If keeping BookStack
   ```

### Post-Migration

**DokuWiki Configuration:**
```php
// conf/local.php
$conf['useacl'] = 1;
$conf['superuser'] = '@admin';
$conf['indexdelay'] = 0;  # Rebuild search index immediately
```

**Rebuild search index:**
```bash
# In DokuWiki root
php bin/indexer.php -c
```

---

## 🧹 Cleanup

- Delete test exports after verification:
   ```bash
   rm -rf dokuwiki_export
   ```
- Remove old backups you no longer need, keeping at least one known-good copy:
   ```bash
   rm -rf backup/bookstack_backup_*
   ```

---

## 🎯 Quick Reference

### Command Cheat Sheet

```bash
# Run interactive menu
python3 migrate.py

# Test script
python3 test_hierarchical_export.py

# View logs
tail -f migration_logs/migration_*.log

# Check export
tree -L 3 dokuwiki_export
```

### File Locations

| File | Purpose |
|------|---------|
| `migrate.py` | Main migration script (57KB) |
| `test_hierarchical_export.py` | Test & validation |
| `migration_logs/` | Execution logs |
| `dokuwiki_export/` | Default export directory |
| `.env` | Database configuration (auto-detected) |

### Support Resources

- **Documentation**: This file
- **Issues**: [GitHub Issues](https://github.com/BookStackApp/BookStack/issues)
- **Script**: `migrate.py` includes `--help` mode

---

[← Back to Migration](./README.md) | [Database Migration →](./database-migration.md)
