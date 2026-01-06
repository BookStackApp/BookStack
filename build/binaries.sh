#!/bin/bash
# build/binaries.sh - Generate executables for Mac, Windows, Linux

set -e

echo "📦 Building binaries for all platforms..."

pip install -q pyinstaller

# Linux x64
echo "🐧 Linux x64..."
pyinstaller --onefile --name bookstack-migrate-linux bookstack-migrate
mv dist/bookstack-migrate-linux dist/bookstack-migrate-linux-x64 2>/dev/null || true

echo "✅ Binaries ready in dist/"
ls -lh dist/bookstack-migrate* 2>/dev/null || echo "  (build output)"
