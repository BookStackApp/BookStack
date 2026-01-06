# Migration Tools

> Export and migrate BookStack content to other platforms

## 📖 Books in this Shelf

### [DokuWiki Export](./dokuwiki-export.md)
Complete guide to exporting BookStack content to DokuWiki format with hierarchical structure preservation.

**Chapters:**
- Overview & Features
- Installation & Setup
- Usage & Configuration
- Troubleshooting

### [Database Migration](./database-migration.md)
Procedures for migrating existing BookStack database instances.

**Chapters:**
- Prerequisites
- Migration Script
- Backup & Restore
- Verification

---

## 🎯 Quick Links

- **[migrate.py](../../migrate.py)** - Main migration script (57KB)
- **[test_hierarchical_export.py](../../test_hierarchical_export.py)** - Test & validation script
- **[Migration Logs](../../migration_logs/)** - Execution logs

---

## 🔧 Tools Overview

| Tool | Purpose | Status |
|------|---------|--------|
| migrate.py | BookStack → DokuWiki export | ✅ Complete |
| Backup utility | Database & file backup | ✅ Integrated |
| Schema inspector | Database analysis | ✅ Integrated |
| Dry-run mode | Test without changes | ✅ Available |

---

[← Back to Documentation](../README.md)
