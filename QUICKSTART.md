# Quick Start Guide

## 🚀 One-Minute Setup

### Option 1: Standalone Binary (Easiest)
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
cd BookStack && git checkout feature/standalone
pip install -e .

# Set environment variables
export BOOKSTACK_TOKEN_ID="your_api_token_id"
export BOOKSTACK_TOKEN_SECRET="your_api_token_secret"

# Run
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

## 🔑 API Token Setup

### Generate BookStack API Token

1. **Log in to BookStack** as an admin user
2. **Go to Settings** → **Users** → **[Your User]** → **API Tokens**
3. **Click "Create Token"**
4. **Copy the ID and Secret**
5. **Export them**:
   ```bash
   export BOOKSTACK_TOKEN_ID="abc123def456"
   export BOOKSTACK_TOKEN_SECRET="xyz789uvw012"
   export BOOKSTACK_BASE_URL="https://your-bookstack.example.com"
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

**API token errors?**
```bash
# Verify env vars are set
echo $BOOKSTACK_TOKEN_ID
echo $BOOKSTACK_TOKEN_SECRET

# Test API connection
python bookstack-migrate export --db test --user admin --password test
```

**DokuWiki not detected?**
```bash
# Check common paths
ls -la /var/lib/dokuwiki      # APT install
ls -la /var/www/dokuwiki      # Manual install
ls -la /data/dokuwiki         # Docker
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

1. ✅ Generate your API token (Settings → Users → API Tokens)
2. ✅ Set environment variables
3. ✅ Detect your DokuWiki installation
4. ✅ Export BookStack content
5. ✅ Load into DokuWiki
6. ✅ Verify migration
7. ✅ Report feedback/issues

Happy migrating! 🎉
