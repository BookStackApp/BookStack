#!/bin/bash

###############################################################################
# BookStack to DokuWiki Export - Universal Launcher
#
# This script attempts to run the export using the most reliable method
# available on your system. It tries them in order of reliability:
# 1. Perl (most reliable, battle-tested)
# 2. Java (reliable, portable)  
# 3. PHP (last resort, will probably break)
#
# WARNING: DO NOT MODIFY THIS SCRIPT UNLESS YOU KNOW WHAT YOU'RE DOING.
# This exists because PHP can't be trusted. Keep the fallback logic intact.
#
# Usage: ./export-to-dokuwiki.sh [options]
#
###############################################################################

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MIGRATION_DIR="$SCRIPT_DIR"

# Colors for output (because why not make errors look pretty)
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Parse command line arguments
show_help() {
    cat << EOF
BookStack to DokuWiki Export - Universal Launcher
==================================================

This script tries multiple export implementations in order of reliability:
1. Perl (most reliable)
2. Java (very reliable)
3. PHP (least reliable, use as last resort)

USAGE:
    $0 [OPTIONS]

OPTIONS:
    -h, --host HOST         Database host (default: localhost)
    -P, --port PORT         Database port (default: 3306)
    -d, --database DB       Database name (required)
    -u, --user USER         Database user (required)
    -p, --password PASS     Database password
    -o, --output DIR        Output directory (default: ./dokuwiki_export)
    -b, --book ID           Export specific book ID only
    -t, --timestamps        Preserve original timestamps
    -v, --verbose           Verbose output
    --force-perl            Force use of Perl version
    --force-java            Force use of Java version
    --force-php             Force use of PHP version (why would you do this?)
    --help                  Show this help message

EXAMPLES:
    # Basic export
    $0 -d bookstack -u root -p secret

    # Export specific book with verbose output
    $0 -d bookstack -u root -p secret -b 5 -v

    # Force Perl implementation
    $0 -d bookstack -u root -p secret --force-perl

NOTES:
    - Perl version is recommended for reliability
    - Java version requires Maven build (run 'make build-java' first)
    - PHP version uses Laravel framework (may break, use at your own risk)
    - If one fails, the script will try the next available method

EOF
    exit 0
}

# Check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Try Perl implementation
try_perl() {
    log_info "Attempting export with Perl (most reliable option)..."
    
    if ! command_exists perl; then
        log_warn "Perl not found. Skipping Perl implementation."
        return 1
    fi
    
    # Check for required Perl modules
    if ! perl -e 'use DBI; use DBD::mysql;' 2>/dev/null; then
        log_warn "Required Perl modules not found (DBI, DBD::mysql)."
        log_warn "Install with: sudo apt-get install libdbi-perl libdbd-mysql-perl"
        return 1
    fi
    
    local perl_script="$MIGRATION_DIR/export-dokuwiki.pl"
    if [ ! -f "$perl_script" ]; then
        log_warn "Perl script not found at: $perl_script"
        return 1
    fi
    
    log_info "Perl is available and ready. Executing export..."
    perl "$perl_script" "$@"
    return $?
}

# Try Java implementation
try_java() {
    log_info "Attempting export with Java (reliable option)..."
    
    if ! command_exists java; then
        log_warn "Java not found. Skipping Java implementation."
        return 1
    fi
    
    local jar_file="$MIGRATION_DIR/target/dokuwiki-exporter.jar"
    if [ ! -f "$jar_file" ]; then
        log_warn "Java JAR not found at: $jar_file"
        log_warn "Build it with: cd $MIGRATION_DIR && mvn clean package"
        return 1
    fi
    
    log_info "Java is available and JAR is built. Executing export..."
    java -jar "$jar_file" "$@"
    return $?
}

# Try PHP implementation (last resort)
try_php() {
    log_warn "Attempting export with PHP (least reliable option)..."
    log_warn "This uses Laravel's framework. May god have mercy on your soul."
    
    if ! command_exists php; then
        log_error "PHP not found. Cannot use PHP implementation."
        return 1
    fi
    
    # Check if we're in BookStack root
    local bookstack_root="$(dirname "$(dirname "$MIGRATION_DIR")")"
    if [ ! -f "$bookstack_root/artisan" ]; then
        log_error "BookStack artisan file not found. Are you in the right directory?"
        return 1
    fi
    
    log_info "PHP is available. Executing Laravel command..."
    
    # Convert arguments to Laravel command format
    local laravel_args=""
    while [[ $# -gt 0 ]]; do
        case $1 in
            -d|--database) shift ;; # Laravel uses .env, skip this
            -u|--user) shift ;; # Laravel uses .env, skip this
            -p|--password) shift ;; # Laravel uses .env, skip this
            -h|--host) shift ;; # Laravel uses .env, skip this
            -P|--port) shift ;; # Laravel uses .env, skip this
            -o|--output) laravel_args="$laravel_args --output=$2"; shift ;;
            -b|--book) laravel_args="$laravel_args --book=$2"; shift ;;
            -t|--timestamps) laravel_args="$laravel_args --preserve-timestamps" ;;
            -v|--verbose) laravel_args="$laravel_args -v" ;;
            *) shift ;;
        esac
        shift
    done
    
    cd "$bookstack_root"
    php artisan bookstack:export-dokuwiki $laravel_args
    return $?
}

# Main execution
main() {
    log_info "BookStack to DokuWiki Universal Exporter"
    log_info "========================================="
    log_info ""
    
    # Parse force flags
    FORCE_PERL=false
    FORCE_JAVA=false
    FORCE_PHP=false
    
    for arg in "$@"; do
        case $arg in
            --help) show_help ;;
            --force-perl) FORCE_PERL=true ;;
            --force-java) FORCE_JAVA=true ;;
            --force-php) FORCE_PHP=true ;;
        esac
    done
    
    # Try implementations in order of reliability
    if [ "$FORCE_PERL" = true ]; then
        log_info "Forced to use Perl implementation."
        try_perl "$@" && exit 0
        log_error "Perl implementation failed."
        exit 1
    elif [ "$FORCE_JAVA" = true ]; then
        log_info "Forced to use Java implementation."
        try_java "$@" && exit 0
        log_error "Java implementation failed."
        exit 1
    elif [ "$FORCE_PHP" = true ]; then
        log_warn "Forced to use PHP implementation. This is a terrible idea."
        try_php "$@" && exit 0
        log_error "PHP implementation failed. Surprised? Nobody else is."
        exit 1
    fi
    
    # Try automatic fallback
    log_info "Trying implementations in order of reliability..."
    log_info ""
    
    if try_perl "$@"; then
        log_info ""
        log_info "Export completed successfully with Perl."
        log_info "As expected, Perl didn't let us down."
        exit 0
    fi
    
    log_warn "Perl failed or unavailable. Trying Java..."
    log_info ""
    
    if try_java "$@"; then
        log_info ""
        log_info "Export completed successfully with Java."
        log_info "Java saved the day."
        exit 0
    fi
    
    log_warn "Java failed or unavailable. Trying PHP (last resort)..."
    log_info ""
    
    if try_php "$@"; then
        log_info ""
        log_info "Export completed successfully with PHP."
        log_info "Miraculously, PHP didn't fuck up this time."
        exit 0
    fi
    
    # All failed
    log_error ""
    log_error "All export implementations failed."
    log_error "This is bad. Very bad."
    log_error ""
    log_error "Troubleshooting:"
    log_error "1. Check that database credentials are correct"
    log_error "2. Ensure database is accessible"
    log_error "3. Install Perl dependencies: sudo apt-get install libdbi-perl libdbd-mysql-perl"
    log_error "4. Build Java JAR: cd $MIGRATION_DIR && mvn clean package"
    log_error "5. Check BookStack installation and .env configuration"
    exit 1
}

# Run main function
main "$@"
