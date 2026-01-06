#!/bin/bash
# build/release.sh - Create release builds and artifacts

set -e

VERSION=$(grep 'version' pyproject.toml | head -1 | cut -d'"' -f2)
RELEASE_DIR="releases/${VERSION}"

echo "📦 Building Release v${VERSION}"
echo "================================================"

# Create release directory
mkdir -p "${RELEASE_DIR}"

echo ""
echo "1️⃣  Building platform-specific binaries..."
mkdir -p "${RELEASE_DIR}/binaries"

# Windows
if command -v pyinstaller &> /dev/null; then
    echo "  🪟 Windows (x64)..."
    pyinstaller --onefile --name bookstack-migrate-${VERSION}-windows.exe bookstack-migrate
    mv dist/bookstack-migrate-${VERSION}-windows.exe "${RELEASE_DIR}/binaries/" 2>/dev/null || echo "  ⚠️  PyInstaller not available"
    
    echo "  🐧 Linux (x64)..."
    pyinstaller --onefile --name bookstack-migrate-${VERSION}-linux bookstack-migrate
    mv dist/bookstack-migrate-${VERSION}-linux "${RELEASE_DIR}/binaries/" 2>/dev/null || true
    
    echo "  🍎 macOS (x64)..."
    # macOS build would need to be on macOS machine
    echo "  ⚠️  macOS build requires macOS machine"
else
    echo "  ⚠️  PyInstaller not installed (pip install pyinstaller)"
fi

echo ""
echo "2️⃣  Building Python distributions..."
mkdir -p "${RELEASE_DIR}/python"
python -m build
cp dist/*.whl dist/*.tar.gz "${RELEASE_DIR}/python/" 2>/dev/null || true

echo ""
echo "3️⃣  Creating checksums..."
cd "${RELEASE_DIR}"
find . -type f \( -name "*.exe" -o -name "*.whl" -o -name "*.tar.gz" -o -name "bookstack-migrate*" \) ! -path "*binaries*" -prune -o -type f -print0 | \
    xargs -0 sha256sum > SHA256SUMS 2>/dev/null || \
    (ls -1 binaries/* python/* 2>/dev/null | xargs -I {} sha256sum {} > SHA256SUMS) || true

echo ""
echo "4️⃣  Creating archive..."
cd /workspaces/BookStack
tar -czf "${RELEASE_DIR}/bookstack-migrate-${VERSION}-complete.tar.gz" \
    bookstack-migrate \
    pyproject.toml \
    requirements.txt \
    README.md \
    LICENSE \
    docker-compose.yml \
    build/ 2>/dev/null || echo "  ⚠️  Archive creation (some files may be missing)"

echo ""
echo "═══════════════════════════════════════════════════════"
echo "✨ Release ${VERSION} artifacts ready!"
echo ""
echo "📍 Location: ${RELEASE_DIR}/"
echo ""
ls -lh "${RELEASE_DIR}/"
echo ""
echo "📥 Download links:"
echo "   - Binaries:  ${RELEASE_DIR}/binaries/"
echo "   - Python:    ${RELEASE_DIR}/python/"
echo "   - Complete:  ${RELEASE_DIR}/bookstack-migrate-${VERSION}-complete.tar.gz"
echo ""
echo "🚀 Next: Push releases to GitHub!"
