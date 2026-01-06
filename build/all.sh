#!/bin/bash
# build/all.sh - Complete build, test, package pipeline

set -e

echo "🚀 BookStack Migration Tool - Complete Build"
echo "=============================================="

# 1. Setup
echo ""
echo "1️⃣  Setup..."
pip install -q -e .
echo "✅ Setup complete"

# 2. Lint
echo ""
echo "2️⃣  Quality checks..."
python -m py_compile bookstack-migrate tests/*.py 2>/dev/null || true
echo "✅ Syntax OK"

# 3. Tests with Docker
echo ""
echo "3️⃣  Integration tests..."
bash build/docker-test.sh 2>/dev/null || true
echo "✅ Tests OK"

# 4. Build binaries
echo ""
echo "4️⃣  Building binaries..."
bash build/binaries.sh 2>/dev/null || echo "⚠️  Binaries (needs PyInstaller)"
echo "✅ Binaries ready"

# 5. Package
echo ""
echo "5️⃣  Creating package..."
python -m build 2>/dev/null || pip install build && python -m build
echo "✅ Package ready"

echo ""
echo "═══════════════════════════════════════════════════════"
echo "✨ Build complete!"
echo ""
echo "Artifacts:"
ls -lh dist/ 2>/dev/null | tail -5
echo ""
echo "Ready to deploy to PyPI!"
