#!/bin/bash
# Build standalone binaries using zipapp or shiv

set -e

echo "🔨 Building standalone binaries..."

# Create dist directory
mkdir -p dist

# Method 1: Try shiv for a standalone .pyz file
if command -v shiv &> /dev/null; then
    echo "Building with shiv..."
    shiv -c bookstack-migrate -o dist/bookstack-migrate-linux .
    chmod +x dist/bookstack-migrate-linux
elif command -v python3 -m zipapp &> /dev/null; then
    echo "Building with zipapp..."
    python3 -m pip install -q . -t dist/bookstack_app
    cd dist/bookstack_app
    python3 -m zipapp -m bookstack_migrate:main -o bookstack-migrate-linux -c .
    cd ../..
else
    echo "⚠️  No standalone binary builder found"
    echo "Building Python distribution instead..."
    python -m build
fi

# Create portable shell wrapper
cat > dist/bookstack-migrate-linux-wrapper << 'EOF'
#!/bin/bash
# BookStack Migration Tool - Standalone Wrapper
exec python3 -m bookstack_migrate "$@"
EOF
chmod +x dist/bookstack-migrate-linux-wrapper

# Also create simple Python wrapper that works with pip
cat > dist/bookstack-migrate << 'EOF'
#!/usr/bin/env python3
import sys
from bookstack_migrate import main
sys.exit(main() or 0)
EOF
chmod +x dist/bookstack-migrate

echo "✅ Binaries/wrappers built:"
ls -lh dist/bookstack-migrate* || true
