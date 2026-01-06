# BookStack Migration Tool

Command-line utility to migrate content from BookStack to DokuWiki with intelligent data source selection (API or database).

## Features

- **Intelligent Data Source Selection**: Automatically chooses between BookStack REST API or database export
- **Comprehensive Logging**: Detailed logs to `bookstack_migrate.log` for debugging
- **Multi-Driver Support**: MySQL and MariaDB database drivers with auto-installation
- **Automatic DokuWiki Detection**: Finds all DokuWiki installations on the system
- **Non-Interactive**: All configuration via environment variables
- **Cross-Platform**: Runs on Linux, macOS, and Windows
- **Standalone Executable**: Portable binary with no external dependencies (Python 3.8+ only)

## Installation & Usage

### Option 1: Standalone Binary (Recommended)
```bash
# Download from releases
wget https://github.com/alvonellos/BookStack/releases/download/v1.0.0/bookstack-migrate-linux
chmod +x bookstack-migrate-linux

# Set environment variables
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"

# Run
./bookstack-migrate-linux detect
./bookstack-migrate-linux export --db bookstack_db --user root --password secret
```

### Option 2: Python Package
```bash
pip install bookstack-migrate

# Set environment variables
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"

# Run
bookstack-migrate detect
bookstack-migrate export --db bookstack_db --user root --password secret
```

### Option 3: From Source
```bash
git clone https://github.com/alvonellos/BookStack.git
cd BookStack && git checkout feature/standalone
pip install -e .

# Set environment variables
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"

# Run
python bookstack_migrate.py detect
```

### With optional dependencies
```bash
# For MySQL support
pip install "bookstack-migrate[mysql]"

# For MariaDB support
pip install "bookstack-migrate[mariadb]"

# For development & testing
pip install "bookstack-migrate[dev]"
```

## Quick Start

### Step 1: Generate BookStack API Token
1. Log into your BookStack instance as an admin
2. Go to **Settings → Users → [Your User] → API Tokens**
3. Create a new token and save the ID and secret
4. Export them:
   ```bash
   export BOOKSTACK_TOKEN_ID="your_token_id"
   export BOOKSTACK_TOKEN_SECRET="your_token_secret"
   export BOOKSTACK_BASE_URL="https://your-bookstack.example.com"
   ```

### Step 2: Detect DokuWiki Installation
```bash
bookstack-migrate detect
# Output: Lists all found installations with paths and permissions
```

### Step 3: Export BookStack Content
```bash
# Option A: Export via API (auto-uses if DB not available)
bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --prefer-api

# Option B: Export via Database (preferred for large content)
bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --host localhost \
  --port 3306 \
  --driver mysql \
  --output ./export
```

### Step 4: Verify Results
```bash
bookstack-migrate version
bookstack-migrate help
```

## Configuration

All configuration is read from environment variables. No interactive prompts.

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| BOOKSTACK_TOKEN_ID | Yes | - | API token ID from BookStack |
| BOOKSTACK_TOKEN_SECRET | Yes | - | API token secret from BookStack |
| BOOKSTACK_BASE_URL | No | `http://localhost:8000` | Base URL of BookStack instance |
| BOOKSTACK_SPEC_CACHE | No | `~/.cache/bookstack/openapi.json` | Path to cache OpenAPI spec |
| DB_DRIVER | No | auto | Database driver: `mysql` or `mariadb` |

## Commands

### `detect` - Find DokuWiki Installations
```bash
bookstack-migrate detect
```
Searches common paths for DokuWiki installations and reports accessibility.

### `export` - Export BookStack Content
```bash
bookstack-migrate export [OPTIONS]
```

**Options:**
- `--db NAME` (required) - Database name
- `--user USER` (required) - Database user
- `--password PASS` (required) - Database password
- `--host HOST` - Database host (default: localhost)
- `--port PORT` - Database port (default: 3306)
- `--driver {mysql,mariadb}` - Database driver (auto-detected if not specified)
- `--output DIR` - Output directory (default: ./export)
- `--prefer-api` - Prefer API over database if both available

### `version` - Show Version
```bash
bookstack-migrate version
```

### `help` - Show Help
```bash
bookstack-migrate help
```

## Data Source Selection

The tool intelligently selects the best data source:

1. **If both API and Database are available:**
   - Uses database by default (faster for large content)
   - Use `--prefer-api` flag to force API usage

2. **If only API is available:**
   - Uses BookStack REST API to export content

3. **If only Database is available:**
   - Uses direct database export (MySQL/MariaDB)

4. **If neither is available:**
   - Fails with clear error message and installation instructions

## Logging

All operations are logged to `bookstack_migrate.log`:
```
2026-01-06 23:47:43,857 [INFO] Command: version
2026-01-06 23:47:43,857 [INFO] Version: 1.0.0
2026-01-06 23:47:44,027 [INFO] DataSourceSelector: DB=true, API=true, prefer_api=false
2026-01-06 23:47:44,027 [INFO] Using database (preferred method)
```

View logs in real-time:
```bash
tail -f bookstack_migrate.log
```

## Docker Environment (Testing)

```bash
# Start all services
docker-compose up -d

# Wait for services to be ready (30 seconds)

# Access:
# - BookStack: http://localhost:8000
# - DokuWiki:  http://localhost:8080
# - MySQL:     localhost:3306

# Run tests
bash build/integration-test.sh

# Stop all
docker-compose down
```

## Development

### Install dev dependencies
```bash
pip install -e ".[dev]"
```

### Run tests
```bash
python -m pytest tests/ -v
```

### Run integration tests
```bash
bash build/integration-test.sh
```

### Build locally
```bash
bash build/all.sh
```

### Build standalone binaries
```bash
bash build/binaries.sh
```

## Requirements

- **Python**: 3.8+
- **Optional**: `mysql-connector-python` for MySQL export
- **Optional**: `mariadb` for MariaDB export
- **Optional**: `pytest` for testing
- **Optional**: Docker for full integration testing

## TODO & Future Enhancements

- [ ] **Full Content Migration**: Implement page-by-page content copying with metadata
- [ ] **Image/Media Migration**: Download and migrate images to DokuWiki media directories
- [ ] **Hierarchical Structure**: Preserve BookStack hierarchy (Bookshelf → Book → Chapter → Page) in DokuWiki
- [ ] **Permissions Mapping**: Map BookStack access controls to DokuWiki page access
- [ ] **User Account Sync**: Migrate user accounts from BookStack to DokuWiki (if applicable)
- [ ] **Incremental Sync**: Support incremental updates (not full re-export)
- [ ] **Search Index**: Rebuild DokuWiki search indices after import
- [ ] **Conflict Resolution**: Handle duplicate page names intelligently
- [ ] **Format Conversion**: Advanced HTML → Markdown/DokuWiki syntax conversion
- [ ] **Multi-Language Support**: Handle multi-language BookStack instances
- [ ] **API Fallback**: Retry with database if API is slow/unreliable
- [ ] **Progress Bar**: Add visual progress indication for long operations
- [ ] **Dry-Run Mode**: Test migration without making changes
- [ ] **Rollback Support**: Generate rollback scripts for failed migrations

## Alternative Approaches (If Standard Methods Fail)

If the standard API and database export methods don't work:

1. **HTML Export + Web Scraping**
   ```bash
   # Export BookStack as HTML and parse locally
   # Requires: beautifulsoup4, html2text
   # Converts BookStack HTML to DokuWiki syntax
   ```

2. **Direct Database Queries (Advanced)**
   ```bash
   # Custom SQL queries against BookStack database
   # Requires: Direct database access (MySQL/MariaDB)
   # Benefit: Full control over data extraction
   ```

3. **LDAP/User Import**
   ```bash
   # If BookStack uses LDAP, import user accounts directly
   # Requires: ldap3, proper DokuWiki LDAP plugin setup
   ```

4. **File-Based Migration**
   ```bash
   # Export BookStack pages as JSON/XML files
   # Import into DokuWiki via plugin
   # Requires: Custom importer plugin development
   ```

## Troubleshooting

### Database Connection Failed
```
❌ No database driver found. Tried mysql-connector and mariadb.
```
**Solution**: Install MySQL connector
```bash
pip install mysql-connector-python
# or
pip install mariadb
```

### API Not Available
```
⚠️  API not available: [error message]
```
**Solution**: Check environment variables
```bash
echo $BOOKSTACK_TOKEN_ID
echo $BOOKSTACK_TOKEN_SECRET
echo $BOOKSTACK_BASE_URL
```

### Permission Denied
```
❌ DokuWiki not writable: /var/www/dokuwiki
```
**Solution**: Adjust file permissions
```bash
sudo chown -R www-data:www-data /var/www/dokuwiki
```

## GitHub Actions CI/CD

This project includes automated testing and releases:

- **Test Matrix**: Python 3.8, 3.9, 3.10, 3.11, 3.12
- **Automated Tests**: Unit tests, linting, package builds
- **Docker Integration**: Tests against real BookStack/DokuWiki containers
- **Auto-Release**: Automatic binary and package creation on version tags

See [.github/workflows/build.yml](.github/workflows/build.yml) for details.

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Support

For issues, questions, or contributions:
- **GitHub Issues**: [alvonellos/BookStack/issues](https://github.com/alvonellos/BookStack/issues)
- **Documentation**: [README.md](README.md)
- **Logs**: Check `bookstack_migrate.log` for detailed debugging information

