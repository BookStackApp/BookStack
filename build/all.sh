#!/bin/bash
# Full build and test pipeline

set -e

echo "📦 BookStack Migration Tool - Full Build Pipeline"
echo ""

# Setup
echo "🔧 Setting up environment..."
if [ ! -d venv ]; then
    python3 -m venv venv
fi
source venv/bin/activate
python -m pip install -q --upgrade pip
python -m pip install -q -e ".[dev]"
python -m pip install -q pylint
python -m pip install -q build

# Lint
echo "📝 Running linters..."
python -m pylint bookstack_migrate.py --disable=all --enable=syntax-error || true

# Unit tests
echo "🧪 Running unit tests..."
python -m pytest tests/ -v

# Build
echo "🔨 Building package..."
python -m build

# Binaries
echo "📦 Building standalone binaries..."
bash build/binaries.sh

echo ""
echo "✅ Build complete!"
echo "   - Package: dist/"
echo "   - Binary: dist/bookstack-migrate-linux"
