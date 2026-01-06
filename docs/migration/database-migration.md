# Database Migration Procedures

> Procedures for migrating existing BookStack database instances

---

## 📖 Chapter 1: Prerequisites

### System Requirements

**Source System (BookStack):**
- BookStack version 0.x - 24.x
- MySQL 5.7+ or MariaDB 10.3+
- PHP 7.4+ (for older versions) or 8.1+ (for v24+)
- Database access credentials

**Target System:**
- Python 3.8+ with `mysql-connector-python` or `pymysql`
- Network access to source database
- Sufficient disk space (2x database size)

### Pre-Migration Checklist

- [ ] **Database credentials** verified and documented
- [ ] **Backup strategy** planned and tested
- [ ] **Downtime window** scheduled and communicated
- [ ] **Disk space** verified (minimum 2x current DB size)
- [ ] **Network connectivity** between source and migration host
- [ ] **Migration script** tested in staging environment
- [ ] **Rollback plan** documented

---

## 📖 Chapter 2: Migration Script

### Script Overview

The `migrate.py` script provides comprehensive database migration capabilities:

```python
# Location: /workspaces/BookStack/migrate.py
# Size: 57 KB
# Dependencies: mysql-connector-python OR pymysql
```

### Features

✅ **Database Inspection**
- Schema analysis and table discovery
- Automatic version detection
- Row count and size estimation

✅ **Backup Creation**
- Full database dump via `mysqldump`
- File upload archival
- Configuration preservation

✅ **Export Options**
- DokuWiki format export
- Hierarchical structure preservation
- Custom format support (extensible)

✅ **Validation**
- Connectivity testing
- Dependency checking
- Dry-run capability

### Installation

```bash
# 1. Copy migration script
cp migrate.py /opt/migration/

# 2. Install dependencies
pip3 install mysql-connector-python

# 3. Test connectivity
python3 migrate.py
# Choose option 1: Run Diagnostics
```

---

## 📖 Chapter 3: Backup & Restore

### Creating a Backup

#### Using migrate.py

```bash
python3 migrate.py
# Select option 4: Create Backup
```

**Output:**
```
./backup/bookstack_backup_YYYYMMDD_HHMMSS/
├── database.sql          # Full database dump
├── storage/
│   └── uploads/         # User-uploaded files
├── public/
│   └── uploads/         # Public file uploads
└── .env                 # Configuration backup
```

#### Manual Database Backup

```bash
# Complete backup
mysqldump -h localhost -u bookstack_user -p \
    --single-transaction \
    --routines \
    --triggers \
    bookstack > bookstack_backup.sql

# Compressed backup
mysqldump -h localhost -u bookstack_user -p \
    --single-transaction \
    bookstack | gzip > bookstack_backup.sql.gz

# With progress (using pv)
mysqldump -h localhost -u bookstack_user -p \
    bookstack | pv | gzip > bookstack_backup.sql.gz
```

### Restoring a Backup

#### Restore Database

```bash
# From uncompressed dump
mysql -h localhost -u bookstack_user -p \
    bookstack < bookstack_backup.sql

# From compressed dump
gunzip < bookstack_backup.sql.gz | \
    mysql -h localhost -u bookstack_user -p bookstack

# With progress
pv bookstack_backup.sql | \
    mysql -h localhost -u bookstack_user -p bookstack
```

#### Restore Files

```bash
# Restore uploads
rsync -av backup/storage/uploads/ \
    /var/www/bookstack/storage/uploads/

rsync -av backup/public/uploads/ \
    /var/www/bookstack/public/uploads/

# Restore permissions
chown -R www-data:www-data /var/www/bookstack/storage
chown -R www-data:www-data /var/www/bookstack/public/uploads
```

#### Restore Configuration

```bash
# Compare .env files
diff backup/.env /var/www/bookstack/.env

# Restore if needed (CAREFUL!)
cp backup/.env /var/www/bookstack/.env

# Verify configuration
cd /var/www/bookstack
php artisan config:clear
php artisan config:cache
```

---

## 📖 Chapter 4: Migration Procedures

### Procedure 1: In-Place Upgrade

**Use Case:** Upgrading BookStack version on same server

**Steps:**

1. **Enable maintenance mode:**
   ```bash
   cd /var/www/bookstack
   php artisan down --message="Upgrading BookStack"
   ```

2. **Create backup:**
   ```bash
   python3 /opt/migration/migrate.py
   # Option 4: Create Backup
   ```

3. **Pull updates:**
   ```bash
   git fetch --all
   git checkout v24.12.0  # or desired version
   ```

4. **Update dependencies:**
   ```bash
   composer install --no-dev
   npm install
   npm run production
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

6. **Clear caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

7. **Disable maintenance mode:**
   ```bash
   php artisan up
   ```

8. **Verify:**
   ```bash
   php artisan bookstack:version
   curl -I https://bookstack.example.com
   ```

### Procedure 2: Server Migration

**Use Case:** Moving BookStack to new server

**Source Server:**

1. **Create full backup:**
   ```bash
   python3 migrate.py
   # Option 4: Create Backup
   ```

2. **Export database:**
   ```bash
   mysqldump -h localhost -u bookstack -p \
       --single-transaction \
       bookstack | gzip > bookstack_db.sql.gz
   ```

3. **Archive files:**
   ```bash
   tar czf bookstack_files.tar.gz \
       /var/www/bookstack/storage/uploads \
       /var/www/bookstack/public/uploads \
       /var/www/bookstack/.env
   ```

4. **Transfer to destination:**
   ```bash
   scp bookstack_db.sql.gz user@newserver:/tmp/
   scp bookstack_files.tar.gz user@newserver:/tmp/
   ```

**Destination Server:**

1. **Install BookStack:**
   ```bash
   # Follow official installation guide
   git clone https://github.com/BookStackApp/BookStack.git --branch release --single-branch
   cd BookStack
   composer install --no-dev
   ```

2. **Restore database:**
   ```bash
   mysql -u bookstack -p bookstack < /tmp/bookstack_db.sql
   ```

3. **Restore files:**
   ```bash
   tar xzf /tmp/bookstack_files.tar.gz -C /
   ```

4. **Update configuration:**
   ```bash
   nano /var/www/bookstack/.env
   # Update:
   # - APP_URL
   # - DB_HOST (if changed)
   # - Other environment-specific settings
   ```

5. **Fix permissions:**
   ```bash
   chown -R www-data:www-data /var/www/bookstack
   chmod -R 755 /var/www/bookstack/storage
   chmod -R 755 /var/www/bookstack/bootstrap/cache
   chmod -R 755 /var/www/bookstack/public/uploads
   ```

6. **Run post-migration tasks:**
   ```bash
   php artisan migrate --force
   php artisan cache:clear
   php artisan key:generate  # Only if APP_KEY not set
   ```

7. **Test:**
   ```bash
   php artisan bookstack:test-environment
   ```

### Procedure 3: Platform Migration

**Use Case:** Migrating BookStack to different platform (e.g., DokuWiki)

1. **Create backup:**
   ```bash
   python3 migrate.py
   # Option 4: Create Backup
   ```

2. **Inspect database:**
   ```bash
   python3 migrate.py
   # Option 2: Inspect Database Schema
   ```

3. **Dry run export:**
   ```bash
   python3 migrate.py
   # Option 3: Dry Run Export
   ```

4. **Perform export:**
   ```bash
   python3 migrate.py
   # Option 5: Export to DokuWiki
   ```

5. **Verify export:**
   ```bash
   tree -L 3 dokuwiki_export
   find dokuwiki_export -name "*.txt" | wc -l
   ```

6. **Import to target platform:**
   ```bash
   # For DokuWiki:
   rsync -av dokuwiki_export/ \
       /var/www/dokuwiki/data/pages/
   
   # Rebuild search index:
   cd /var/www/dokuwiki
   php bin/indexer.php -c
   ```

---

## 📖 Chapter 5: Verification

### Database Integrity

```sql
-- Check table counts
SELECT COUNT(*) FROM entities WHERE type = 'page';
SELECT COUNT(*) FROM entities WHERE type = 'book';
SELECT COUNT(*) FROM entities WHERE type = 'chapter';

-- Verify relationships
SELECT 
    e.type,
    COUNT(*) as count
FROM entities e
WHERE deleted_at IS NULL
GROUP BY e.type;

-- Check orphaned pages
SELECT COUNT(*) 
FROM entities 
WHERE type = 'page' 
    AND book_id IS NULL;

-- Verify users
SELECT COUNT(*) FROM users WHERE deleted_at IS NULL;
```

### File Integrity

```bash
# Count uploaded files
find /var/www/bookstack/storage/uploads -type f | wc -l
find /var/www/bookstack/public/uploads -type f | wc -l

# Check permissions
ls -la /var/www/bookstack/storage/uploads
ls -la /var/www/bookstack/public/uploads

# Verify file sizes
du -sh /var/www/bookstack/storage/uploads
du -sh /var/www/bookstack/public/uploads
```

### Application Health

```bash
# Check environment
php artisan bookstack:test-environment

# Verify version
php artisan bookstack:version

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check queue workers (if used)
php artisan queue:monitor

# View recent logs
tail -100 /var/www/bookstack/storage/logs/laravel.log
```

### Post-Migration Testing

**Functional Tests:**
- [ ] Login with existing user
- [ ] Create new page
- [ ] Edit existing page
- [ ] Upload image
- [ ] Search functionality
- [ ] Export (PDF, HTML, text)
- [ ] User management
- [ ] Permission system

**Performance Tests:**
```bash
# Response time
curl -o /dev/null -s -w '%{time_total}\n' \
    https://bookstack.example.com

# Database query time
php artisan tinker --execute="
    \$start = microtime(true);
    \BookStack\Entities\Models\Page::count();
    echo microtime(true) - \$start;
"
```

---

## 📖 Chapter 6: Troubleshooting

### Issue: Migration Failed

**Symptoms:**
- Incomplete data transfer
- Missing pages or books
- Database errors

**Diagnosis:**
```bash
# Check migration logs
cat migration_logs/migration_*.log | grep -i error

# Verify database state
mysql -u bookstack -p bookstack -e "SHOW TABLES;"

# Check file transfers
diff -r backup/storage/uploads /var/www/bookstack/storage/uploads
```

**Solutions:**
1. Restore from backup
2. Re-run migration with verbose logging
3. Check database constraints and foreign keys
4. Verify disk space and permissions

### Issue: Performance Degradation

**Symptoms:**
- Slow page loads
- Timeouts
- High database CPU

**Diagnosis:**
```bash
# Check database size
mysql -u bookstack -p -e "
    SELECT 
        table_name,
        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
    FROM information_schema.TABLES 
    WHERE table_schema = 'bookstack'
    ORDER BY (data_length + index_length) DESC;
"

# Monitor queries
mysql -u bookstack -p -e "SHOW PROCESSLIST;"

# Check PHP-FPM
service php8.2-fpm status
```

**Solutions:**
```bash
# Optimize tables
mysql -u bookstack -p bookstack -e "OPTIMIZE TABLE search_terms;"

# Clear application caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild search index
php artisan bookstack:regenerate-search

# Increase PHP memory
# In .env: APP_MEMORY_LIMIT=512M
```

### Issue: Missing Content

**Symptoms:**
- Some pages not visible
- Images broken
- Formatting lost

**Diagnosis:**
```bash
# Check for soft-deletes
mysql -u bookstack -p bookstack -e "
    SELECT COUNT(*) FROM entities WHERE deleted_at IS NOT NULL;
"

# Verify file references
mysql -u bookstack -p bookstack -e "
    SELECT COUNT(*) FROM images;
"

# Check orphaned records
mysql -u bookstack -p bookstack -e "
    SELECT COUNT(*) FROM entity_page_data epd
    LEFT JOIN entities e ON e.id = epd.page_id
    WHERE e.id IS NULL;
"
```

**Solutions:**
1. Restore specific content from backup
2. Recover soft-deleted items
3. Re-upload missing files
4. Run content regeneration commands

---

## 🎯 Quick Reference

### Command Cheat Sheet

```bash
# Backup
python3 migrate.py  # Option 4
mysqldump [options] bookstack > backup.sql

# Restore
mysql [options] bookstack < backup.sql

# Migrate
php artisan migrate --force

# Verify
php artisan bookstack:test-environment

# Troubleshoot
tail -f storage/logs/laravel.log
```

### Critical Files

| File/Dir | Purpose |
|----------|---------|
| `database.sql` | Database backup |
| `storage/uploads/` | User uploads |
| `public/uploads/` | Public files |
| `.env` | Configuration |
| `migration_logs/` | Migration logs |

---

[← Back to Migration](./README.md) | [DokuWiki Export →](./dokuwiki-export.md)
