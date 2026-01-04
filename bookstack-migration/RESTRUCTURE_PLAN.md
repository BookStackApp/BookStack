# Migration Toolkit Restructuring Plan

## Executive Summary
The current structure has 19 scripts with significant redundancy, unclear naming, and joke code. This plan consolidates everything into a clean, stage-based workflow.

## Current Problems

### 1. Redundant Dependency Installers (3 files doing same thing)
- `AUTO_INSTALL_EVERYTHING.sh` (589 lines) ✅ KEEP - Most comprehensive
- `scripts/setup-deps.sh` (227 lines) ❌ DELETE - Redundant
- `tools/AUTO_INSTALL_DEPS.sh` (116 lines) ❌ DELETE - Redundant

### 2. Joke/Development Scripts (No production value)
- `scripts/gaslight-user.sh` (256 lines) ❌ DELETE - Humor script
- `scripts/commit-and-push.sh` ❌ DELETE - Dev helper
- `scripts/validate-and-commit.sh` ❌ DELETE - Dev helper
- `scripts/diagnose.sh` (6 lines, calls perl) ❌ DELETE - Wrapper

### 3. Redundant Documentation (5+ files saying same thing)
- `README.md` (336 lines) ✅ CONSOLIDATE - Main docs
- `START_HERE.txt` (373 lines) ❌ MERGE into README
- `QUICK_REFERENCE.txt` (204 lines) ❌ MERGE into README
- `MIGRATION_INVENTORY.txt` ❌ MERGE into README
- `STAGING_FINAL.txt` ❌ DELETE - Development notes
- `STAGING_READY.txt` ❌ DELETE - Development notes

### 4. Unclear Script Purposes
- `scripts/ULTIMATE_MIGRATION.sh` (861 lines) ⚠️ EVALUATE - Might be useful
- `scripts/migration-helper.sh` ❌ DELETE - Calls other scripts
- `scripts/make-backup-before-migration.sh` ✅ KEEP as stage

### 5. Multiple Entry Points (Confusing for users)
- `help_me_fix_my_mistake.sh` ✅ KEEP - Good interactive interface
- `bookstack_migration.py` ✅ KEEP - Python option
- `tools/one_script_to_rule_them_all.pl` ✅ KEEP - Main workhorse
- Plus 6 other scripts...

## Proposed Clean Structure

```
.github/
  migration/
    stages/
      01-setup.sh           # AUTO_INSTALL_EVERYTHING.sh (renamed)
      02-backup.sh          # make-backup-before-migration.sh (moved)
      03-export.sh          # Core export logic (extracted)
      04-validate.sh        # Validation logic (extracted)
      
    tools/
      perl/
        one_script_to_rule_them_all.pl
      python/
        bookstack_migration.py
      java/
        DokuWikiExporter.java
      c/
        bookstack2dokuwiki.c
      php/
        ExportToDokuWiki.php
        
    tests/
      test_perl_migration.t
      test_python_migration.py
      ExportToDokuWikiTest.php
      test_integration.sh      # New comprehensive test
      
    docs/
      README.md              # Consolidated from 5 docs
      ARCHITECTURE.md        # How it works
      LANGUAGE_COMPARISON.md # (moved from docs/)
      DETAILED_GUIDE.md      # (moved from docs/)

bookstack-migration/ (root - CLEAN)
  migrate.sh               # Single entry point - menu system
  README.md                # Points to .github/migration/docs/
  docker-compose.test.yml  # Keep for testing
  
# DELETED (12 files):
  scripts/setup-deps.sh
  scripts/gaslight-user.sh
  scripts/diagnose.sh
  scripts/commit-and-push.sh
  scripts/validate-and-commit.sh
  scripts/migration-helper.sh
  tools/AUTO_INSTALL_DEPS.sh
  START_HERE.txt
  QUICK_REFERENCE.txt
  MIGRATION_INVENTORY.txt
  STAGING_FINAL.txt
  STAGING_READY.txt
```

## Stage-Based Workflow

### Stage 1: Setup (`01-setup.sh`)
- Check OS and architecture
- Install C compiler, Perl modules, Java, Python
- Validate MySQL/MariaDB running
- Check web server status
- Verify credentials/permissions
**Source**: Current `AUTO_INSTALL_EVERYTHING.sh`

### Stage 2: Backup (`02-backup.sh`)
- Create timestamped database backup
- Export .env and configs
- Create restore instructions
- Verify backup integrity
**Source**: Current `scripts/make-backup-before-migration.sh`

### Stage 3: Export (`03-export.sh`)
- Connect to BookStack database
- Extract pages, books, chapters, attachments
- Convert to DokuWiki format
- Generate namespace structure
- Handle images/media
**Source**: Logic from Perl/Python/Java tools

### Stage 4: Validate (`04-validate.sh`)
- Check export completeness
- Verify file integrity (MD5)
- Compare record counts
- Test DokuWiki format compliance
- Generate migration report
**Source**: Extracted from various scripts

## Single Entry Point (`migrate.sh`)

```bash
#!/bin/bash
# BookStack to DokuWiki Migration
# Usage: ./migrate.sh [stage|all|interactive]

case "$1" in
  1|setup)    .github/migration/stages/01-setup.sh ;;
  2|backup)   .github/migration/stages/02-backup.sh ;;
  3|export)   .github/migration/stages/03-export.sh ;;
  4|validate) .github/migration/stages/04-validate.sh ;;
  all)        # Run all stages
              for stage in .github/migration/stages/*.sh; do
                bash "$stage" || exit 1
              done ;;
  *)          # Interactive menu
              .github/migration/tools/perl/one_script_to_rule_them_all.pl ;;
esac
```

## Benefits

1. **Clear Structure**: Stages make workflow obvious
2. **No Redundancy**: One script per purpose
3. **Easy Testing**: Each stage independently testable
4. **Better CI/CD**: .github location is standard
5. **Clean Root**: Only entry point visible
6. **Professional**: No joke code in production
7. **Maintainable**: Related code grouped together
8. **Discoverable**: Obvious what each file does

## Migration Checklist

- [ ] Create .github/migration/ structure
- [ ] Move AUTO_INSTALL_EVERYTHING.sh → 01-setup.sh
- [ ] Move make-backup-before-migration.sh → 02-backup.sh
- [ ] Extract export logic → 03-export.sh
- [ ] Extract validation logic → 04-validate.sh
- [ ] Move all tools into tools/{language}/
- [ ] Consolidate docs into single README
- [ ] Create migrate.sh entry point
- [ ] Update all path references
- [ ] Run comprehensive tests
- [ ] Delete 12 redundant files
- [ ] Update root README with new structure

## Rollback Plan

If anything breaks:
1. All original files preserved in git
2. Can revert entire commit
3. Old structure fully functional until tested

## Testing Strategy

```bash
# Test each stage independently
.github/migration/stages/01-setup.sh --dry-run
.github/migration/stages/02-backup.sh --dry-run
.github/migration/stages/03-export.sh --dry-run
.github/migration/stages/04-validate.sh --dry-run

# Test full workflow
./migrate.sh all --test-mode

# Test each tool
perl .github/migration/tools/perl/one_script_to_rule_them_all.pl --help
python3 .github/migration/tools/python/bookstack_migration.py --help
```

## Timeline

1. Create structure: 30 min
2. Move/rename files: 20 min
3. Update paths: 15 min
4. Test stages: 30 min
5. Documentation: 20 min
6. Final validation: 15 min

**Total**: ~2 hours

## Approval Required?

This is a significant restructure. Should we:
- [ ] Proceed with full restructure
- [ ] Do it in phases
- [ ] Review plan first
- [ ] Keep current structure (cleaned up)
