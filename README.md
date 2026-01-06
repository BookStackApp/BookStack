# BookStack Migration Tool

Command-line utility to migrate content from BookStack to DokuWiki.

## Quick Start

```bash
python bookstack-migrate detect          # Find DokuWiki installations
python bookstack-migrate export ...      # Export BookStack content
python bookstack-migrate version         # Show version
```

## Installation

### From Source
```bash
pip install -r requirements.txt
./bookstack-migrate --help
```

### With Docker
```bash
docker-compose up -d
```

### Environment Variables

The tool requires the following environment variables for API access:

- `BOOKSTACK_TOKEN_ID` - Your BookStack API token ID
- `BOOKSTACK_TOKEN_SECRET` - Your BookStack API token secret
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

## Usage

### Detect DokuWiki Installations
```bash
python bookstack-migrate detect
```

### Export BookStack Database
```bash
python bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --output ./export
```

## Development

### Build Binaries
```bash
bash build/all.sh
```

### Run Tests
```bash
bash build/docker-test.sh
```

## Requirements

- Python 3.8+
- Optional: mysql-connector-python for database export
- Optional: Docker for testing environment

## License

See LICENSE file for details.
