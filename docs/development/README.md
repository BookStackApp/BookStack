# Development Resources

> Resources for BookStack development, testing, and architecture

## 📖 Books in this Shelf

### [Build & Test](./build-and-test.md)
Complete procedures for building assets, configuring environments, and running tests.

**Chapters:**
1. Build Process (production & development)
2. PHP Configuration (extensions & setup)
3. Database Setup (migrations & seeding)
4. Running Tests (PHPUnit, linting, static analysis)
5. Test Results (latest execution details)
6. Troubleshooting (common issues & solutions)

### [Architecture](./architecture.md)
System architecture, design patterns, and code organization.

**Chapters:**
1. Overview & Principles
2. Directory Structure
3. Core Patterns (MVC, Repository, Permissions)
4. Frontend Architecture
5. Database Schema

---

## 🔧 Quick Start

### Development Environment

```bash
# Clone repository
git clone https://github.com/BookStackApp/BookStack.git
cd BookStack

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Build assets
npm run dev

# Run tests
composer test
```

### Docker Development

```bash
# Start services
docker-compose up -d

# Access container
docker exec -it bookstack-app-1 bash

# Run migrations
php artisan migrate
```

---

## 📊 Current Status

| Component | Status | Details |
|-----------|--------|---------|
| Version | v24.x | Latest development |
| PHP | 8.2.14 | Minimum 8.1 |
| Laravel | 12.x | Latest |
| Node.js | 20.x | For asset compilation |
| Database | MySQL 8.4 | Or MariaDB 10.3+ |

---

## 🧪 Testing

### Test Coverage

- **Unit Tests**: Core functionality
- **Feature Tests**: End-to-end workflows
- **Export Tests**: ✅ All passing (5/5)
- **API Tests**: REST API endpoints
- **Permission Tests**: Access control

### Continuous Integration

- PHP CodeSniffer (linting)
- PHPStan (static analysis)
- PHPUnit (unit/feature tests)
- ESLint (JavaScript)

---

## 📚 Related Documentation

- [Copilot Instructions](../../.github/copilot-instructions.md) - AI development guide
- [Main README](../../readme.md) - Project overview
- [Contributing Guide](../../CONTRIBUTING.md) - Contribution guidelines

---

[← Back to Documentation](../README.md)
