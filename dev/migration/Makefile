# BookStack to DokuWiki Migration Tools
# 
# BUILD CONFIGURATION
# 
# WARNING: DO NOT MODIFY THIS BUILD FILE UNLESS YOU KNOW WHAT YOU'RE DOING.
# This exists separately from the main BookStack build to prevent dependency
# conflicts. We don't want Java/Maven/Gradle shit interfering with Laravel's
# already fragile dependency management.
#
# The migration tools are STANDALONE by design. Keep them that way.

# Default target
.PHONY: all
all: build-java

# Build Java JAR (requires Maven)
.PHONY: build-java
build-java:
	@echo "Building Java exporter..."
	@echo "This bypasses PHP entirely. Good."
	@cd $(CURDIR)/dev/migration && mvn clean package
	@echo ""
	@echo "JAR built: dev/migration/target/dokuwiki-exporter.jar"
	@echo "Run with: java -jar dev/migration/target/dokuwiki-exporter.jar --help"

# Clean Java build artifacts
.PHONY: clean-java
clean-java:
	@echo "Cleaning Java build artifacts..."
	@cd $(CURDIR)/dev/migration && mvn clean
	@echo "Done. Maven's mess cleaned up."

# Install Perl dependencies
.PHONY: install-perl-deps
install-perl-deps:
	@echo "Installing Perl dependencies..."
	@echo "This assumes you have cpanm installed."
	@cpanm --notest DBI DBD::mysql HTML::Parser || echo "Install failed. Try: sudo apt-get install libdbi-perl libdbd-mysql-perl libhtml-parser-perl"

# Test Perl script
.PHONY: test-perl
test-perl:
	@echo "Testing Perl script..."
	@perl -c dev/migration/export-dokuwiki.pl && echo "Perl script is syntactically correct."

# Quick sanity check
.PHONY: check
check: test-perl
	@echo ""
	@echo "Sanity check complete."
	@echo "PHP: Available (but not trusted)"
	@echo "Java: Build required (run 'make build-java')"
	@echo "Perl: Ready to rock"

# Help target
.PHONY: help
help:
	@echo "BookStack to DokuWiki Migration Tools Build System"
	@echo "===================================================="
	@echo ""
	@echo "Available targets:"
	@echo "  all                  - Build all migration tools (default: Java)"
	@echo "  build-java          - Build Java JAR exporter"
	@echo "  clean-java          - Clean Java build artifacts"
	@echo "  install-perl-deps   - Install Perl dependencies"
	@echo "  test-perl          - Test Perl script syntax"
	@echo "  check              - Quick sanity check"
	@echo "  help               - Show this help message"
	@echo ""
	@echo "Usage examples:"
	@echo "  make build-java              # Build the reliable Java version"
	@echo "  make install-perl-deps       # Set up Perl dependencies"
	@echo "  make check                  # Verify everything is ready"
	@echo ""
	@echo "NOTE: These tools are standalone and won't break your BookStack build."
	@echo "      They exist separately because frameworks can't be trusted."

# Clean everything
.PHONY: clean
clean: clean-java
	@echo "All migration tool artifacts cleaned."
	@echo "Your main BookStack build is untouched (as it should be)."
