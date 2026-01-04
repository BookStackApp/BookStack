#!/bin/bash
# Build script for BookStack DokuWiki Exporter (Java)

set -e

echo "Building BookStack DokuWiki Exporter..."
echo ""

# Check for Maven
if command -v mvn > /dev/null 2>&1; then
    echo "Using Maven build..."
    mvn clean package
    echo ""
    echo "Build complete!"
    echo "JAR location: target/dokuwiki-exporter-1.0.0-jar-with-dependencies.jar"
    echo ""
    echo "Run with:"
    echo "  java -jar target/dokuwiki-exporter-1.0.0-jar-with-dependencies.jar --help"
    exit 0
fi

# Check for javac
if ! command -v javac > /dev/null 2>&1; then
    echo "Error: Java compiler not found!"
    echo "Please install JDK 11 or higher"
    exit 1
fi

echo "Maven not found. Using manual compilation..."
echo ""

# Create lib directory if it doesn't exist
mkdir -p lib

# Check for required JARs
MISSING_DEPS=0
if [ ! -f "lib/commons-cli-1.5.0.jar" ]; then
    echo "Missing: lib/commons-cli-1.5.0.jar"
    MISSING_DEPS=1
fi
if [ ! -f "lib/jsoup-1.15.3.jar" ]; then
    echo "Missing: lib/jsoup-1.15.3.jar"
    MISSING_DEPS=1
fi
if [ ! -f "lib/mysql-connector-j-8.0.33.jar" ]; then
    echo "Missing: lib/mysql-connector-j-8.0.33.jar"
    MISSING_DEPS=1
fi

if [ $MISSING_DEPS -eq 1 ]; then
    echo ""
    echo "Please download the required JAR files to the lib/ directory:"
    echo "  - Apache Commons CLI: https://commons.apache.org/proper/commons-cli/"
    echo "  - JSoup: https://jsoup.org/"
    echo "  - MySQL Connector/J: https://dev.mysql.com/downloads/connector/j/"
    echo ""
    echo "Or install Maven and run: mvn clean package"
    exit 1
fi

# Compile
echo "Compiling..."
javac -cp ".:lib/*" -d . com/bookstack/export/DokuWikiExporter.java

echo ""
echo "Build complete!"
echo ""
echo "Run with:"
echo "  java -cp \".:lib/*\" com.bookstack.export.DokuWikiExporter --help"
