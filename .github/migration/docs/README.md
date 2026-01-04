# BookStack to DokuWiki Migration Guide

**Complete migration toolset with comprehensive stage-based workflow**

## Table of Contents

- [Quick Start](#quick-start)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Stage-Based Workflow](#stage-based-workflow)
- [Tool Selection Guide](#tool-selection-guide)
- [Troubleshooting](#troubleshooting)
- [Advanced Usage](#advanced-usage)
- [Additional Documentation](#additional-documentation)

---

## Quick Start

### The Fastest Way (Recommended)

```bash
# 1. Install all dependencies automatically
.github/migration/stages/01-setup.sh

# 2. Create a backup
.github/migration/stages/02-backup.sh

# 3. Export your data
.github/migration/stages/03-export.sh

# 4. Validate the export
.github/migration/stages/04-validate.sh
```

### Interactive Mode (Hand-Holding)

```bash
# Menu-driven interface with validation
.github/migration/tools/perl/one_script_to_rule_them_all.pl --interactive
```

### Single Command (Advanced)

```bash
# Run full migration in one go
.github/migration/tools/perl/one_script_to_rule_them_all.pl --full
```

---

## Prerequisites

### System Requirements

- **Operating System**: Linux/Unix (Windows requires WSL)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Disk Space**: At least 2x your BookStack database size
- **Memory**: Minimum 1GB available RAM

### Required Software

The setup script (`01-setup.sh`) will automatically install these if missing:

- **C Compiler**: gcc or clang (for native tools)
- **Perl**: 5.10+ with DBI and DBD::mysql modules
- **MySQL Client**: For database access
- **Python**: 3.6+ with pip (optional, for Python tools)
- **Java**: JRE 11+ and Maven (optional, for Java tools)

### Permissions

- Database read access (SELECT on all BookStack tables)
- Write access to export directory
- Optional: Backup directory write access

---

## Installation

### Automatic Installation (Recommended)

```bash
# This checks your system and installs everything needed
.github/migration/stages/01-setup.sh

# The script will:
# ✓ Detect your OS and architecture
# ✓ Install missing compilers and build tools
# ✓ Install Perl modules (DBI, DBD::mysql)
# ✓ Install Python packages (if using Python tools)
# ✓ Verify MySQL/MariaDB is running
# ✓ Test database connectivity
# ✓ Compile native tools
# ✓ Validate all components
```

### Manual Installation

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install -y \
    gcc make \
    perl libdbi-perl libdbd-mysql-perl \
    mysql-client \
    python3 python3-pip \
    default-jre maven
```

**macOS (with Homebrew):**
```bash
brew install gcc perl mysql-client python3 openjdk maven
cpan install DBI DBD::mysql
```

**Verify Installation:**
```bash
.github/migration/stages/01-setup.sh --check
```

---

## Stage-Based Workflow

The migration process is divided into four clear stages for reliability and maintainability.

### Stage 1: Setup (`01-setup.sh`)

**Purpose**: Prepare your system with all required dependencies.

```bash
.github/migration/stages/01-setup.sh

# Options:
--check          # Verify installation without installing
--skip-compile   # Skip compiling native tools
--dry-run        # Show what would be installed
```

**What it does:**
- Detects your operating system and architecture
- Checks for and installs missing system packages
- Installs Perl modules via CPAN
- Installs Python packages via pip
- Compiles native C tools
- Validates MySQL/MariaDB connectivity
- Tests database credentials
- Generates installation report

**Output:**
```
✓ Operating System: Ubuntu 24.04 LTS
✓ Architecture: x86_64
✓ C Compiler: gcc 11.4.0
✓ Perl: 5.34.0
✓ Perl DBI: 1.643
✓ Perl DBD::mysql: 4.050
✓ MySQL Client: 8.0.35
✓ Python: 3.10.12
✓ Java: OpenJDK 11.0.20
✓ Database Connection: SUCCESS
✓ Native Tools Compiled: SUCCESS

All prerequisites satisfied. Ready for migration.
```

---

### Stage 2: Backup (`02-backup.sh`)

**Purpose**: Create comprehensive backups before migration.

```bash
.github/migration/stages/02-backup.sh

# Options:
--output-dir /path/to/backups   # Custom backup location
--skip-database                 # Skip database backup
--skip-uploads                  # Skip file uploads backup
--compress                      # Compress backups
```

**What it backs up:**
1. **Database**: Complete SQL dump with structure and data
2. **Configuration**: .env files and configs
3. **Uploads**: Storage files and attachments
4. **Metadata**: Migration timestamp and system info

**Backup structure:**
```
backups/
└── bookstack-backup-20260104-153045/
    ├── database/
    │   ├── bookstack-full.sql
    │   └── bookstack-full.sql.sha256
    ├── config/
    │   ├── .env
    │   └── config-backup.json
    ├── uploads/
    │   └── storage-uploads.tar.gz
    ├── RESTORE_INSTRUCTIONS.txt
    └── backup-manifest.json
```

**Validation:**
- SHA256 checksums for all files
- SQL dump integrity test
- Restore instructions generated

**Time estimate**: 2-10 minutes (depends on database size)

---

### Stage 3: Export (`03-export.sh`)

**Purpose**: Extract BookStack data and convert to DokuWiki format.

```bash
.github/migration/stages/03-export.sh

# Options:
--db-host localhost             # Database hostname
--db-name bookstack             # Database name
--db-user bookstack_user        # Database username
--db-pass secret_password       # Database password
--output-dir ./export           # Export directory
--tool perl                     # Tool to use (perl/python/java/c)
--validate                      # Enable validation
--verbose                       # Detailed output
```

**What it extracts:**

1. **Books** → DokuWiki namespaces
   - Book metadata preserved in comments
   - Hierarchy maintained

2. **Chapters** → DokuWiki subdirectories
   - Chapter descriptions → start.txt files
   - Proper namespace structure

3. **Pages** → DokuWiki text files
   - HTML → DokuWiki syntax conversion
   - Metadata comments at top of files
   - Proper file naming (lowercase, no spaces)

4. **Relationships** preserved
   - Parent-child relationships
   - Ordering information
   - Cross-references

**Conversion examples:**

*HTML → DokuWiki:*
```html
<!-- Input: BookStack HTML -->
<h1>Chapter Title</h1>
<p>Some <strong>bold</strong> and <em>italic</em> text.</p>
<ul>
  <li>Item 1</li>
  <li>Item 2</li>
</ul>
```

```dokuwiki
<!-- Output: DokuWiki syntax -->
====== Chapter Title ======

Some **bold** and //italic// text.

  * Item 1
  * Item 2
```

**Output structure:**
```
export/
├── general_knowledge/
│   ├── start.txt           # Book index
│   ├── getting_started/
│   │   ├── start.txt       # Chapter index
│   │   ├── introduction.txt
│   │   └── first_steps.txt
│   └── advanced_topics.txt
└── technical_docs/
    └── ...
```

**Performance:**
- Perl: ~1000 pages/minute
- Python: ~800 pages/minute
- Java: ~300 pages/minute (with JVM startup)
- C: ~2000 pages/minute

**Time estimate**: 1-30 minutes (depends on data size and tool)

---

### Stage 4: Validate (`04-validate.sh`)

**Purpose**: Verify export completeness and integrity.

```bash
.github/migration/stages/04-validate.sh

# Options:
--export-dir ./export           # Directory to validate
--strict                        # Enable strict validation
--report validation-report.txt  # Save report to file
```

**Validation checks:**

1. **Completeness**
   - Compare record counts (DB vs export)
   - Verify all books exported
   - Check all chapters present
   - Ensure no missing pages

2. **File Integrity**
   - SHA256 checksums
   - File size validation
   - Proper UTF-8 encoding
   - Valid DokuWiki syntax

3. **Structure**
   - Namespace hierarchy correct
   - File naming conventions followed
   - start.txt files present
   - No forbidden characters

4. **Content**
   - HTML conversion quality
   - No truncated files
   - Metadata preservation
   - Character encoding issues

**Sample report:**
```
================================
VALIDATION REPORT
================================
Generated: 2026-01-04 15:45:22

DATABASE RECORDS:
  Books:    12
  Chapters: 45
  Pages:    892

EXPORTED FILES:
  Books:    12 ✓
  Chapters: 45 ✓
  Pages:    892 ✓

FILE INTEGRITY:
  Total files:     892
  Valid syntax:    892 ✓
  Valid UTF-8:     892 ✓
  Checksums match: 892 ✓

ISSUES FOUND: 0

STATUS: ✓ PASSED
All data successfully exported and validated.
```

**Time estimate**: 1-5 minutes

---

## Tool Selection Guide

We provide **five** independent implementations. Choose based on your needs:

### 1. Perl (⭐ **RECOMMENDED**)

**Best for**: Most users, production migrations

**Pros:**
- Most reliable and battle-tested
- Fast performance
- Excellent error handling
- MD5/SHA256 validation built-in
- Works everywhere (Perl is universal)
- Minimal dependencies

**Cons:**
- Need to install Perl modules (DBI, DBD::mysql)
- Less familiar to modern developers

**Location**: `.github/migration/tools/perl/one_script_to_rule_them_all.pl`

**Usage:**
```bash
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl \
    --db-host localhost \
    --db-name bookstack \
    --db-user root \
    --db-pass password \
    --full
```

---

### 2. Python

**Best for**: Python developers, modern environments

**Pros:**
- Readable, maintainable code
- Good error messages
- Interactive mode with prompts
- Auto-installs packages if needed
- Familiar to most developers

**Cons:**
- Slower than Perl/C
- Larger dependency footprint
- May have environment issues

**Location**: `.github/migration/tools/python/bookstack_migration.py`

**Usage:**
```bash
python3 .github/migration/tools/python/bookstack_migration.py
# Interactive mode with prompts
```

---

### 3. Java

**Best for**: Enterprise environments, when reliability > speed

**Pros:**
- Type-safe, robust
- Good for large datasets
- Professional error handling
- Comprehensive logging

**Cons:**
- Very slow (JVM startup overhead)
- Requires Maven to compile
- Large memory footprint
- Overkill for most migrations

**Location**: `.github/migration/tools/java/`

**Usage:**
```bash
cd .github/migration/tools/java
mvn clean package
java -jar target/bookstack-exporter.jar \
    --db-name bookstack \
    --db-user root \
    --db-pass password \
    --output ./export
```

---

### 4. C (Native Binary)

**Best for**: Speed, minimal dependencies, large migrations

**Pros:**
- Extremely fast (~2000 pages/minute)
- Tiny binary size
- No runtime dependencies
- Minimal memory usage
- Security-hardened

**Cons:**
- Needs compilation
- Less user-friendly errors
- Basic HTML conversion
- Requires MySQL development libraries

**Location**: `.github/migration/tools/c/bookstack2dokuwiki.c`

**Usage:**
```bash
# Compile (done by 01-setup.sh)
gcc -o bookstack2dokuwiki bookstack2dokuwiki.c `mysql_config --cflags --libs`

# Run
./bookstack2dokuwiki \
    --db-host localhost \
    --db-name bookstack \
    --db-user root \
    --db-pass password \
    --output ./export
```

---

### 5. PHP (Laravel Command)

**Best for**: When you need BookStack internals access

**Pros:**
- Direct access to Laravel models
- Uses BookStack's own database abstraction
- Understands BookStack internals

**Cons:**
- Requires BookStack environment
- Less portable
- Slower than standalone tools
- Framework overhead

**Location**: `.github/migration/tools/php/ExportToDokuWiki.php`

**Usage:**
```bash
cd /path/to/bookstack
php artisan bookstack:export-dokuwiki --output-path=./export
```

---

### Comparison Table

| Feature | Perl | Python | Java | C | PHP |
|---------|------|--------|------|---|-----|
| **Speed** | Fast | Medium | Slow | Very Fast | Medium |
| **Reliability** | ★★★★★ | ★★★★☆ | ★★★★★ | ★★★★☆ | ★★★☆☆ |
| **Setup** | Easy | Easy | Complex | Medium | Easy |
| **Portability** | ★★★★★ | ★★★★☆ | ★★★☆☆ | ★★★☆☆ | ★★☆☆☆ |
| **Error Messages** | Excellent | Good | Verbose | Basic | Fair |
| **Memory Usage** | Low | Medium | High | Very Low | Medium |
| **Dependencies** | 2 modules | Several | Many | None | Framework |
| **Binary Size** | ~20KB | ~5MB | ~50MB | ~30KB | N/A |

**Recommendation by use case:**
- **General use**: Perl
- **Large migrations**: C
- **Enterprise**: Java
- **Python shops**: Python
- **BookStack dev**: PHP

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Fails

**Symptoms:**
```
ERROR: Can't connect to MySQL server on 'localhost'
```

**Solutions:**
```bash
# Check MySQL is running
systemctl status mysql
sudo systemctl start mysql

# Test connection manually
mysql -h localhost -u bookstack -p bookstack

# Verify credentials in .env
cat .env | grep DB_

# Check MySQL is listening
netstat -tlnp | grep 3306
```

---

#### 2. Perl Modules Missing

**Symptoms:**
```
Can't locate DBI.pm in @INC
```

**Solutions:**
```bash
# Ubuntu/Debian
sudo apt-get install libdbi-perl libdbd-mysql-perl

# macOS
cpan install DBI DBD::mysql

# Manual CPAN
perl -MCPAN -e 'install DBI'
perl -MCPAN -e 'install DBD::mysql'
```

---

#### 3. Permission Denied on Export Directory

**Symptoms:**
```
ERROR: Cannot write to ./export/
```

**Solutions:**
```bash
# Create directory with proper permissions
mkdir -p ./export
chmod 755 ./export

# Or use a different directory
.github/migration/stages/03-export.sh --output-dir /tmp/export
```

---

#### 4. HTML Conversion Issues

**Symptoms:**
- Garbled characters
- Missing formatting
- Broken links

**Solutions:**
```bash
# Use Perl tool (best HTML conversion)
.github/migration/stages/03-export.sh --tool perl

# Enable verbose mode to see conversion
.github/migration/stages/03-export.sh --verbose

# Check for UTF-8 issues
file export/book_name/page.txt
# Should show: UTF-8 Unicode text
```

---

#### 5. Java Out of Memory

**Symptoms:**
```
java.lang.OutOfMemoryError: Java heap space
```

**Solutions:**
```bash
# Increase heap size
java -Xmx2G -jar target/bookstack-exporter.jar ...

# Or use a different tool (Perl/C)
.github/migration/stages/03-export.sh --tool perl
```

---

#### 6. Validation Fails

**Symptoms:**
```
VALIDATION FAILED: 10 pages missing
```

**Solutions:**
```bash
# Run export again with validation
.github/migration/stages/03-export.sh --validate

# Check for specific issues
.github/migration/stages/04-validate.sh --strict

# Compare record counts manually
mysql -u bookstack -p -e "SELECT COUNT(*) FROM pages;" bookstack
find export/ -name "*.txt" | wc -l
```

---

### Getting Help

#### Generate Diagnostic Report

```bash
# Create comprehensive diagnostic
.github/migration/tools/perl/one_script_to_rule_them_all.pl --diagnose

# This generates a report with:
# - System information
# - Installed software versions
# - Database connectivity status
# - Recent errors
# - Suggested fixes
```

#### Ask AI for Help

1. Generate diagnostic: `--diagnose`
2. Copy the output
3. Ask ChatGPT or Claude:
   > "I'm migrating BookStack to DokuWiki and getting this error. Here's my diagnostic report: [paste]"
4. Follow the exact commands provided

---

## Advanced Usage

### Custom Database Configuration

```bash
# Non-standard port
.github/migration/stages/03-export.sh \
    --db-host localhost:3307 \
    --db-name bookstack \
    --db-user admin \
    --db-pass 'complex!password' \
    --db-socket /var/run/mysqld/mysqld.sock
```

### Selective Export

```bash
# Export only specific books
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl \
    --books "Technical Docs,User Guide" \
    --output ./export

# Export with filters
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl \
    --exclude-drafts \
    --only-published \
    --output ./export
```

### Docker Testing Environment

```bash
# Start test environment
docker-compose -f docker-compose.test.yml up -d

# Run migration in container
docker exec -it bookstack-migration bash
cd /workspace
.github/migration/stages/03-export.sh
```

### Parallel Processing

```bash
# Export using multiple processes (Perl only)
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl \
    --parallel 4 \
    --output ./export
```

### Custom Output Format

```bash
# Include metadata in separate files
.github/migration/stages/03-export.sh \
    --metadata-separate \
    --include-timestamps \
    --preserve-ids

# Generate migration manifest
.github/migration/stages/03-export.sh \
    --generate-manifest \
    --output ./export
```

---

## Post-Migration Steps

### 1. Install DokuWiki

```bash
# Download
wget https://download.dokuwiki.org/src/dokuwiki/dokuwiki-stable.tgz
tar -xzf dokuwiki-stable.tgz
mv dokuwiki-* /var/www/dokuwiki

# Set permissions
sudo chown -R www-data:www-data /var/www/dokuwiki
sudo chmod -R 755 /var/www/dokuwiki
```

### 2. Import Data

```bash
# Copy exported pages
cp -r export/* /var/www/dokuwiki/data/pages/

# Fix permissions
sudo chown -R www-data:www-data /var/www/dokuwiki/data/pages
sudo chmod -R 775 /var/www/dokuwiki/data/pages
```

### 3. Rebuild Search Index

```bash
# Via command line
cd /var/www/dokuwiki
sudo -u www-data php bin/indexer.php -c

# Or via web interface
# Visit: http://yoursite.com/doku.php?do=index
```

### 4. Configure Web Server

See [GUIDE.md](GUIDE.md) for Apache/Nginx configuration examples.

---

## Additional Documentation

- **[GUIDE.md](GUIDE.md)**: Detailed step-by-step migration guide
- **[TOOLS.md](TOOLS.md)**: In-depth comparison of all five tools
- **[ARCHITECTURE.md](ARCHITECTURE.md)**: Technical architecture and design decisions
- **[TEST.md](../tests/README.md)**: Testing strategy and test suite

---

## Success Indicators

After migration, you should see:

- ✅ All books have directories in export/
- ✅ Each chapter has a start.txt file
- ✅ Pages are properly formatted .txt files
- ✅ Validation report shows zero errors
- ✅ Record counts match (database vs export)
- ✅ DokuWiki can read all pages
- ✅ Search index rebuilt successfully

---

## Support

### Before Asking for Help

1. Run diagnostic: `--diagnose`
2. Check error logs
3. Verify database connectivity
4. Try Perl tool (most reliable)
5. Read [GUIDE.md](GUIDE.md)

### Community Resources

- GitHub Issues: [BookStack Repository]
- Documentation: This guide and linked docs
- AI Assistance: ChatGPT, Claude (with diagnostic report)

---

## License

This migration toolkit is provided as-is. Use at your own risk. If it breaks, you get to keep both pieces.

---

**Developed with care for BookStack users migrating to DokuWiki.**

*Documentation last updated: January 4, 2026*
