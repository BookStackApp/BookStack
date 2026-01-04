# Java Migration Tool

## DokuWikiExporter.java

Enterprise-grade BookStack to DokuWiki exporter for when PHP has difficulties.

### What it does

A robust, framework-independent Java application that connects directly to the BookStack database and exports content to DokuWiki format. This tool exists because sometimes you need something that doesn't depend on Laravel's "elegant" architecture having a good day.

### Features

- Direct database access (no framework dependencies)
- HTML parsing and cleanup using JSoup
- Namespace preservation
- Timestamp handling
- Comprehensive error reporting
- Verbose logging option
- Command-line interface
- Multi-threaded export capabilities

### Prerequisites

**Java Development Kit:**
```bash
# Java 11 or higher
java -version
javac -version
```

**Dependencies:**
- Apache Commons CLI (1.5.0)
- JSoup (1.15.3)
- MySQL Connector/J (8.0.33)

### Building

```bash
# Compile with dependencies
javac -cp ".:lib/*" com/bookstack/export/DokuWikiExporter.java

# Or use the provided Maven configuration
mvn clean package

# Or use the build script
./build.sh
```

### Usage

```bash
# Run the exporter
java -cp ".:lib/*:." com.bookstack.export.DokuWikiExporter \
    --host localhost \
    --port 3306 \
    --database bookstack \
    --user bookstack \
    --password secret \
    --output /path/to/dokuwiki/data

# With additional options
java -cp ".:lib/*:." com.bookstack.export.DokuWikiExporter \
    --host localhost \
    --database bookstack \
    --user bookstack \
    --password secret \
    --output /path/to/output \
    --preserve-timestamps \
    --verbose

# Show help
java -cp ".:lib/*:." com.bookstack.export.DokuWikiExporter --help
```

### Command-line Options

- `-h, --host` - Database host (default: localhost)
- `-P, --port` - Database port (default: 3306)
- `-d, --database` - Database name (required)
- `-u, --user` - Database user (required)
- `-p, --password` - Database password (required)
- `-o, --output` - Output directory path (required)
- `-t, --preserve-timestamps` - Preserve original timestamps
- `-v, --verbose` - Enable verbose logging

### Output Structure

```
output/
├── pages/
│   └── [namespaces]/
│       └── *.txt
├── media/
│   └── [namespaces]/
│       └── [files]
└── export-report.txt
```

### Building from Source

**Option 1: Maven (Recommended)**

```bash
mvn clean compile
mvn package
java -jar target/dokuwiki-exporter-1.0-jar-with-dependencies.jar [options]
```

**Option 2: Manual Compilation**

Download dependencies:
- [Apache Commons CLI](https://commons.apache.org/proper/commons-cli/)
- [JSoup](https://jsoup.org/)
- [MySQL Connector/J](https://dev.mysql.com/downloads/connector/j/)

Place JARs in `lib/` directory and compile as shown above.

### Maven Configuration

See `pom.xml` for complete dependency configuration.

### Performance Notes

- For large databases (>1000 pages), consider using `--verbose` to monitor progress
- The tool uses connection pooling for optimal performance
- Export time scales roughly linearly with content size

### Error Handling

The exporter will:
- Validate database connectivity before starting
- Create output directories if they don't exist
- Skip invalid or corrupted entries with warnings
- Provide detailed error messages and stack traces in verbose mode
- Generate an export report with statistics

### Troubleshooting

**ClassNotFoundException:**
- Ensure all JAR dependencies are in the classpath
- Check `lib/` directory contains required JARs

**SQLException:**
- Verify database credentials
- Check MySQL/MariaDB is running and accessible
- Ensure user has SELECT permissions on BookStack database

**OutOfMemoryError:**
- Increase heap size: `java -Xmx2g -cp ...`
- Process books individually if database is very large

### Author

Created for reliability when frameworks fail.

---

*"This code exists because frameworks are unreliable. Keep it simple."*
