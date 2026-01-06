# Contributing & Release Guide

## Development Workflow

### Local Development

```bash
# Setup environment
python -m venv venv
source venv/bin/activate  # or on Windows: venv\Scripts\activate
pip install -r requirements.txt -e ".[dev]"

# Run tool
python bookstack-migrate detect

# Run tests
bash build/docker-test.sh

# Build locally
bash build/all.sh
```

### Code Style

```bash
# Format code (if using black)
pip install black
black bookstack-migrate

# Type check (if using mypy)
pip install mypy
mypy bookstack-migrate
```

## Creating a Release

### Step 1: Update Version

```bash
# Update version in pyproject.toml
sed -i 's/version = "1.0.0"/version = "1.0.1"/' pyproject.toml
git add pyproject.toml
git commit -m "version: bump to 1.0.1"
```

### Step 2: Tag Release

```bash
# Create annotated tag
git tag -a v1.0.1 -m "Release v1.0.1

- Feature X
- Bug fix Y
- Improvement Z"

# Push tag (triggers GitHub Actions)
git push origin v1.0.1
```

### Step 3: Automated Release Process

GitHub Actions automatically:
1. Builds binaries for Linux, macOS, Windows
2. Creates Python packages (wheel, tar.gz)
3. Generates SHA256 checksums
4. Creates GitHub Release with all artifacts
5. Publishes to PyPI

### Manual Release Build (Optional)

```bash
# Build all release artifacts locally
bash build/release.sh

# Creates releases/1.0.1/ with:
# - binaries/bookstack-migrate-*
# - python/*.whl, *.tar.gz
# - SHA256SUMS
```

## Release Checklist

- [ ] Update version in `pyproject.toml`
- [ ] Update `RELEASES.md` with new version info
- [ ] Test locally: `bash build/all.sh`
- [ ] Test docker-compose: `docker-compose up`
- [ ] Commit changes
- [ ] Create annotated git tag: `git tag -a v1.0.1 -m "Release message"`
- [ ] Push tag: `git push origin v1.0.1`
- [ ] Verify GitHub Actions workflow completes
- [ ] Verify GitHub Release created with artifacts
- [ ] Verify PyPI package published

## Testing Releases

### Test Binary Download

```bash
cd /tmp
wget https://github.com/alvonellos/BookStack/releases/download/v1.0.1/bookstack-migrate-1.0.1-linux
chmod +x bookstack-migrate-1.0.1-linux
./bookstack-migrate-1.0.1-linux --help
```

### Test PyPI Package

```bash
pip install --pre bookstack-migrate==1.0.1
bookstack-migrate --help
```

### Test Docker Environment

```bash
docker-compose up -d
sleep 30  # Wait for services to start

# Access:
# - BookStack: http://localhost:8000
# - DokuWiki: http://localhost:8080
# - MySQL: localhost:3306

docker-compose down
```

## File Structure for Releases

```
bookstack-migrate/
├── bookstack-migrate          # Main executable
├── pyproject.toml             # Python package config
├── README.md                  # Usage documentation
├── RELEASES.md                # Release documentation
├── requirements.txt           # Dependencies
├── build/
│   ├── all.sh                 # Complete build pipeline
│   ├── binaries.sh            # Binary build
│   ├── docker-test.sh         # Integration tests
│   └── release.sh             # Release builder
├── docker-compose.yml         # Dev environment (BookStack + DokuWiki)
└── .github/workflows/
    └── release.yml            # Automated release workflow
```

## Environment Variables

For GitHub Actions releases, set:
- `PYPI_API_TOKEN` - PyPI API token for publishing packages

## Support

For issues or questions:
- GitHub Issues: https://github.com/alvonellos/BookStack/issues
- Discussions: https://github.com/alvonellos/BookStack/discussions
