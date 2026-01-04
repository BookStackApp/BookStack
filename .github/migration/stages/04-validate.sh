#!/bin/bash
################################################################################
#
# 04-validate.sh - Validate DokuWiki Export
#
# This script validates that the BookStack export completed successfully
# and that the exported data is in valid DokuWiki format.
#
# Validation checks:
#   1. Export directory exists and is not empty
#   2. Minimum file count check (at least some content exported)
#   3. DokuWiki format validation (files have .txt extension, proper structure)
#   4. Metadata files exist (if applicable)
#   5. No corrupt or empty files
#   6. File size sanity checks
#
# Prerequisites:
#   - Run 03-export.sh first
#
# Usage: ./04-validate.sh [export_directory]
#
# Exit codes:
#   0 = Validation passed
#   1 = Validation failed
#   2 = Export directory not found
#   3 = Critical validation errors
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

# Validation stats
TOTAL_FILES=0
VALID_FILES=0
EMPTY_FILES=0
CORRUPT_FILES=0
DOKUWIKI_FILES=0
WARNINGS=0
ERRORS=0

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
    ((WARNINGS++))
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
    ((ERRORS++))
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
║   🔍 STAGE 4: VALIDATE DOKUWIKI EXPORT                           ║
║                                                                   ║
║   This script validates your exported DokuWiki data to ensure    ║
║   everything is ready for import.                                ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
}

################################################################################
# Directory Validation
################################################################################

validate_export_directory() {
    log_step "Validating Export Directory"
    
    # Check if directory exists
    if [ ! -d "${EXPORT_DIR}" ]; then
        log_error "Export directory not found: ${EXPORT_DIR}"
        log_info "Did you run 03-export.sh first?"
        exit 2
    fi
    
    log_success "Export directory exists: ${EXPORT_DIR}"
    
    # Check if directory is not empty
    TOTAL_FILES=$(find "${EXPORT_DIR}" -type f 2>/dev/null | wc -l)
    
    if [ ${TOTAL_FILES} -eq 0 ]; then
        log_error "Export directory is empty!"
        log_info "The export may have failed. Check 03-export.sh output."
        exit 2
    fi
    
    log_success "Found ${TOTAL_FILES} files in export directory"
    
    # Check directory size
    local dir_size=$(du -sh "${EXPORT_DIR}" 2>/dev/null | cut -f1)
    log_info "Export size: ${dir_size}"
    
    # Minimum size check (should be at least a few KB)
    local size_kb=$(du -sk "${EXPORT_DIR}" | cut -f1)
    if [ ${size_kb} -lt 10 ]; then
        log_error "Export directory is suspiciously small (< 10KB)"
        log_warn "This suggests the export may have failed"
        ((ERRORS++))
    else
        log_success "Export size looks reasonable"
    fi
}

################################################################################
# DokuWiki Format Validation
################################################################################

validate_dokuwiki_format() {
    log_step "Validating DokuWiki Format"
    
    log_info "Checking for DokuWiki text files (.txt)..."
    
    # Count .txt files (DokuWiki pages)
    DOKUWIKI_FILES=$(find "${EXPORT_DIR}" -name "*.txt" -type f 2>/dev/null | wc -l)
    
    if [ ${DOKUWIKI_FILES} -eq 0 ]; then
        log_error "No DokuWiki .txt files found!"
        log_info "Expected at least some .txt files for wiki pages"
        log_warn "The export may not be in DokuWiki format"
    else
        log_success "Found ${DOKUWIKI_FILES} DokuWiki text files"
    fi
    
    # Check for data/pages directory structure (standard DokuWiki)
    if [ -d "${EXPORT_DIR}/data/pages" ]; then
        log_success "DokuWiki directory structure detected (data/pages/)"
        local pages_count=$(find "${EXPORT_DIR}/data/pages" -name "*.txt" 2>/dev/null | wc -l)
        log_info "  Pages in data/pages/: ${pages_count}"
    elif [ -d "${EXPORT_DIR}/pages" ]; then
        log_success "Pages directory found"
        local pages_count=$(find "${EXPORT_DIR}/pages" -name "*.txt" 2>/dev/null | wc -l)
        log_info "  Pages: ${pages_count}"
    else
        log_warn "Standard DokuWiki directory structure not detected"
        log_info "Files may need to be reorganized for DokuWiki import"
    fi
    
    # Check for media/uploads
    if [ -d "${EXPORT_DIR}/data/media" ] || [ -d "${EXPORT_DIR}/media" ]; then
        local media_dir="${EXPORT_DIR}/data/media"
        [ ! -d "$media_dir" ] && media_dir="${EXPORT_DIR}/media"
        local media_count=$(find "$media_dir" -type f 2>/dev/null | wc -l)
        log_success "Media directory found with ${media_count} files"
    else
        log_warn "No media/uploads directory found"
        log_info "If your BookStack had images, they may be missing"
    fi
}

################################################################################
# File Integrity Validation
################################################################################

validate_file_integrity() {
    log_step "Validating File Integrity"
    
    log_info "Checking for empty or corrupt files..."
    
    # Find all files
    local all_files=$(find "${EXPORT_DIR}" -type f)
    
    # Check each file
    while IFS= read -r file; do
        ((VALID_FILES++))
        
        # Check if file is empty
        if [ ! -s "$file" ]; then
            log_warn "Empty file: $(basename $file)"
            ((EMPTY_FILES++))
            continue
        fi
        
        # For text files, check if they contain valid UTF-8
        if [[ "$file" == *.txt ]]; then
            if ! iconv -f UTF-8 -t UTF-8 "$file" > /dev/null 2>&1; then
                log_warn "Potentially corrupt file (invalid UTF-8): $(basename $file)"
                ((CORRUPT_FILES++))
            fi
        fi
    done <<< "$all_files"
    
    if [ ${EMPTY_FILES} -eq 0 ]; then
        log_success "No empty files found"
    else
        log_warn "Found ${EMPTY_FILES} empty files"
    fi
    
    if [ ${CORRUPT_FILES} -eq 0 ]; then
        log_success "No corrupt files detected"
    else
        log_error "Found ${CORRUPT_FILES} potentially corrupt files"
    fi
}

################################################################################
# Content Validation
################################################################################

validate_content() {
    log_step "Validating Content"
    
    # Sample a few files to check content
    log_info "Sampling exported files for content validation..."
    
    local sample_files=$(find "${EXPORT_DIR}" -name "*.txt" -type f | head -5)
    local sample_count=0
    local valid_content=0
    
    while IFS= read -r file; do
        [ -z "$file" ] && continue
        ((sample_count++))
        
        # Check if file has some content (at least 10 characters)
        local file_size=$(wc -c < "$file" 2>/dev/null || echo 0)
        if [ ${file_size} -gt 10 ]; then
            ((valid_content++))
            
            # Show first line of file (if it looks like a header)
            local first_line=$(head -n1 "$file" 2>/dev/null)
            if [ -n "$first_line" ]; then
                log_info "✓ $(basename $file) - ${file_size} bytes"
                # Check for DokuWiki syntax markers
                if grep -q "====" "$file" 2>/dev/null || grep -q "**" "$file" 2>/dev/null; then
                    log_info "  Contains DokuWiki formatting"
                fi
            fi
        else
            log_warn "File too small: $(basename $file) - ${file_size} bytes"
        fi
    done <<< "$sample_files"
    
    if [ ${sample_count} -gt 0 ]; then
        log_info "Validated ${valid_content}/${sample_count} sample files"
        
        if [ ${valid_content} -eq ${sample_count} ]; then
            log_success "All sampled files contain valid content"
        else
            log_warn "Some sampled files may be incomplete"
        fi
    fi
}

################################################################################
# Metadata Validation
################################################################################

validate_metadata() {
    log_step "Validating Metadata"
    
    # Check for export manifest or metadata file
    if [ -f "${EXPORT_DIR}/export_manifest.txt" ] || \
       [ -f "${EXPORT_DIR}/export_info.txt" ] || \
       [ -f "${EXPORT_DIR}/EXPORT_INFO.txt" ]; then
        log_success "Export metadata file found"
        
        # Show metadata content
        for metafile in "${EXPORT_DIR}/export_manifest.txt" \
                        "${EXPORT_DIR}/export_info.txt" \
                        "${EXPORT_DIR}/EXPORT_INFO.txt"; do
            if [ -f "$metafile" ]; then
                log_info "Metadata from $(basename $metafile):"
                head -n 5 "$metafile" | sed 's/^/  /'
                break
            fi
        done
    else
        log_warn "No export metadata file found"
        log_info "This is optional but helpful for tracking"
    fi
    
    # Check for checksums file
    if [ -f "${EXPORT_DIR}/export_checksums.txt" ] || \
       [ -f "${EXPORT_DIR}/checksums.md5" ]; then
        log_success "Checksum file found"
        log_info "You can verify file integrity with: md5sum -c checksums.md5"
    else
        log_warn "No checksum file found"
        log_info "Cannot verify file integrity"
    fi
}

################################################################################
# Summary Report
################################################################################

show_validation_summary() {
    echo ""
    
    if [ ${ERRORS} -eq 0 ] && [ ${WARNINGS} -lt 3 ]; then
        echo -e "${GREEN}${BOLD}"
        cat << 'EOF'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   ✅ VALIDATION PASSED                                           ║
║                                                                   ║
║   Your export looks good and is ready for import!                ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
        echo -e "${NC}"
        
        log_success "Export validation completed successfully"
    elif [ ${ERRORS} -eq 0 ]; then
        echo -e "${YELLOW}${BOLD}"
        cat << 'EOF'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   ⚠️  VALIDATION PASSED WITH WARNINGS                            ║
║                                                                   ║
║   Export looks mostly good but has some warnings.                ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
        echo -e "${NC}"
        
        log_warn "Export has ${WARNINGS} warnings but no critical errors"
    else
        echo -e "${RED}${BOLD}"
        cat << 'EOF'
╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║   ❌ VALIDATION FAILED                                           ║
║                                                                   ║
║   Export has critical errors that need to be fixed.              ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝
EOF
        echo -e "${NC}"
        
        log_error "Export has ${ERRORS} critical errors"
    fi
    
    echo ""
    log_info "═══════════════════════════════════════════════════════════════"
    log_info "VALIDATION STATISTICS"
    log_info "═══════════════════════════════════════════════════════════════"
    log_info "Total files: ${TOTAL_FILES}"
    log_info "DokuWiki text files: ${DOKUWIKI_FILES}"
    log_info "Empty files: ${EMPTY_FILES}"
    log_info "Corrupt files: ${CORRUPT_FILES}"
    log_info "Warnings: ${WARNINGS}"
    log_info "Errors: ${ERRORS}"
    log_info "═══════════════════════════════════════════════════════════════"
    echo ""
    
    if [ ${ERRORS} -eq 0 ]; then
        log_info "Next steps:"
        log_info "  1. Review the exported files in: ${EXPORT_DIR}"
        log_info "  2. Import into DokuWiki"
        log_info "  3. Verify content in DokuWiki interface"
        echo ""
        log_success "Export is ready for import!"
        return 0
    else
        log_info "Recommended actions:"
        log_info "  1. Review error messages above"
        log_info "  2. Re-run 03-export.sh if needed"
        log_info "  3. Check BookStack database connectivity"
        log_info "  4. Verify export tool is working correctly"
        echo ""
        log_error "Please fix errors before proceeding with import"
        return 1
    fi
}

################################################################################
# Main Execution
################################################################################

main() {
    show_banner
    validate_export_directory
    validate_dokuwiki_format
    validate_file_integrity
    validate_content
    validate_metadata
    
    if show_validation_summary; then
        exit 0
    else
        exit 1
    fi
}

# Run main function
main
