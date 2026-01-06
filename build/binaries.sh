#!/bin/bash
# Build standalone binaries using PyInstaller

set -e

echo "🔨 Building standalone binaries..."

# Check dependencies
if ! command -v pyinstaller &> /dev/null; then
    echo "Installing PyInstaller..."
    pip install pyinstaller
fi

# Create dist directory
mkdir -p dist

# Build Linux binary
echo "Building bookstack-migrate-linux..."
pyinstaller \
    --onefile \
    --name bookstack-migrate-linux \
    --specpath build/specs \
    --distpath dist \
    --workpath build/pybuild \
    --noupx \
    bookstack_migrate.py

# Make executable
chmod +x dist/bookstack-migrate-linux

echo "✅ Binary built: dist/bookstack-migrate-linux"
ls -lh dist/bookstack-migrate-linux
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
