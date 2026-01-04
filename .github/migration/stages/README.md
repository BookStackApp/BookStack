# BookStack Migration Stages

This directory contains the organized migration scripts for migrating from BookStack to DokuWiki.

## Overview

The migration is broken into 4 clear stages, each designed to be run independently with proper error handling and status codes.

## Stage Scripts

### 01-setup.sh (24KB)
**Purpose:** Install all required dependencies for the migration

**What it does:**
- Detects OS and package manager
- Installs C compiler toolchain
- Installs Perl with DBI and DBD::mysql modules
- Validates Java/Maven setup
- Checks and restarts system services (MySQL, web servers)
- Comprehensive diagnostics for any issues

**Usage:**
```bash
./01-setup.sh
```

**Exit codes:**
- 0 = Setup completed successfully
- 1 = Setup failed

**Features:**
- Smeagol-themed output (because why not?)
- Auto-detects missing dependencies
- Interactive prompts for confirmations
- Comprehensive error messages

---

### 02-backup.sh (9.5KB)
**Purpose:** Create comprehensive backup of BookStack before migration

**What it does:**
- Backs up entire BookStack database
- Backs up all uploaded files
- Backs up .env configuration
- Creates compressed archive
- Verifies backup is valid
- Shows exact location of backup

**Usage:**
```bash
./02-backup.sh
```

**Exit codes:**
- 0 = Backup succeeded
- 1 = Backup failed

**Features:**
- Manual backup script for safety
- Timestamp-based backup names
- Validation checks
- Clear output of backup location

---

### 03-export.sh (14KB)
**Purpose:** Export BookStack content to DokuWiki format

**What it does:**
- Validates database configuration from .env file
- Automatically selects best available export tool:
  1. Perl (fastest, most reliable)
  2. Java (slower but works)
  3. C binary (fast if compiled)
  4. PHP artisan (last resort)
- Runs export with appropriate tool
- Generates export statistics
- Creates properly formatted DokuWiki files

**Usage:**
```bash
./03-export.sh [output_directory]
```

**Exit codes:**
- 0 = Export succeeded
- 1 = Export failed
- 2 = Configuration error (missing .env or credentials)
- 3 = No suitable export tool found

**Features:**
- Auto-detection of best available tool
- Database connectivity testing
- Detailed progress reporting
- Export statistics (file count, size, duration)
- Clear error messages

---

### 04-validate.sh (17KB)
**Purpose:** Validate the exported DokuWiki data

**What it does:**
- Checks export directory exists and is not empty
- Validates DokuWiki format (`.txt` files, proper structure)
- Checks for standard DokuWiki directory structure (`data/pages/`, `data/media/`)
- Validates file integrity (no empty or corrupt files)
- Samples files for content validation
- Checks for metadata and checksum files
- Generates detailed validation report

**Usage:**
```bash
./04-validate.sh [export_directory]
```

**Exit codes:**
- 0 = Validation passed
- 1 = Validation failed
- 2 = Export directory not found
- 3 = Critical validation errors

**Features:**
- Comprehensive validation checks
- UTF-8 encoding validation
- DokuWiki syntax detection
- Detailed statistics
- Clear pass/fail reporting
- Actionable recommendations

---

## Complete Migration Workflow

Run the scripts in order:

```bash
# Stage 1: Setup dependencies
cd /var/www/bookstack
.github/migration/stages/01-setup.sh

# Stage 2: Backup everything
.github/migration/stages/02-backup.sh

# Stage 3: Export to DokuWiki format
.github/migration/stages/03-export.sh ./dokuwiki-export

# Stage 4: Validate the export
.github/migration/stages/04-validate.sh ./dokuwiki-export
```

## Exit Code Standards

All scripts follow consistent exit code conventions:
- **0** = Success
- **1** = General failure
- **2** = Configuration/prerequisite error
- **3** = Critical error (for validation scripts)

## Features Common to All Scripts

✅ **Clear output formatting** with colored messages
✅ **Proper error handling** with meaningful messages
✅ **Independent execution** - each can be run standalone
✅ **Status codes** for automation/scripting
✅ **Progress indicators** and statistics
✅ **Helpful documentation** in script headers

## Source Files

These scripts were organized from:
- `01-setup.sh` ← `bookstack-migration/AUTO_INSTALL_EVERYTHING.sh`
- `02-backup.sh` ← `bookstack-migration/scripts/make-backup-before-migration.sh`
- `03-export.sh` ← Extracted export logic from `bookstack-migration/scripts/ULTIMATE_MIGRATION.sh`
- `04-validate.sh` ← New validation script created for this stage system

## Design Philosophy

Each stage script is designed to:
1. **Do one thing well** - Single responsibility principle
2. **Fail fast** - Exit immediately on errors (set -e)
3. **Be transparent** - Clear logging of what's happening
4. **Be resumable** - Can be re-run if something fails
5. **Be helpful** - Provide actionable error messages

## Troubleshooting

If a stage fails:

1. **Read the error message** - Scripts provide detailed error context
2. **Check prerequisites** - Each script documents what it needs
3. **Run previous stages** - Ensure earlier stages completed
4. **Check logs** - Scripts output helpful diagnostic info
5. **Re-run the stage** - Scripts are designed to be idempotent

## Notes

- Original mega-script `ULTIMATE_MIGRATION.sh` (861 lines) has been preserved in `bookstack-migration/scripts/` but is no longer needed
- The stage system provides better modularity and debugging
- Each stage can be tested independently
- Clear separation of concerns makes troubleshooting easier

---

**Created:** 2026-01-04
**Organization:** Part of BookStack migration system reorganization
