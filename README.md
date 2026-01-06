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
