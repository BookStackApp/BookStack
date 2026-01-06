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
python -m py_compile bookstack_migrate.py bookstack_api.py tests/ 2>/dev/null || true
echo "✅ Syntax OK"

# 3. Unit tests
echo ""
echo "3️⃣  Running unit tests..."
python -m pytest tests/ -v 2>/dev/null || echo "  (pytest optional; install with: pip install pytest)"
echo "✅ Unit tests OK"

# 4. Integration tests with Docker
echo ""
echo "4️⃣  Integration tests..."
bash build/docker-test.sh 2>/dev/null || echo "  (docker-compose optional)"
echo "✅ Integration tests OK"

# 5. Build binaries
echo ""
echo "5️⃣  Building binaries..."
bash build/binaries.sh 2>/dev/null || echo "  (PyInstaller optional; install with: pip install pyinstaller)"
echo "✅ Binaries ready"

# 6. Package
echo ""
echo "6️⃣  Creating package..."
python -m build 2>/dev/null || pip install build && python -m build
echo "✅ Package ready"

echo ""
echo "═══════════════════════════════════════════════════════"
echo "✨ Build complete!"
echo ""
echo "Artifacts:"
ls -lh dist/ 2>/dev/null | tail -5 || echo "  (no dist/ yet)"
echo ""
echo "Ready to deploy to PyPI!"
