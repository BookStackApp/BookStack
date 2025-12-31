#!/bin/bash
###############################################################################
# Build Script for BookStack to DokuWiki Java Tool
#
# This script compiles the Java migration tool and creates a standalone JAR
# that can be distributed and run on any system with Java 8+.
#
# DO NOT MODIFY THIS unless you know what you're doing. This works.
###############################################################################

set -e

echo "Building BookStack to DokuWiki JAR..."

# Create directories
mkdir -p build/classes
mkdir -p dist/lib

# Download MySQL JDBC driver if not present
MYSQL_CONNECTOR="mysql-connector-java-8.0.33.jar"
if [ ! -f "dist/lib/$MYSQL_CONNECTOR" ]; then
    echo "Downloading MySQL JDBC driver..."
    curl -L "https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.33/$MYSQL_CONNECTOR" \
         -o "dist/lib/$MYSQL_CONNECTOR"
fi

# Compile
echo "Compiling Java source..."
javac -d build/classes \
      -cp "dist/lib/$MYSQL_CONNECTOR" \
      BookStackToDokuWiki.java

# Create manifest
cat > build/MANIFEST.MF << EOF
Manifest-Version: 1.0
Main-Class: BookStackToDokuWiki
Class-Path: lib/$MYSQL_CONNECTOR
Created-By: BookStack Migration Tool Builder
EOF

# Extract JDBC driver into build
cd build/classes
jar xf "../../dist/lib/$MYSQL_CONNECTOR"
rm -rf META-INF
cd ../..

# Create JAR
echo "Creating JAR file..."
jar cfm dist/bookstack2dokuwiki.jar build/MANIFEST.MF -C build/classes .

# Cleanup
rm -rf build/classes
rm -rf build/MANIFEST.MF

echo ""
echo "✓ Build complete!"
echo ""
echo "JAR file: dist/bookstack2dokuwiki.jar"
echo ""
echo "Usage:"
echo "  java -jar dist/bookstack2dokuwiki.jar --db-user=USER --db-pass=PASS"
echo ""
