#!/bin/bash
# Create release artifacts with checksums

set -e

echo "📦 Creating release artifacts..."

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOOL_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Build everything
bash "$TOOL_ROOT/build/all.sh"

# Create release directory
mkdir -p "$TOOL_ROOT/release"
cd "$TOOL_ROOT/dist"

# Generate checksums
echo "Generating checksums..."
rm -f ../release/checksums.txt

# Include any built platform binaries (may be absent if PyInstaller was skipped)
shopt -s nullglob
BINARIES=(bookstack-migrate-*)
shopt -u nullglob

if [ ${#BINARIES[@]} -gt 0 ]; then
    sha256sum "${BINARIES[@]}" >> ../release/checksums.txt
else
    echo "⚠️  No platform binaries found (PyInstaller may have been skipped)." >&2
fi

sha256sum bookstack_migrate-*.whl >> ../release/checksums.txt
sha256sum bookstack_migrate-*.tar.gz >> ../release/checksums.txt

# Create archive
echo "Creating release archive..."
tar czf ../release/bookstack-migrate-release.tar.gz \
    ${BINARIES[@]} \
    bookstack_migrate-*.whl \
    bookstack_migrate-*.tar.gz

cd ..

echo "✅ Release artifacts created in release/"
ls -lh release/
