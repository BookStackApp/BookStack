# Perl Migration Tool

## one_script_to_rule_them_all.pl

The comprehensive BookStack to DokuWiki migration script written in Perl.

### What it does

This is the main migration script that handles the complete migration process:

1. **DIAGNOSE**: Database connection validation, schema inspection, and system capability checks
2. **BACKUP**: Complete database dump (mysqldump) and file preservation with compression
3. **EXPORT**: Full data export from BookStack to DokuWiki format
4. **TRANSFORM**: Content conversion, HTML cleanup, and format transformation
5. **DEPLOY**: DokuWiki structure creation and deployment

### Features

- Complete database migration with validation
- Intelligent error handling and recovery
- Backup creation before any destructive operations
- HTML to DokuWiki syntax conversion
- File attachment handling
- Timestamp preservation
- Comprehensive logging

### Prerequisites

```bash
# Perl 5.10 or higher
perl --version

# Required Perl modules
cpan install DBI DBD::mysql File::Copy::Recursive Archive::Tar HTML::Parser
```

### Usage

```bash
# Make executable
chmod +x one_script_to_rule_them_all.pl

# Run with default settings
./one_script_to_rule_them_all.pl

# Run with custom database settings
./one_script_to_rule_them_all.pl --host localhost --port 3306 --database bookstack --user root

# Run specific stage only
./one_script_to_rule_them_all.pl --stage backup
./one_script_to_rule_them_all.pl --stage export

# Dry run (no changes made)
./one_script_to_rule_them_all.pl --dry-run
```

### Configuration

The script can be configured via:
- Command-line arguments
- Environment variables
- Config file (`.migration.conf`)

### Output

- Backup files in `storage/backups/`
- Exported DokuWiki structure in `storage/dokuwiki-export/`
- Detailed logs in `storage/logs/migration.log`

### Troubleshooting

If the script fails:
1. Check the log file for detailed error messages
2. Verify database credentials and connectivity
3. Ensure sufficient disk space for backups
4. Check Perl module dependencies

### Author

Created by Alex Alvonellos

---

*"One Script to rule them all, One Script to find them, One Script to bring them all, and in DokuWiki bind them"*
