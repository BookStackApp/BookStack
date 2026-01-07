#!/bin/bash
# Full build and test pipeline

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOOL_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "📦 BookStack Migration Tool - Full Build Pipeline"
echo ""

cd "$TOOL_ROOT"

# Setup
echo "🔧 Setting up environment..."
if [ ! -d "$TOOL_ROOT/venv" ]; then
    python3 -m venv "$TOOL_ROOT/venv"
fi
source "$TOOL_ROOT/venv/bin/activate"
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
