# Makefile for BookStack to DokuWiki Migration Tool (C)
# Compiles bookstack2dokuwiki.c into a native binary

# Compiler settings
CC = gcc
CFLAGS = -Wall -Wextra -Wpedantic -std=c11 -O2
LDFLAGS = $(shell mysql_config --libs)
INCLUDES = $(shell mysql_config --cflags)

# Target binary
TARGET = bookstack2dokuwiki
SRC = bookstack2dokuwiki.c

# Installation paths
PREFIX = /usr/local
BINDIR = $(PREFIX)/bin

# Build targets
.PHONY: all clean install uninstall debug release test

# Default target
all: $(TARGET)

# Main build rule
$(TARGET): $(SRC)
	@echo "Compiling $(TARGET)..."
	$(CC) $(CFLAGS) $(INCLUDES) -o $(TARGET) $(SRC) $(LDFLAGS)
	@echo "Build complete: $(TARGET)"
	@echo ""
	@echo "Usage: ./$(TARGET) --help"

# Debug build with symbols and no optimization
debug: CFLAGS = -Wall -Wextra -Wpedantic -std=c11 -g -O0 -DDEBUG
debug: $(SRC)
	@echo "Building debug version..."
	$(CC) $(CFLAGS) $(INCLUDES) -o $(TARGET)-debug $(SRC) $(LDFLAGS)
	@echo "Debug build complete: $(TARGET)-debug"

# Release build with maximum optimization
release: CFLAGS = -Wall -Wextra -Wpedantic -std=c11 -O3 -march=native -DNDEBUG
release: $(SRC)
	@echo "Building optimized release version..."
	$(CC) $(CFLAGS) $(INCLUDES) -o $(TARGET) $(SRC) $(LDFLAGS)
	strip $(TARGET)
	@echo "Release build complete (stripped): $(TARGET)"

# Install to system
install: $(TARGET)
	@echo "Installing $(TARGET) to $(BINDIR)..."
	install -d $(BINDIR)
	install -m 755 $(TARGET) $(BINDIR)
	@echo "Installation complete. Run: $(TARGET) --help"

# Uninstall from system
uninstall:
	@echo "Removing $(TARGET) from $(BINDIR)..."
	rm -f $(BINDIR)/$(TARGET)
	@echo "Uninstall complete."

# Clean build artifacts
clean:
	@echo "Cleaning build artifacts..."
	rm -f $(TARGET) $(TARGET)-debug *.o core
	@echo "Clean complete."

# Test build (requires test suite)
test: $(TARGET)
	@echo "Running tests..."
	@if [ -f "test_runner.sh" ]; then \
		./test_runner.sh; \
	else \
		echo "No test suite found. Skipping tests."; \
		echo "To run manually: ./$(TARGET) --help"; \
	fi

# Static analysis (requires cppcheck)
check: $(SRC)
	@echo "Running static analysis..."
	@if command -v cppcheck > /dev/null 2>&1; then \
		cppcheck --enable=all --suppress=missingIncludeSystem $(SRC); \
	else \
		echo "cppcheck not found. Install with: sudo apt-get install cppcheck"; \
	fi

# Memory leak check (requires valgrind)
memcheck: $(TARGET)
	@echo "Running memory leak detection..."
	@if command -v valgrind > /dev/null 2>&1; then \
		echo "Note: You need to run with actual arguments:"; \
		echo "valgrind --leak-check=full ./$(TARGET) -h localhost -u user -p pass -d db -o /tmp/test"; \
	else \
		echo "valgrind not found. Install with: sudo apt-get install valgrind"; \
	fi

# Display build information
info:
	@echo "Build Configuration:"
	@echo "  Compiler: $(CC)"
	@echo "  Flags: $(CFLAGS)"
	@echo "  Includes: $(INCLUDES)"
	@echo "  Libraries: $(LDFLAGS)"
	@echo "  Target: $(TARGET)"
	@echo "  Install path: $(BINDIR)"
	@echo ""
	@echo "MySQL Configuration:"
	@mysql_config --version 2>/dev/null || echo "  mysql_config not found"

# Help target
help:
	@echo "BookStack to DokuWiki Migration Tool - Makefile"
	@echo ""
	@echo "Available targets:"
	@echo "  make              - Build the binary (default)"
	@echo "  make all          - Same as default"
	@echo "  make debug        - Build with debug symbols"
	@echo "  make release      - Build optimized release version"
	@echo "  make install      - Install to $(BINDIR)"
	@echo "  make uninstall    - Remove from $(BINDIR)"
	@echo "  make clean        - Remove build artifacts"
	@echo "  make test         - Run test suite"
	@echo "  make check        - Run static analysis (cppcheck)"
	@echo "  make memcheck     - Run memory leak detection (valgrind)"
	@echo "  make info         - Display build configuration"
	@echo "  make help         - Display this help message"
	@echo ""
	@echo "Custom builds:"
	@echo "  make CFLAGS=\"-O3 -march=native\"  - Build with custom flags"
	@echo "  make PREFIX=/opt/local            - Install to custom prefix"
	@echo ""
	@echo "Requirements:"
	@echo "  - GCC or compatible C compiler"
	@echo "  - MySQL/MariaDB development libraries (libmysqlclient-dev)"
	@echo "  - mysql_config tool (from MySQL/MariaDB)"
	@echo ""
	@echo "Installation:"
	@echo "  Debian/Ubuntu: sudo apt-get install build-essential libmysqlclient-dev"
	@echo "  RedHat/Fedora: sudo dnf install gcc make mysql-devel"
	@echo "  macOS: brew install mysql-client"
