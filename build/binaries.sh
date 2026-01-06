#!/bin/bash
# build/binaries.sh - Generate executables for Mac, Windows, Linux

set -e

echo "📦 Building binaries for all platforms..."

pip install pyinstaller

# Linux x64
echo "🐧 Linux x64..."
pyinstaller --onefile --name bookstack-migrate-linux bookstack-migrate
mv dist/bookstack-migrate-linux dist/bookstack-migrate-linux-x64

# Can add macOS/Windows cross-compilation here with additional setup
# For now, PyInstaller builds for current platform

echo "✅ Binaries ready in dist/"
ls -lh dist/bookstack-migrate*
