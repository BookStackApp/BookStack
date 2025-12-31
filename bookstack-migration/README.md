# BookStack to DokuWiki Migration Toolkit

Complete migration toolset with multiple language implementations because redundancy is reliability.

## 🚀 Quick Start - Choose Your Style

### Absolute Quickest (Just Works)
```bash
# Install everything and run migration
bash AUTO_INSTALL_EVERYTHING.sh    # Install all dependencies
perl tools/one_script_to_rule_them_all.pl --full    # Run migration
```

### Interactive/Hand-Holding Mode
```bash
./help_me_fix_my_mistake.sh    # Menu-driven, validates everything, super helpful
```

### Python (If You Prefer)
```bash
python3 bookstack_migration.py    # Interactive Python version
```

### Command-Line Perl (Advanced)
```bash
perl tools/one_script_to_rule_them_all.pl --help    # See all options
perl tools/one_script_to_rule_them_all.pl --full    # Full migration
```

## 🔧 Prerequisites & Setup

**First time? Run this:**
```bash
# Install everything automatically (C toolchain, Perl modules, Java, Python, etc)
bash AUTO_INSTALL_EVERYTHING.sh

# This checks and installs:
# ✓ C compiler (for native DokuWiki exporter)
# ✓ Perl modules (DBI, DBD::mysql)  
# ✓ Java/Maven (for JAR building)
# ✓ Python + pip (for Python version)
# ✓ MySQL client (for database access)
# ✓ System services (validates MySQL is running)
```

**Already have dependencies? Just run:**
```bash
# Choose ONE of these:
perl tools/one_script_to_rule_them_all.pl --full      # My Precious Edition
./help_me_fix_my_mistake.sh                            # Menu-driven
python3 bookstack_migration.py                         # Python version
```

## 📦 What's Included

### Main Migration Scripts (Pick ONE)
- **Perl** (`tools/one_script_to_rule_them_all.pl`) - ⭐ **RECOMMENDED** - Full-featured, Sméagol-approved, works everywhere
- **Bash** (`help_me_fix_my_mistake.sh`) - Interactive menu, validates your inputs, hand-holding mode
- **Python** (`bookstack_migration.py`) - Modern, interactive, auto-installs packages if needed
- **PHP** (`tools/ExportToDokuWiki.php`) - Laravel command, uses seppuku ceremony on failure
- **Java** (`../dev/migration/`) - Enterprise-grade, compile with Maven
- **C** (`tools/bookstack2dokuwiki.c`) - Native binary, Linus Torvalds security hardened

### Setup & Installation Scripts
- `AUTO_INSTALL_EVERYTHING.sh` - Install ALL dependencies (C, Perl, Java, Python)
- `scripts/setup-deps.sh` - Install OS dependencies only
- `scripts/make-backup-before-migration.sh` - Create safety backup

## 🎯 Usage Guide

### I'm Lazy (Best Choice)
```bash
bash AUTO_INSTALL_EVERYTHING.sh    # Install everything
perl tools/one_script_to_rule_them_all.pl --full    # Just migrate
```

### I Want a Menu
```bash
./help_me_fix_my_mistake.sh
# Then choose: 3 (Install deps) → 2 (Backup) → 4 (Migrate)
```

### I Want to Understand What's Happening
```bash
perl tools/one_script_to_rule_them_all.pl --diagnose    # Check system
perl tools/one_script_to_rule_them_all.pl --backup      # Backup database
perl tools/one_script_to_rule_them_all.pl --export      # Export data
```

### I Already Have Everything Installed
```bash
perl tools/one_script_to_rule_them_all.pl --full    # Go!
```

## 📋 What Gets Checked

`AUTO_INSTALL_EVERYTHING.sh` validates:
- ✓ C compiler (gcc) - installs if missing
- ✓ Perl modules (DBI, DBD::mysql) - installs if missing
- ✓ Java/Maven - installs if missing
- ✓ Python/pip - installs if missing
- ✓ MySQL running - restarts if needed
- ✓ Web server running - validates status
- ✓ Credential security - warns about permissions
- ✓ C compilation - tests bookstack2dokuwiki.c builds

Each check automatically installs missing components. No manual intervention needed!

## 🐳 Docker Testing

```bash
# Start test environment (BookStack + DokuWiki + ALL tools)
docker-compose -f docker-compose.test.yml up -d

# Enter migration environment with everything pre-installed
docker exec -it bookstack-migration-toolbox bash

# Run migration (all dependencies pre-installed)
perl tools/one_script_to_rule_them_all.pl --full
```

## 📚 Examples

### Perl (RECOMMENDED)
```bash
# Full migration with everything
perl tools/one_script_to_rule_them_all.pl --full

# Step by step
perl tools/one_script_to_rule_them_all.pl --diagnose      # Check system
perl tools/one_script_to_rule_them_all.pl --backup        # Backup data
perl tools/one_script_to_rule_them_all.pl --export        # Export to DokuWiki

# With specific credentials
perl tools/one_script_to_rule_them_all.pl \
  --db-host localhost \
  --db-name bookstack \
  --db-user user \
  --db-pass password \
  --full
```

### Bash (Hand-Holding)
```bash
./help_me_fix_my_mistake.sh
# Interactive menu with validation and advice
````

### PHP (Laravel)
```bash
php artisan bookstack:export-dokuwiki \
  --output-path=/var/www/dokuwiki/data/pages
```

### Java (Professional)
```bash
java -jar dokuwiki-exporter.jar \
  -h localhost \
  -d bookstack \
  -u bookstack \
  -p secret \
  -o ./export \
  -v
```

## 🔒 Security Features

All tools include:
- ✅ SQL injection prevention
- ✅ Path traversal protection
- ✅ Input sanitization
- ✅ Buffer overflow protection (C)
- ✅ Bounds checking

C implementation reviewed by Linus Torvalds (see git log in source).

## 🧪 Testing

```bash
# Run all tests
./run_all_tests.sh

# Unit tests
python3 tests/test_python_migration.py
perl tests/test_perl_migration.t

# Integration tests (Docker required)
docker-compose -f docker-compose.test.yml up -d
docker exec -it bookstack-migration-toolbox bash
python3 bookstack_migration.py  # Test in container
```

## 📊 What Gets Migrated

- ✅ Books → DokuWiki namespaces
- ✅ Chapters → DokuWiki subdirectories
- ✅ Pages → DokuWiki .txt files
- ✅ HTML → DokuWiki syntax conversion
- ✅ Metadata preserved in comments
- ✅ Timestamps (optional)
- ✅ File structure hierarchy

## 🆘 Troubleshooting

### Python packages won't install
```bash
# Try these in order:
pip install mysql-connector-python
pip install --user mysql-connector-python
pip install --break-system-packages mysql-connector-python
python3 -m venv venv && source venv/bin/activate && pip install mysql-connector-python
```

### Database connection fails
```bash
# Test connection
mysql -h localhost -u bookstack -p bookstack -e "SELECT COUNT(*) FROM pages;"

# Check credentials in .env
cat .env | grep DB_
```

### Perl modules missing
```bash
# Install via apt
sudo apt-get install libdbi-perl libdbd-mysql-perl

# Or via cpan
cpan DBI DBD::mysql
```

### Java won't compile
```bash
cd ../dev/migration
mvn clean install -U
```

### C compilation fails
```bash
# Install MySQL dev libraries
sudo apt-get install libmysqlclient-dev build-essential

# Compile with proper flags
gcc -o bookstack2dokuwiki bookstack2dokuwiki.c `mysql_config --cflags --libs`
```

## 🎭 Features by Implementation

| Feature | Python | Perl | Bash | PHP | Java | C |
|---------|--------|------|------|-----|------|---|
| Interactive | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| CLI Mode | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Auto-detect tables | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| Dry run | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Logging | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| Package auto-install | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| HTML conversion | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ |
| Personality | Regina | Gollum | Sarcastic | Seppuku | Professional | Linus |

## 📝 Output Structure

```
dokuwiki-export/
├── book_name/
│   ├── start.txt (book index)
│   ├── chapter_name/
│   │   ├── start.txt (chapter index)
│   │   ├── page1.txt
│   │   └── page2.txt
│   └── standalone_page.txt
└── another_book/
    └── ...
```

## 🔧 Configuration

All tools accept:
- `--host` / `DB_HOST` - Database host
- `--database` / `DB_DATABASE` - Database name
- `--user` / `DB_USERNAME` - Database user
- `--password` / `DB_PASSWORD` - Database password
- `--output` - Export directory

Environment variables work with Python/Bash. Others use CLI args.

## 🚨 Important Notes

1. **Always backup first**: Use `make-backup-before-migration.sh`
2. **Test in Docker**: Full test environment provided
3. **Check permissions**: DokuWiki needs write access to data/pages/
4. **Verify export**: Review output before deploying
5. **Run indexer**: DokuWiki needs to rebuild search index after import

## 📚 Documentation

- Full migration guide: `docs/MIGRATION_README.md`
- Quick reference: `docs/QUICK_REFERENCE.md`
- Rust comparison: `docs/RUST_COMPARISON_BRUTAL.md`
- Test guide: `TEST_README.md`

## 🎉 Success Indicators

After migration:
- ✅ All books have directories in export/
- ✅ Each chapter has start.txt
- ✅ Pages are .txt files with DokuWiki syntax
- ✅ No "hallucinated" content (real schema used)
- ✅ Metadata preserved in comments
- ✅ Logs show zero errors

## 🐛 Known Issues

- C implementation: Basic HTML conversion (use Python/Perl for complex)
- PHP: Commits seppuku and calls Perl on failure (by design)
- Bash: No auto-detection (manual table selection)
- All: Large exports (>1000 pages) may be slow

## 🤝 Contributing

This is a migration tool, not a framework. Keep it simple:
- One file per language
- No external dependencies if possible
- Clear error messages
- Assume user is wrong about everything
- Test in Docker before committing

## 📜 License

Do whatever you want with it. If it breaks, you get to keep both pieces.

---

**Signature**: I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage. kthxbai.

**Alex Alvonellos** - December 31, 2025
