# BookStack Migration Tool

Command-line utility to migrate content from BookStack to DokuWiki.

## Quick Start

```bash
# Set API credentials
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"
export BOOKSTACK_BASE_URL="https://your-bookstack.example.com"

# Run tool
python bookstack-migrate detect          # Find DokuWiki installations
python bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --output ./export

python bookstack-migrate version         # Show version
```

## Installation

### From Source
```bash
pip install -r requirements.txt
./bookstack-migrate --help
```

### From PyPI (when released)
```bash
pip install bookstack-migrate
bookstack-migrate --help
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

### With Docker
```bash
docker-compose up -d
```

## Environment Variables

The tool requires the following environment variables for API access:

- `BOOKSTACK_TOKEN_ID` - Your BookStack API token ID (required)
- `BOOKSTACK_TOKEN_SECRET` - Your BookStack API token secret (required)
- `BOOKSTACK_BASE_URL` (optional) - BookStack instance URL (default: `http://localhost:8000`)
- `BOOKSTACK_SPEC_CACHE` (optional) - OpenAPI spec cache path (default: `~/.cache/bookstack/openapi.json`)

**To generate API tokens:**
1. Log into your BookStack instance as an admin
2. Go to Settings → Users → [Your User] → API Tokens
3. Create a new token and save the ID and secret
4. Export them:
   ```bash
   export BOOKSTACK_TOKEN_ID="your_token_id"
   export BOOKSTACK_TOKEN_SECRET="your_token_secret"
   export BOOKSTACK_BASE_URL="https://your-bookstack.example.com"
   ```

## Features

- Automatic DokuWiki installation detection (apt, manual, Docker)
- BookStack database export to DokuWiki format
- Image migration with proper permission handling
- Comprehensive error handling and logging
- Non-interactive: all config via environment variables and CLI flags

## Usage

### Detect DokuWiki Installations
```bash
bookstack-migrate detect
```

### Export BookStack Database
```bash
bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --host localhost \
  --port 3306 \
  --output ./export
```

### Show Version
```bash
bookstack-migrate version
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

### Build locally
```bash
bash build/all.sh
```

### Build binaries
```bash
bash build/binaries.sh
```

## Requirements

- Python 3.8+
- Optional: mysql-connector-python for database export
- Optional: Docker for testing environment
- Optional: pytest for running tests
- Optional: pyinstaller for building standalone binaries

## License

See LICENSE file for details.

