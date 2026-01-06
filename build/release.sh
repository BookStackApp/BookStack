#!/bin/bash
# Create release artifacts with checksums

set -e

echo "📦 Creating release artifacts..."

# Build everything
bash build/all.sh

# Create release directory
mkdir -p release
cd dist

# Generate checksums
echo "Generating checksums..."
sha256sum bookstack-migrate-linux > ../release/checksums.txt
sha256sum bookstack_migrate-*.whl >> ../release/checksums.txt
sha256sum bookstack_migrate-*.tar.gz >> ../release/checksums.txt

# Create archive
echo "Creating release archive..."
tar czf ../release/bookstack-migrate-release.tar.gz \
    bookstack-migrate-linux \
    bookstack_migrate-*.whl \
    bookstack_migrate-*.tar.gz

cd ..

echo "✅ Release artifacts created in release/"
ls -lh release/
