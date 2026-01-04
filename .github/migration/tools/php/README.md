# PHP Migration Tool

## ExportToDokuWiki.php

Laravel Artisan command for BookStack to DokuWiki export (when you're already in the framework).

### What it does

A Laravel console command that exports BookStack content to DokuWiki format from within the BookStack application. This is the "official" method that uses BookStack's models and existing database connections.

### ⚠️ Warning

This tool depends on:
- Laravel framework being functional
- BookStack application being properly configured
- PHP having a good day
- Your prayers being answered

If this doesn't work (and it might not), use the Perl, Python, Java, or C versions instead.

### Features

- Integrated with BookStack's Eloquent models
- Uses existing database configuration
- Handles attachments and images
- Preserves metadata and timestamps
- HTML to DokuWiki syntax conversion
- Automatic fallback to Perl version on failure

### Prerequisites

This must be run from within a working BookStack installation:

```bash
# PHP 8.1 or higher
php --version

# Laravel dependencies (already installed with BookStack)
composer install

# BookStack must be properly configured
php artisan config:cache
```

### Installation

This file should be placed in your BookStack installation:

```
BookStack/
└── app/
    └── Console/
        └── Commands/
            └── ExportToDokuWiki.php
```

Register the command in `app/Console/Kernel.php`:

```php
protected $commands = [
    Commands\ExportToDokuWiki::class,
];
```

### Usage

```bash
# From BookStack root directory
php artisan bookstack:export-dokuwiki

# Specify output path
php artisan bookstack:export-dokuwiki --output-path=/path/to/output

# Additional options
php artisan bookstack:export-dokuwiki \
    --output-path=/path/to/output \
    --preserve-timestamps \
    --include-drafts \
    --verbose

# Show help
php artisan bookstack:export-dokuwiki --help
```

### Command Options

- `--output-path` - Output directory (default: storage/dokuwiki-export)
- `--preserve-timestamps` - Preserve original creation/modification times
- `--include-drafts` - Include draft pages in export
- `--clean` - Clean output directory before export
- `--verbose` - Enable detailed logging
- `--no-attachments` - Skip attachment export

### Output Structure

```
storage/dokuwiki-export/
├── pages/
│   └── [book-name]/
│       ├── [chapter-name]/
│       │   └── *.txt
│       └── start.txt
├── media/
│   └── [book-name]/
│       └── [images, files]
└── export.log
```

### Process Flow

1. **Validation**: Checks Laravel configuration and database connectivity
2. **Preparation**: Creates output directory structure
3. **Export Books**: Iterates through all books
4. **Export Chapters**: Processes chapters within each book
5. **Export Pages**: Converts page content to DokuWiki format
6. **Attachments**: Copies images and files to media directory
7. **Metadata**: Creates DokuWiki-compatible metadata files
8. **Logging**: Generates detailed export report

### Fallback Mechanism

If this command fails, it will automatically suggest running the Perl version:

```bash
# The command will output:
# "PHP export failed. Falling back to Perl implementation..."
# "Run: perl tools/one_script_to_rule_them_all.pl"
```

### Integration with BookStack

The command respects BookStack's:
- User permissions (runs as console user)
- Database configuration (from .env)
- Storage settings (uses configured storage driver)
- Image handling (processes through BookStack's image service)

### Environment Requirements

```bash
# .env configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bookstack
DB_USERNAME=bookstack
DB_PASSWORD=secret

# Ensure storage is writable
chmod -R 755 storage/
```

### Troubleshooting

**Class Not Found:**
```bash
composer dump-autoload
php artisan config:clear
```

**Permission Errors:**
```bash
# Fix storage permissions
chmod -R 755 storage/
chown -R www-data:www-data storage/

# Or match your web server user
chown -R nginx:nginx storage/
```

**Memory Limit:**
```bash
# Increase PHP memory limit
php -d memory_limit=512M artisan bookstack:export-dokuwiki

# Or edit php.ini
memory_limit = 512M
```

**Laravel Errors:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate caches
php artisan config:cache
php artisan route:cache
```

**When All Else Fails:**

Use one of the standalone tools:
```bash
# Perl (recommended)
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl

# Python (user-friendly)
python3 .github/migration/tools/python/bookstack_migration.py

# Java (enterprise)
java -jar .github/migration/tools/java/dokuwiki-exporter.jar

# C (performance)
./.github/migration/tools/c/bookstack2dokuwiki
```

### Performance Considerations

- Large databases (>1000 pages) may take several minutes
- Memory usage scales with page content size
- Consider running during low-traffic periods
- Use `--verbose` to monitor progress

### Logging

All operations are logged to:
- `storage/logs/laravel.log` (standard Laravel logging)
- `storage/dokuwiki-export/export.log` (export-specific log)

### Author

Alex Alvonellos  
*"DO NOT touch this on a Friday afternoon."*

---

**Recommendation**: If you're not already running BookStack or if this causes issues, use the Python or Perl versions instead. They're more reliable and don't depend on Laravel's mood.
