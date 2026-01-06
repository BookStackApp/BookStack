# Quick Start Guide

## 🚀 One-Minute Setup

### Option 1: Standalone Binary (Easiest)
```bash
# Download from releases
wget https://github.com/alvonellos/BookStack/releases/download/v1.0.0/bookstack-migrate-linux
chmod +x bookstack-migrate-linux

# Run
./bookstack-migrate-linux detect
```

### Option 2: Python Package
```bash
pip install bookstack-migrate
bookstack-migrate detect
```

### Option 3: From Source
```bash
git clone https://github.com/alvonellos/BookStack.git
cd BookStack && git checkout feature/standalone
python bookstack-migrate detect
```

---

## 📋 Common Commands

### Detect DokuWiki
```bash
bookstack-migrate detect
# Lists all found installations with paths and permissions
```

### Export BookStack
```bash
bookstack-migrate export \
  --db bookstack_db \
  --user root \
  --password secret \
  --output ./export
```

### Show Help
```bash
bookstack-migrate help
```

### Check Version
```bash
bookstack-migrate version
```

---

## 🐳 Docker Environment (Full Stack)

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

---

## 🔍 Troubleshooting

**Tool not found?**
```bash
# Make executable
chmod +x ./bookstack-migrate-linux

# Use absolute path
./bookstack-migrate-linux --help
```

**Python install issues?**
```bash
# Check Python version (need 3.8+)
python --version

# Try with pip3
pip3 install bookstack-migrate
```

**DokuWiki not detected?**
```bash
# Check common paths
ls -la /var/lib/dokuwiki      # APT install
ls -la /var/www/dokuwiki      # Manual install
ls -la /data/dokuwiki         # Docker

# Specify custom path
export DOKUWIKI_PATH=/custom/path
bookstack-migrate detect
```

---

## 📚 Learn More

- **README.md** - Full usage documentation
- **RELEASES.md** - Download & installation options
- **CONTRIBUTING.md** - Development & releases
- **ARCHITECTURE.md** - Technical design
- **GitHub Issues** - Ask questions

---

## 🎯 Next Steps

1. ✅ Detect your DokuWiki installation
2. ✅ Export BookStack content
3. ✅ Load into DokuWiki
4. ✅ Verify migration
5. ✅ Report issues/feedback

Happy migrating! 🎉
