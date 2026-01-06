# BookStack Documentation

> Comprehensive documentation for BookStack development, migration, and deployment.

## 📚 Documentation Structure

This documentation follows the BookStack content hierarchy: **Shelf → Book → Chapter → Page**

```
docs/
├── README.md                    (You are here - Documentation index)
├── migration/                   (Migration & Export Tools)
│   ├── README.md               (Migration overview)
│   ├── dokuwiki-export.md      (DokuWiki export guide)
│   └── database-migration.md   (Database migration procedures)
└── development/                 (Development Resources)
    ├── README.md               (Development overview)
    ├── build-and-test.md       (Build & testing procedures)
    └── architecture.md         (System architecture)
```

---

## 🗂️ Documentation Sections

### 📦 [Migration](./migration/)
Tools and guides for migrating BookStack data to other platforms.

- **[DokuWiki Export](./migration/dokuwiki-export.md)** - Export BookStack content to DokuWiki format
- **[Database Migration](./migration/database-migration.md)** - Migrate existing BookStack instances

### 🔧 [Development](./development/)
Resources for BookStack development and testing.

- **[Build & Test](./development/build-and-test.md)** - Build procedures and test results
- **[Architecture](./development/architecture.md)** - System architecture and design patterns

---

## 🚀 Quick Start

### For Developers
```bash
# Install dependencies
composer install
npm install

# Build assets
npm run production

# Run tests
composer test
```

### For Migration
```bash
# Run migration tool
python3 migrate.py

# Or test export
python3 test_hierarchical_export.py
```

---

## 📖 Related Documentation

- [Main README](../readme.md) - Project overview and setup
- [Copilot Instructions](../.github/copilot-instructions.md) - Development guide for AI assistants
- [Security Policy](../SECURITY.md) - Security guidelines
- [Code of Conduct](../CODE_OF_CONDUCT.md) - Community guidelines

---

## 🤝 Contributing

This documentation uses the BookStack content model:
- **Shelf**: Top-level category (e.g., Migration, Development)
- **Book**: Major topic within a shelf (e.g., DokuWiki Export)
- **Chapter**: Logical section within a book (e.g., Setup, Usage)
- **Page**: Individual document with specific content

When adding documentation:
1. Place files in the appropriate shelf directory
2. Create a README.md for each shelf/book
3. Use clear, hierarchical headings
4. Include practical examples
5. Link between related pages

---

*Last Updated: January 6, 2026*
