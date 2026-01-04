#!/bin/bash
################################################################################
#
# 03-export.sh - Export BookStack Content to DokuWiki Format
#
# This script exports BookStack data using the best available export tool.
# It automatically selects the optimal tool based on what's available:
#   1. Perl (fastest, most reliable)
#   2. Java (slower but works)
#   3. C binary (fast if compiled)
#   4. PHP (last resort)
#
# Prerequisites:
#   - Run 01-setup.sh first to install dependencies
#   - Run 02-backup.sh to create a backup
#   - Have BookStack .env file in current directory
#
# Usage: ./03-export.sh [output_directory]
#
# Exit codes:
#   0 = Export succeeded
#   1 = Export failed
#   2 = Configuration error (missing .env or credentials)
#   3 = No suitable export tool found
#
################################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
EXPORT_DIR="${1:-${SCRIPT_DIR}/../../dokuwiki-export}"
SELECTED_TOOL=""
TOOL_PATH=""

# Stats
EXPORT_START_TIME=$(date +%s)
EXPORT_FILES=0
EXPORT_SIZE=0

################################################################################
# Utility Functions
################################################################################

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warn() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_step() {
    echo ""
    echo -e "${CYAN}${BOLD}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}${BOLD}║ $1${NC}"
    echo -e "${CYAN}${BOLD}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

################################################################################
# Banner
################################################################################

show_banner() {
    clear
    echo -e "${CYAN}${BOLD}"
    cat << 'EOF'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   📤 STAGE 3: EXPORT BOOKSTACK TO DOKUWIKI                       ║
║                                                                   ║
║   This script exports your BookStack content to DokuWiki format  ║
║   using the best available export tool.                          ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
}

################################################################################
# Configuration Validation
################################################################################

validate_configuration() {
    log_step "Validating Configuration"
    
    # Check for .env file
    if [ ! -f ".env" ]; then
        log_error ".env file not found in current directory"
        log_info "Make sure you're running this from BookStack root directory"
        log_info "Example: cd /var/www/bookstack && $(basename $0)"
        exit 2
    fi
    
    log_success "Found .env file"
    
    # Load environment variables
    export $(grep -v '^#' .env | grep -v '^$' | xargs) 2>/dev/null || true
    
    # Validate database credentials
    if [ -z "${DB_HOST}" ] || [ -z "${DB_DATABASE}" ] || [ -z "${DB_USERNAME}" ]; then
        log_error "Missing database credentials in .env"
        log_info "Required variables: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
        exit 2
    fi
    
    log_success "Database credentials loaded"
    log_info "  Host: ${DB_HOST}"
    log_info "  Database: ${DB_DATABASE}"
    log_info "  User: ${DB_USERNAME}"
    
    # Test database connection
    log_info "Testing database connection..."
    if mysql -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" -e "USE ${DB_DATABASE}" 2>/dev/null; then
        log_success "Database connection successful"
    else
        log_error "Cannot connect to database"
        log_info "Check your credentials in .env file"
        exit 2
    fi
}

################################################################################
# Tool Selection
################################################################################

select_export_tool() {
    log_step "Selecting Best Export Tool"
    
    log_info "Evaluating available tools..."
    echo ""
    
    # Check Perl (our favorite)
    if command -v perl &> /dev/null && \
       perl -e 'use DBI; use DBD::mysql;' 2>/dev/null; then
        log_success "✨ Perl with DBI/DBD::mysql is available (BEST OPTION)"
        if [ -f "/workspaces/BookStack/bookstack-migration/tools/perl/export-dokuwiki-perly.pl" ]; then
            SELECTED_TOOL="perl"
            TOOL_PATH="/workspaces/BookStack/bookstack-migration/tools/perl/export-dokuwiki-perly.pl"
            log_info "   Using: $TOOL_PATH"
            return 0
        elif [ -f "dev/migration/export-dokuwiki-perly.pl" ]; then
            SELECTED_TOOL="perl"
            TOOL_PATH="dev/migration/export-dokuwiki-perly.pl"
            log_info "   Using: $TOOL_PATH"
            return 0
        else
            log_warn "   Perl is available but export script not found"
        fi
    else
        log_warn "⚠️  Perl not available or missing DBI/DBD::mysql modules"
        log_info "   Install with: cpan DBI DBD::mysql"
    fi
    
    # Check Java (slower but reliable)
    if command -v java &> /dev/null; then
        log_success "☕ Java is available (slower but reliable)"
        if [ -f "/workspaces/BookStack/bookstack-migration/tools/java/bookstack2dokuwiki.jar" ]; then
            SELECTED_TOOL="java"
            TOOL_PATH="/workspaces/BookStack/bookstack-migration/tools/java/bookstack2dokuwiki.jar"
            log_info "   Using: $TOOL_PATH"
            return 0
        elif [ -f "dev/tools/bookstack2dokuwiki.jar" ]; then
            SELECTED_TOOL="java"
            TOOL_PATH="dev/tools/bookstack2dokuwiki.jar"
            log_info "   Using: $TOOL_PATH"
            return 0
        else
            log_warn "   Java is available but JAR not found"
        fi
    else
        log_warn "⚠️  Java not available"
    fi
    
    # Check C binary
    if [ -x "/workspaces/BookStack/bookstack-migration/tools/c/bookstack2dokuwiki" ]; then
        log_success "⚡ C binary is available (FAST)"
        SELECTED_TOOL="c"
        TOOL_PATH="/workspaces/BookStack/bookstack-migration/tools/c/bookstack2dokuwiki"
        log_info "   Using: $TOOL_PATH"
        return 0
    elif [ -x "dev/tools/bookstack2dokuwiki" ]; then
        log_success "⚡ C binary is available (FAST)"
        SELECTED_TOOL="c"
        TOOL_PATH="dev/tools/bookstack2dokuwiki"
        log_info "   Using: $TOOL_PATH"
        return 0
    else
        log_warn "⚠️  C binary not available"
    fi
    
    # Check PHP artisan command (last resort)
    if command -v php &> /dev/null && [ -f "artisan" ]; then
        log_warn "🐘 PHP artisan is available (last resort)"
        log_info "   This may fail if the export command is not implemented"
        SELECTED_TOOL="php"
        TOOL_PATH="artisan"
        return 0
    else
        log_warn "⚠️  PHP artisan not available"
    fi
    
    # No suitable tool found
    log_error "No suitable export tool found!"
    log_info ""
    log_info "Please install one of the following:"
    log_info "  1. Run 01-setup.sh to install Perl with DBI/DBD::mysql"
    log_info "  2. Install Java and build the JAR"
    log_info "  3. Compile the C binary"
    log_info "  4. Ensure PHP and artisan are available"
    exit 3
}

################################################################################
# Export Execution
################################################################################

run_export() {
    log_step "Exporting BookStack Data"
    
    log_info "Selected tool: ${SELECTED_TOOL}"
    log_info "Export directory: ${EXPORT_DIR}"
    
    # Create export directory
    mkdir -p "${EXPORT_DIR}"
    
    # Run appropriate tool
    case "${SELECTED_TOOL}" in
        perl)
            log_info "🐪 Running Perl export..."
            echo ""
            if perl "${TOOL_PATH}" \
                -h "${DB_HOST:-localhost}" \
                -d "${DB_DATABASE}" \
                -u "${DB_USERNAME}" \
                -P "${DB_PASSWORD}" \
                -o "${EXPORT_DIR}" \
                -vv; then
                log_success "Perl export completed successfully"
            else
                log_error "Perl export failed with exit code $?"
                exit 1
            fi
            ;;
        
        java)
            log_info "☕ Running Java export (this may take a while)..."
            echo ""
            if java -jar "${TOOL_PATH}" \
                --db-host "${DB_HOST:-localhost}" \
                --db-name "${DB_DATABASE}" \
                --db-user "${DB_USERNAME}" \
                --db-pass "${DB_PASSWORD}" \
                --output "${EXPORT_DIR}" \
                --verbose; then
                log_success "Java export completed successfully"
            else
                log_error "Java export failed with exit code $?"
                exit 1
            fi
            ;;
        
        c)
            log_info "⚡ Running C binary export..."
            echo ""
            if "${TOOL_PATH}" \
                --db-host "${DB_HOST:-localhost}" \
                --db-name "${DB_DATABASE}" \
                --db-user "${DB_USERNAME}" \
                --db-pass "${DB_PASSWORD}" \
                --output "${EXPORT_DIR}" \
                --verbose; then
                log_success "C binary export completed successfully"
            else
                log_error "C binary export failed with exit code $?"
                exit 1
            fi
            ;;
        
        php)
            log_info "🐘 Running PHP artisan export..."
            log_warn "This may fail if the export command is not implemented"
            echo ""
            if php artisan bookstack:export-dokuwiki \
                --output-path="${EXPORT_DIR}"; then
                log_success "PHP export completed successfully"
            else
                log_error "PHP export failed with exit code $?"
                log_info "The artisan command may not be implemented yet"
                exit 1
            fi
            ;;
    esac
}

################################################################################
# Export Statistics
################################################################################

calculate_statistics() {
    log_step "Export Statistics"
    
    # Count exported files
    if [ -d "${EXPORT_DIR}" ]; then
        EXPORT_FILES=$(find "${EXPORT_DIR}" -type f | wc -l)
        EXPORT_SIZE=$(du -sh "${EXPORT_DIR}" 2>/dev/null | cut -f1)
        
        log_info "Files exported: ${EXPORT_FILES}"
        log_info "Total size: ${EXPORT_SIZE}"
        
        # Calculate time taken
        EXPORT_END_TIME=$(date +%s)
        EXPORT_DURATION=$((EXPORT_END_TIME - EXPORT_START_TIME))
        log_info "Time taken: ${EXPORT_DURATION} seconds"
        
        # Show some sample files
        echo ""
        log_info "Sample exported files:"
        find "${EXPORT_DIR}" -type f | head -5 | while read file; do
            echo "  - $(basename $file)"
        done
        
        if [ ${EXPORT_FILES} -gt 5 ]; then
            echo "  ... and $((EXPORT_FILES - 5)) more files"
        fi
    else
        log_warn "Export directory not found: ${EXPORT_DIR}"
        exit 1
    fi
}

################################################################################
# Summary
################################################################################

show_summary() {
    echo ""
    echo -e "${GREEN}${BOLD}"
    cat << 'EOF'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   ✅ EXPORT COMPLETED SUCCESSFULLY                               ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    
    log_success "BookStack data has been exported to DokuWiki format"
    log_info "Export directory: ${EXPORT_DIR}"
    log_info "Total files: ${EXPORT_FILES}"
    log_info "Total size: ${EXPORT_SIZE}"
    log_info "Tool used: ${SELECTED_TOOL}"
    echo ""
    log_info "Next step: Run 04-validate.sh to validate the export"
}

################################################################################
# Main Execution
################################################################################

main() {
    show_banner
    validate_configuration
    select_export_tool
    run_export
    calculate_statistics
    show_summary
}

# Run main function
main

exit 0
