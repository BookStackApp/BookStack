# BookStack Migration Tool

Command-line utility to migrate content from BookStack to DokuWiki.

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
```

### Option 2: Python Package
```bash
pip install bookstack-migrate

# Set environment variables
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"

# Run
bookstack-migrate detect
```

### Option 3: From Source
```bash
git clone https://github.com/alvonellos/BookStack.git
cd BookStack
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

### Generate BookStack API Token
1. Log into your BookStack instance as an admin
2. Go to Settings → Users → [Your User] → API Tokens
3. Create a new token and save the ID and secret
4. Export them:
   ```bash
   export BOOKSTACK_TOKEN_ID="your_token_id"
   export BOOKSTACK_TOKEN_SECRET="your_token_secret"
   export BOOKSTACK_BASE_URL="https://your-bookstack.example.com"
   ```

### Detect DokuWiki Installation
```bash
bookstack-migrate detect
# Lists all found installations with paths and permissions
```

### Export BookStack to DokuWiki Format
```bash
bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --host localhost \
  --port 3306 \
  --output ./export \
  --driver mysql
```

### Show Help
```bash
bookstack-migrate help
```

### Check Version
```bash
bookstack-migrate version
```

## Configuration

All configuration is read from environment variables. No interactive prompts.

| Variable | Required | Description |
|----------|----------|-------------|
| BOOKSTACK_TOKEN_ID | Yes | API token ID from BookStack |
| BOOKSTACK_TOKEN_SECRET | Yes | API token secret from BookStack |
| BOOKSTACK_BASE_URL | No | Base URL of BookStack (auto-detected if possible) |
| BOOKSTACK_SPEC_CACHE | No | Path to cache OpenAPI spec |

## Features

- Automatic DokuWiki installation detection (apt, manual, Docker)
- BookStack database export to DokuWiki format
- Image migration with proper permission handling
- Comprehensive error handling and logging
- Non-interactive: all config via environment variables and CLI flags

## Docker Environment (Full Stack)

```bash
# Start all services
docker-compose up -d

# Wait for services to be ready (30 seconds)

# Access:
# - BookStack: http://localhost:8000
# - DokuWiki:  http://localhost:8080
# - MySQL:     localhost:3306

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
- Optional: mariadb for database export
- Optional: Docker for testing environment
- Optional: pytest for running tests
- Optional: pyinstaller for building standalone binaries

## License

See LICENSE file for details.

