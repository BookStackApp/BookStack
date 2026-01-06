# BookStack Migration Tool - Architecture

## Overview

Clean, standalone Python-based migration tool for BookStack → DokuWiki.

```
bookstack-migrate/
├── 📄 Documentation
│   ├── README.md              # Quick start & usage
│   ├── RELEASES.md            # Download & release info
│   ├── CONTRIBUTING.md        # Development & release guide
│   └── ARCHITECTURE.md        # This file
│
├── 🐍 Core Application
│   └── bookstack-migrate      # Single executable (3.6KB)
│       ├── detect_dokuwiki()  # Find DokuWiki installations
│       ├── cmd_detect()       # List found installations
│       ├── cmd_export()       # Export BookStack content
│       ├── DokuWikiInstall    # Data model for installations
│       └── main()             # CLI entry point
│
├── 📦 Packaging & Distribution
│   ├── pyproject.toml         # Modern Python package config
│   ├── requirements.txt       # Optional dependencies
│   └── .github/workflows/
│       └── release.yml        # Automated release pipeline
│
├── 🔧 Build & Testing
│   ├── build/
│   │   ├── all.sh             # Complete build pipeline
│   │   ├── binaries.sh        # PyInstaller cross-platform
│   │   ├── docker-test.sh     # Integration testing
│   │   └── release.sh         # Release artifact builder
│   └── docker-compose.yml     # Dev environment
│       ├── mysql:8.0          # Database (3306)
│       ├── bookstack:latest   # BookStack app (8000)
│       └── dokuwiki:latest    # DokuWiki target (8080)
│
└── 📋 Supporting Files
    ├── LICENSE                # MIT License
    ├── .gitignore             # Git exclusions
    └── .gitattributes         # Git attributes
```

## Core Features

### Detection (`detect_dokuwiki()`)
- **APT**: `/var/lib/dokuwiki`
- **Manual**: `/var/www/dokuwiki`
- **Docker**: Container detection
- **Custom**: Environment-specified paths
- Returns: `DokuWikiInstall` with path, permissions, type

### Export (`cmd_export()`)
- Connects to BookStack database
- Extracts: Pages, images, metadata
- Formats for DokuWiki compatibility
- Handles nested hierarchy

### Data Model
```python
@dataclass
class DokuWikiInstall:
    path: Path              # Installation root
    pages_dir: Path         # Wiki pages directory
    media_dir: Path         # Media/images directory
    install_type: str       # 'apt', 'manual', 'docker', 'custom'
    writable: bool          # Write permission check
```

## Technology Stack

| Component | Tech | Version |
|-----------|------|---------|
| **Language** | Python | 3.8-3.12 |
| **Packaging** | setuptools/wheel | Latest |
| **Binaries** | PyInstaller | 5.0+ |
| **Database** | mysql-connector-python | 8.0+ (optional) |
| **Testing** | Docker Compose | 3.8+ |
| **CI/CD** | GitHub Actions | Latest |

## Build Pipeline

### Local Build
```bash
bash build/all.sh
```

Steps:
1. **Setup**: Install dependencies via pip
2. **Lint**: Python syntax validation
3. **Test**: Docker-based integration tests
4. **Binary**: PyInstaller cross-platform builds
5. **Package**: Python wheel + tar.gz

### Release Pipeline (GitHub Actions)

Triggered on: `git push origin v*` (version tags)

```
Tag v1.0.1 pushed
    ↓
GitHub Actions workflow starts
    ├─ Build Linux binary (ubuntu-latest)
    ├─ Build macOS binary (macos-latest)
    ├─ Build Windows binary (windows-latest)
    ├─ Create Python packages (.whl, .tar.gz)
    ├─ Generate SHA256SUMS checksums
    ├─ Create GitHub Release with all artifacts
    └─ Publish to PyPI
```

## Distribution Formats

### 1. Binary Executables
- **No dependencies** required
- **Cross-platform**: Linux, macOS, Windows
- **Standalone**: Single file, ready to run

### 2. Python Package (PyPI)
- Install: `pip install bookstack-migrate`
- Works: Python 3.8+
- Optional: `pip install bookstack-migrate[mysql]`

### 3. Docker Environment
- All-in-one: MySQL + BookStack + DokuWiki
- Testing: Integration environment ready
- Command: `docker-compose up -d`

## Configuration

### Python Package Metadata (`pyproject.toml`)

```toml
[project]
name = "bookstack-migrate"
version = "1.0.0"
requires-python = ">=3.8"

[project.optional-dependencies]
mysql = ["mysql-connector-python>=8.0.0"]
dev = ["pytest>=7.0", "pyinstaller>=5.0"]

[project.scripts]
bookstack-migrate = "bookstack_migrate:main"
```

### Docker Environment (`docker-compose.yml`)

Services:
- **MySQL 8.0**: Shared database for both platforms
- **BookStack**: Current version (solidnerd/bookstack:latest)
- **DokuWiki**: Migration target (linuxserver/dokuwiki:latest)

Ports:
- BookStack: `http://localhost:8000`
- DokuWiki: `http://localhost:8080`
- MySQL: `localhost:3306`

## Release Versioning

**Version Format**: `MAJOR.MINOR.PATCH` (e.g., `1.0.1`)

**Release Process**:
1. Update version in `pyproject.toml`
2. Commit: `git commit -m "version: bump to 1.0.1"`
3. Tag: `git tag -a v1.0.1 -m "Release 1.0.1"`
4. Push: `git push origin v1.0.1`
5. ✅ Automated: GitHub Actions builds & publishes

## Key Files & Responsibilities

| File | Purpose | Size |
|------|---------|------|
| `bookstack-migrate` | Main executable | 3.6KB |
| `pyproject.toml` | Package config | 1.5KB |
| `docker-compose.yml` | Dev environment | 1.4KB |
| `build/release.sh` | Release builder | 2.7KB |
| `.github/workflows/release.yml` | CI/CD automation | 2.1KB |

## Development Workflow

### Setup
```bash
git clone https://github.com/alvonellos/BookStack.git
cd BookStack
git checkout feature/standalone
python -m venv venv
source venv/bin/activate
pip install -e ".[dev]"
```

### Develop
```bash
# Test locally
python bookstack-migrate detect

# Run integration tests
bash build/docker-test.sh

# Build everything
bash build/all.sh
```

### Release
```bash
# Update version
sed -i 's/"1.0.0"/"1.0.1"/' pyproject.toml

# Commit & tag
git add pyproject.toml
git commit -m "version: bump to 1.0.1"
git tag -a v1.0.1 -m "Release 1.0.1"

# Push (triggers automation)
git push origin v1.0.1
```

## Deployment

### Users Download From

1. **GitHub Releases**: Pre-built binaries + packages
   - https://github.com/alvonellos/BookStack/releases

2. **PyPI**: Python package index
   - https://pypi.org/project/bookstack-migrate/

3. **Docker Hub**: Via docker-compose
   - MySQL, BookStack, DokuWiki images

### Verification

```bash
# Verify binary integrity
sha256sum -c SHA256SUMS

# Verify PyPI package
pip install bookstack-migrate
bookstack-migrate --version
```

## Future Enhancements

- [ ] Support additional export formats
- [ ] Incremental migration/sync
- [ ] Web UI for configuration
- [ ] Advanced permission mapping
- [ ] Custom field mappings
- [ ] Rollback capability
- [ ] Migration progress tracking
