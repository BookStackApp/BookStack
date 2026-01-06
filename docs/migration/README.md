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

- **[migrate.py](../../migrate.py)** - Experimental BookStack → DokuWiki export helper (books/chapters/pages only)
- **[test_hierarchical_export.py](../../test_hierarchical_export.py)** - Test & validation script (for the experimental exporter)
- **[Migration Logs](../../migration_logs/)** - Execution logs created by the exporter

---

## 🔧 Tools Overview

| Tool | Purpose | Status |
|------|---------|--------|
| migrate.py | BookStack → DokuWiki export (text content only) | ⚠️ Experimental |
| Backup utility | Database & file backup (best effort) | ✅ Integrated |
| Schema inspector | Database analysis | ✅ Integrated |
| Dry-run mode | Test without changes | ✅ Available |

---

[← Back to Documentation](../README.md)
