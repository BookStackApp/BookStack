#!/bin/bash
# Build standalone binaries using PyInstaller

set -e

echo "🔨 Building standalone binaries..."

PYTHON_BIN=""
if command -v python3 >/dev/null 2>&1; then
    PYTHON_BIN="python3"
else
    PYTHON_BIN="python"
fi

# Check dependencies
if ! command -v pyinstaller &> /dev/null; then
    echo "Installing PyInstaller..."
    "$PYTHON_BIN" -m pip install --upgrade pip
    "$PYTHON_BIN" -m pip install pyinstaller
fi

# Create dist directory
mkdir -p dist

OS=$(uname -s)
ARCH=$(uname -m)
BIN_NAME="bookstack-migrate-linux"

if [ "$OS" = "Darwin" ]; then
    if [ "$ARCH" = "arm64" ]; then
        BIN_NAME="bookstack-migrate-macos-arm64"
    else
        BIN_NAME="bookstack-migrate-macos"
    fi
fi

echo "Building $BIN_NAME..."
pyinstaller \
    --onefile \
    --name "$BIN_NAME" \
    --specpath build/specs \
    --distpath dist \
    --workpath build/pybuild \
    --noupx \
    bookstack_migrate.py

chmod +x "dist/$BIN_NAME" || true

echo "✅ Binary built: dist/$BIN_NAME"
ls -lh "dist/$BIN_NAME" || true

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
