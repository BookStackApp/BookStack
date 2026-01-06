# Releases & Downloads

BookStack Migration Tool releases are published with platform-specific binaries and Python packages.

## Latest Release

Download from [GitHub Releases](https://github.com/alvonellos/BookStack/releases) or [PyPI](https://pypi.org/project/bookstack-migrate/).

## Installation Methods

### 1. Binary (No Python Required)

Download pre-built executables for your platform:

```bash
# Linux
wget https://github.com/alvonellos/BookStack/releases/download/v1.0.0/bookstack-migrate-1.0.0-linux
chmod +x bookstack-migrate-1.0.0-linux
./bookstack-migrate-1.0.0-linux detect

# macOS
wget https://github.com/alvonellos/BookStack/releases/download/v1.0.0/bookstack-migrate-1.0.0-macos
chmod +x bookstack-migrate-1.0.0-macos
./bookstack-migrate-1.0.0-macos detect

# Windows
# Download bookstack-migrate-1.0.0-windows.exe from releases
.\bookstack-migrate-1.0.0-windows.exe detect
```

### 2. Python Package (Requires Python 3.8+)

```bash
# From PyPI
pip install bookstack-migrate

# From source
pip install -r requirements.txt
python bookstack-migrate detect
```

### 3. Docker

```bash
docker-compose up -d

# Access services:
# - BookStack: http://localhost:8000
# - DokuWiki:  http://localhost:8080
# - MySQL:     localhost:3306
```

## Build Your Own

### Prerequisites
- Python 3.8+
- PyInstaller (for binary builds)
- Git

### Build Release

```bash
# Full release build with all artifacts
bash build/release.sh

# This creates:
# - releases/{VERSION}/binaries/     - Platform-specific executables
# - releases/{VERSION}/python/       - Python packages (.whl, .tar.gz)
# - releases/{VERSION}/SHA256SUMS    - Checksums for verification
```

### Build Components

```bash
# Just binaries
bash build/binaries.sh

# Just Python package
python -m build

# Just tests
bash build/docker-test.sh
```

## Verification

Verify downloaded files using SHA256 checksums:

```bash
# Download SHA256SUMS from release
sha256sum -c SHA256SUMS

# Or verify individual file
sha256sum bookstack-migrate-1.0.0-linux
```

## Automated Releases

Releases are automatically built and published when tags are pushed:

```bash
# Create and push a release tag
git tag -a v1.0.1 -m "Release v1.0.1"
git push origin v1.0.1

# Automated GitHub Actions will:
# 1. Build binaries for Linux, macOS, Windows
# 2. Create Python packages
# 3. Generate checksums
# 4. Publish to GitHub Releases
# 5. Publish to PyPI
```

## Release Files

Each release includes:

| File | Description |
|------|-------------|
| `bookstack-migrate-{version}-linux` | Linux x64 binary |
| `bookstack-migrate-{version}-macos` | macOS x64 binary |
| `bookstack-migrate-{version}-windows.exe` | Windows x64 executable |
| `bookstack_migrate-{version}-py3-none-any.whl` | Python wheel package |
| `bookstack-migrate-{version}.tar.gz` | Python source archive |
| `SHA256SUMS` | Checksums for all files |

## Version History

See [CHANGELOG](CHANGELOG.md) for version history and updates.
