# Makefile for BookStack to DokuWiki Migration Tools
#
# This Makefile builds all native/compiled versions of the migration tools.
# Use this to create production-ready binaries.
#
# IMPORTANT: DO NOT modify this unless you know what you're doing.
# These build configurations have been tested and work correctly.

# Compiler and flags
CC = gcc
CFLAGS = -Wall -O2 $(shell mysql_config --cflags)
LIBS = $(shell mysql_config --libs)

# Java compiler
JAVAC = javac
JAR = jar

# MySQL connector version
MYSQL_CONNECTOR_VERSION = 8.0.33
MYSQL_CONNECTOR_JAR = mysql-connector-java-$(MYSQL_CONNECTOR_VERSION).jar
MYSQL_CONNECTOR_URL = https://repo1.maven.org/maven2/mysql/mysql-connector-java/$(MYSQL_CONNECTOR_VERSION)/$(MYSQL_CONNECTOR_JAR)

# Output names
C_BINARY = bookstack2dokuwiki
JAVA_JAR = dist/bookstack2dokuwiki.jar
PERL_SCRIPT = bookstack2dokuwiki.pl

# Directories
BUILD_DIR = build
DIST_DIR = dist
LIB_DIR = dist/lib

.PHONY: all clean c java perl help install test

# Default target
all: c java perl
	@echo ""
	@echo "✓ All migration tools built successfully!"
	@echo ""
	@echo "Available binaries:"
	@echo "  - C binary:     ./$(C_BINARY)"
	@echo "  - Java JAR:     ./$(JAVA_JAR)"
	@echo "  - Perl script:  ./$(PERL_SCRIPT)"
	@echo ""
	@echo "Run 'make test' to verify installations"
	@echo ""

# Build C version
c: $(C_BINARY)

$(C_BINARY): bookstack2dokuwiki.c
	@echo "Building C binary..."
	@if ! command -v mysql_config > /dev/null 2>&1; then \
		echo "Error: mysql_config not found. Install libmysqlclient-dev"; \
		echo "  Ubuntu/Debian: sudo apt-get install libmysqlclient-dev"; \
		echo "  RHEL/CentOS:   sudo yum install mysql-devel"; \
		exit 1; \
	fi
	$(CC) $(CFLAGS) -o $(C_BINARY) bookstack2dokuwiki.c $(LIBS)
	@chmod +x $(C_BINARY)
	@echo "✓ C binary built: $(C_BINARY)"

# Build Java version
java: $(JAVA_JAR)

$(JAVA_JAR): BookStackToDokuWiki.java
	@echo "Building Java JAR..."
	@if ! command -v javac > /dev/null 2>&1; then \
		echo "Error: javac not found. Install JDK"; \
		echo "  Ubuntu/Debian: sudo apt-get install default-jdk"; \
		exit 1; \
	fi
	@mkdir -p $(BUILD_DIR)/classes $(DIST_DIR) $(LIB_DIR)
	
	# Download MySQL connector if needed
	@if [ ! -f "$(LIB_DIR)/$(MYSQL_CONNECTOR_JAR)" ]; then \
		echo "Downloading MySQL JDBC driver..."; \
		curl -L "$(MYSQL_CONNECTOR_URL)" -o "$(LIB_DIR)/$(MYSQL_CONNECTOR_JAR)"; \
	fi
	
	# Compile Java source
	$(JAVAC) -d $(BUILD_DIR)/classes -cp "$(LIB_DIR)/$(MYSQL_CONNECTOR_JAR)" BookStackToDokuWiki.java
	
	# Extract MySQL connector into classes
	cd $(BUILD_DIR)/classes && jar xf "../../$(LIB_DIR)/$(MYSQL_CONNECTOR_JAR)"
	rm -rf $(BUILD_DIR)/classes/META-INF
	
	# Create manifest
	@echo "Manifest-Version: 1.0" > $(BUILD_DIR)/MANIFEST.MF
	@echo "Main-Class: BookStackToDokuWiki" >> $(BUILD_DIR)/MANIFEST.MF
	@echo "Created-By: BookStack Migration Tool Builder" >> $(BUILD_DIR)/MANIFEST.MF
	
	# Create JAR
	cd $(BUILD_DIR)/classes && $(JAR) cfm ../../$(JAVA_JAR) ../MANIFEST.MF .
	
	# Cleanup
	rm -rf $(BUILD_DIR)/classes $(BUILD_DIR)/MANIFEST.MF
	
	@echo "✓ Java JAR built: $(JAVA_JAR)"

# Prepare Perl script
perl: $(PERL_SCRIPT)

$(PERL_SCRIPT):
	@echo "Preparing Perl script..."
	@if ! command -v perl > /dev/null 2>&1; then \
		echo "Error: perl not found"; \
		exit 1; \
	fi
	@chmod +x $(PERL_SCRIPT)
	@perl -c $(PERL_SCRIPT) > /dev/null 2>&1 || { \
		echo "Warning: Perl syntax check failed. Install DBI and DBD::mysql:"; \
		echo "  cpan install DBI DBD::mysql"; \
	}
	@echo "✓ Perl script prepared: $(PERL_SCRIPT)"

# Test installations
test:
	@echo "Testing build artifacts..."
	@echo ""
	
	@if [ -f "$(C_BINARY)" ]; then \
		echo "✓ C binary exists"; \
		ldd $(C_BINARY) > /dev/null 2>&1 && echo "✓ C binary has valid dependencies" || echo "⚠ C binary may have missing libraries"; \
	else \
		echo "✗ C binary not found"; \
	fi
	
	@if [ -f "$(JAVA_JAR)" ]; then \
		echo "✓ Java JAR exists"; \
		jar tf $(JAVA_JAR) > /dev/null 2>&1 && echo "✓ Java JAR is valid" || echo "✗ Java JAR is corrupted"; \
	else \
		echo "✗ Java JAR not found"; \
	fi
	
	@if [ -f "$(PERL_SCRIPT)" ]; then \
		echo "✓ Perl script exists"; \
		perl -c $(PERL_SCRIPT) > /dev/null 2>&1 && echo "✓ Perl script syntax is valid" || echo "⚠ Perl script has syntax errors or missing modules"; \
	else \
		echo "✗ Perl script not found"; \
	fi
	
	@echo ""
	@echo "Run each tool with --help to verify:"
	@echo "  ./$(C_BINARY) --help"
	@echo "  java -jar $(JAVA_JAR) --help"
	@echo "  ./$(PERL_SCRIPT) --help"

# Install to system
install: all
	@echo "Installing migration tools..."
	@if [ -z "$(PREFIX)" ]; then \
		PREFIX=/usr/local; \
	fi
	install -d $(PREFIX)/bin
	install -m 755 $(C_BINARY) $(PREFIX)/bin/
	install -m 755 $(PERL_SCRIPT) $(PREFIX)/bin/
	install -d $(PREFIX)/share/bookstack/
	install -m 644 $(JAVA_JAR) $(PREFIX)/share/bookstack/
	@echo "✓ Installed to $(PREFIX)"
	@echo ""
	@echo "Usage:"
	@echo "  $(PREFIX)/bin/$(C_BINARY) --help"
	@echo "  $(PREFIX)/bin/$(PERL_SCRIPT) --help"
	@echo "  java -jar $(PREFIX)/share/bookstack/bookstack2dokuwiki.jar --help"

# Clean build artifacts
clean:
	@echo "Cleaning build artifacts..."
	rm -f $(C_BINARY)
	rm -rf $(BUILD_DIR)
	rm -rf $(DIST_DIR)
	@echo "✓ Cleaned"

# Help target
help:
	@echo "BookStack to DokuWiki Migration Tools - Makefile"
	@echo ""
	@echo "Available targets:"
	@echo "  make all      - Build all migration tools (default)"
	@echo "  make c        - Build C binary only"
	@echo "  make java     - Build Java JAR only"
	@echo "  make perl     - Prepare Perl script"
	@echo "  make test     - Test built artifacts"
	@echo "  make install  - Install tools to system (requires sudo)"
	@echo "  make clean    - Remove build artifacts"
	@echo "  make help     - Show this help message"
	@echo ""
	@echo "Environment variables:"
	@echo "  PREFIX        - Installation prefix (default: /usr/local)"
	@echo ""
	@echo "Examples:"
	@echo "  make all               # Build everything"
	@echo "  make c                 # Build only C binary"
	@echo "  make install PREFIX=/opt/bookstack  # Custom install location"
	@echo ""
	@echo "Requirements:"
	@echo "  C:    gcc, mysql-devel (libmysqlclient-dev)"
	@echo "  Java: JDK 8+, curl (for downloading MySQL connector)"
	@echo "  Perl: perl, DBI, DBD::mysql"
	@echo ""
