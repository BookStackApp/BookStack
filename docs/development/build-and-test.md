# Build & Test Procedures

> Complete guide to building, testing, and verifying BookStack

---

## 📖 Chapter 1: Build Process

### Production Build

Build all frontend assets for production:

```bash
# Install dependencies
composer install
npm install

# Build production assets
npm run production
```

**Output:**
- `public/dist/app.js` (196 KB)
- `public/dist/code.js` (655 KB)
- `public/dist/wysiwyg.js` (309 KB)
- `public/dist/markdown.js` (182 KB)
- `public/dist/styles.css` (minified)

### Development Build

Watch mode for active development:

```bash
# Watch both JS and CSS
npm run dev

# Watch JS only
npm run build:js:watch

# Watch CSS only
npm run build:css:watch
```

---

## 📖 Chapter 2: PHP Configuration

### Required Extensions

BookStack requires these PHP extensions:

- **GD** - Image processing
- **MySQL PDO** - Database connectivity
- **MySQLi** - Alternative MySQL driver
- **mbstring** - Multi-byte string support
- **XML** - XML processing
- **curl** - HTTP requests
- **zip** - Archive handling

### Installation (Ubuntu/Debian)

```bash
# Add PHP PPA
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install extensions
sudo apt install php8.2-gd php8.2-mysql php8.2-zip \
                 php8.2-mbstring php8.2-xml php8.2-curl
```

### Codespace Configuration

For GitHub Codespaces, symlink extensions to the PHP installation:

```bash
sudo ln -s /usr/lib/php/20220829/gd.so /opt/php/8.2.14/lib/php/extensions/
sudo ln -s /usr/lib/php/20220829/pdo_mysql.so /opt/php/8.2.14/lib/php/extensions/
sudo ln -s /usr/lib/php/20220829/mysqli.so /opt/php/8.2.14/lib/php/extensions/
```

Add to `/opt/php/8.2.14/ini/php.ini`:
```ini
extension=gd.so
extension=pdo_mysql.so
extension=mysqli.so
```

---

## 📖 Chapter 3: Database Setup

### Test Database

Start MySQL container:

```bash
docker-compose up -d db
```

**Configuration:**
- Host: `localhost:3306`
- Database: `bookstack-test`
- User: `bookstack-test`
- Password: `bookstack-test`

### Migrations & Seeding

```bash
# Run migrations
DB_CONNECTION=mysql_testing php artisan migrate

# Seed test data
DB_CONNECTION=mysql_testing php artisan db:seed --class=DummyContentSeeder

# Quick reset & seed
composer refresh-test-database
```

---

## 📖 Chapter 4: Running Tests

### PHPUnit Tests

```bash
# All tests
composer test

# Specific test file
./vendor/bin/phpunit tests/Exports/TextExportTest.php

# Specific test method
./vendor/bin/phpunit --filter testPageTextExport
```

### Text Export Tests

**Test Suite:** `tests/Exports/TextExportTest.php`

✅ **All 5 tests passing:**
1. `testPageTextExport` - Page content export
2. `testBookTextExport` - Book compilation
3. `testBookTextExportFormat` - Book formatting
4. `testChapterTextExport` - Chapter compilation
5. `testChapterTextExportFormat` - Chapter formatting

**Assertions:** 21 total
- Content properly exported to plain text
- HTML stripped correctly
- Proper file headers and encoding
- UTF-8 byte-order-mark handling

### Linting & Static Analysis

```bash
# PHP CodeSniffer
composer lint

# Auto-fix formatting
composer format

# PHPStan static analysis
composer check-static

# ESLint (JavaScript)
npm run lint

# Jest tests
npm run test
```

---

## 📖 Chapter 5: Test Results

### Latest Test Execution

**Date:** January 6, 2026  
**Duration:** 3.5 seconds  
**Results:** ✅ 5/5 tests passed (21 assertions)

```
PHPUnit 11.5.6

TextExportTest
 ✔ Page text export (1127 ms)
 ✔ Book text export (469 ms)
 ✔ Book text export format (475 ms)
 ✔ Chapter text export (447 ms)
 ✔ Chapter text export format (462 ms)

Tests: 5 passed (21 assertions)
Time: 3.50s
```

### Build Status

| Component | Status | Details |
|-----------|--------|---------|
| Frontend Assets | ✅ Built | 5 files, 1.5 MB total |
| PHP Extensions | ✅ Installed | GD, MySQL, zip |
| Database | ✅ Running | MySQL 8.4 on :3306 |
| Migrations | ✅ Complete | 100+ migrations |
| Test Data | ✅ Seeded | 260 pages, 65 chapters, 6 books |
| Unit Tests | ✅ Passing | 5/5 text export tests |

---

## 📖 Chapter 6: Troubleshooting

### Common Issues

#### OpenSSL Version Mismatch

**Symptom:** `symbol OPENSSL_1_1_1 not found`

**Solution:**
```bash
# Download OpenSSL 1.1.1
wget http://archive.ubuntu.com/ubuntu/pool/main/o/openssl/libssl1.1_1.1.1f-1ubuntu2.24_amd64.deb
sudo dpkg -i libssl1.1_1.1.1f-1ubuntu2.24_amd64.deb
```

#### Database Connection Failed

**Symptom:** `Connection refused` or `No such file or directory`

**Solutions:**
```bash
# Check container status
docker ps | grep bookstack

# View logs
docker logs bookstack-db-1

# Ensure port binding
# In docker-compose.yml, under db.ports: "3306:3306"

# Use correct connection string
DB_CONNECTION=mysql_testing php artisan migrate
```

#### Missing PHP Extensions

**Symptom:** `Call to undefined function` or extension errors

**Solution:**
```bash
# Check loaded extensions
php -m | grep -E 'gd|mysql|zip'

# Install missing extensions
sudo apt install php8.2-{gd,mysql,zip,mbstring,xml}

# For Codespaces, symlink to correct location
sudo ln -s /usr/lib/php/20220829/*.so /opt/php/8.2.14/lib/php/extensions/
```

---

## 🎯 Quick Reference

### Essential Commands

```bash
# Full build
composer install && npm install && npm run production

# Test everything
composer test

# Reset test DB
composer refresh-test-database

# Start services
docker-compose up -d

# View logs
docker-compose logs -f
```

### File Locations

- **Assets:** `public/dist/`
- **Tests:** `tests/`
- **Logs:** `storage/logs/`, `migration_logs/`
- **Config:** `phpunit.xml`, `docker-compose.yml`
- **Database:** Running on `localhost:3306`

---

[← Back to Development](./README.md) | [Architecture →](./architecture.md)
