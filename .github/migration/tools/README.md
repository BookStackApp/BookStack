# BookStack Migration Tools

This directory contains migration tools organized by programming language. Each tool provides the same core functionality: migrating BookStack data to DokuWiki format.

## Available Tools

### 🔴 [Perl](perl/) - **Recommended**
**File:** `one_script_to_rule_them_all.pl`

The comprehensive, battle-tested migration script. If you need something that works reliably, use this.

- ✅ Most mature implementation
- ✅ Comprehensive error handling
- ✅ Full backup and recovery
- ✅ Minimal dependencies

**Quick Start:**
```bash
cd perl/
./one_script_to_rule_them_all.pl
```

---

### 🐍 [Python](python/) - **Most User-Friendly**
**File:** `bookstack_migration.py`

Interactive Python script with hand-holding through the entire process.

- ✅ Interactive setup wizard
- ✅ Helpful error messages
- ✅ Dependency management assistance
- ✅ Modern Python 3 code

**Quick Start:**
```bash
cd python/
./bookstack_migration.py
```

---

### ☕ [Java](java/) - **Enterprise**
**File:** `DokuWikiExporter.java`

Framework-independent enterprise-grade exporter.

- ✅ No Laravel dependencies
- ✅ Direct database access
- ✅ Multi-threaded export
- ✅ Maven build support

**Quick Start:**
```bash
cd java/
mvn clean package
java -jar target/dokuwiki-exporter-1.0.0-jar-with-dependencies.jar --help
```

---

### ⚡ [C](c/) - **Performance**
**File:** `bookstack2dokuwiki.c`

Native binary for maximum performance and zero runtime dependencies.

- ✅ Fastest execution
- ✅ No interpreter needed
- ✅ Minimal memory footprint
- ✅ Portable compiled binary

**Quick Start:**
```bash
cd c/
make
./bookstack2dokuwiki --help
```

---

### 🐘 [PHP](php/) - **Laravel Integration**
**File:** `ExportToDokuWiki.php`

Laravel Artisan command for use within BookStack application.

- ⚠️ Requires working BookStack installation
- ⚠️ Framework-dependent
- ⚠️ May have compatibility issues
- ✅ Uses existing configuration

**Quick Start:**
```bash
# From BookStack root directory
php artisan bookstack:export-dokuwiki
```

---

## Which Tool Should I Use?

### Choose **Perl** if:
- You want the most reliable, tested solution
- You need comprehensive error handling and recovery
- You're comfortable with command-line tools

### Choose **Python** if:
- You prefer interactive guidance
- You want helpful error messages
- You're new to migrations

### Choose **Java** if:
- You need enterprise-grade reliability
- You want framework-independent operation
- You have Java already installed

### Choose **C** if:
- You need maximum performance
- You want zero dependencies
- You're compiling on the target system

### Choose **PHP** if:
- You're already running BookStack
- You want to use existing configuration
- You don't mind potential framework issues

---

## General Requirements

All tools require:
- Access to BookStack MySQL/MariaDB database
- Read permissions on BookStack files
- Write permissions for output directory
- Sufficient disk space (2x database size recommended)

### Database Credentials

You'll need:
- Database host and port
- Database name
- Database username and password

These are typically found in your BookStack `.env` file:
```bash
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bookstack
DB_USERNAME=bookstack
DB_PASSWORD=secret
```

---

## Migration Process

All tools follow the same general process:

1. **Diagnose** - Validate database connectivity and schema
2. **Backup** - Create backups before any modifications
3. **Export** - Extract data from BookStack
4. **Transform** - Convert HTML to DokuWiki format
5. **Deploy** - Write DokuWiki structure

---

## Output Structure

All tools produce the same DokuWiki-compatible structure:

```
output/
├── pages/              # DokuWiki pages in .txt format
│   └── [namespace]/
│       ├── start.txt
│       └── *.txt
├── media/              # Images and attachments
│   └── [namespace]/
│       └── [files]
└── migration.log       # Detailed operation log
```

---

## Common Issues

### Database Connection Failed
- Verify credentials in `.env` file
- Check MySQL/MariaDB is running
- Ensure database user has proper permissions

### Permission Denied
- Check output directory is writable
- Verify script has execute permissions
- Ensure sufficient disk space

### Missing Dependencies
- Refer to specific tool's README
- Each tool lists its requirements
- Installation instructions provided

---

## Documentation

Each directory contains a detailed README with:
- Prerequisites and installation
- Usage instructions and examples
- Configuration options
- Troubleshooting guide
- Build instructions (where applicable)

---

## Support

For issues or questions:
1. Check the specific tool's README
2. Review the tool's log files
3. Verify your database credentials
4. Ensure dependencies are installed

---

## Contributing

When adding new tools or modifications:
- Follow the existing directory structure
- Include comprehensive README
- Add build/run scripts where appropriate
- Test thoroughly before committing

---

## License

These tools are part of the BookStack project.

---

## Author

Created by Alex Alvonellos

*"One Script to rule them all, One Script to find them, One Script to bring them all, and in DokuWiki bind them."*
