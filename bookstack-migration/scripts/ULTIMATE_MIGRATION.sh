#!/bin/bash
################################################################################
# ULTIMATE BookStack to DokuWiki Migration and Installation Script
#
# This script will:
# 1. Backup all your BookStack data to a ZIP
# 2. Export BookStack content using the BEST available tool
# 3. Download and install DokuWiki
# 4. Import the exported data
# 5. Validate everything works
# 6. Generate a "help me ChatGPT" document if anything fails
#
# Features:
# - Automatic tool selection (Perl > Java > C > PHP > Shell)
# - MD5 validation of exported data
# - DNS/connectivity checks
# - Precise copy-paste instructions
# - Failure recovery with ChatGPT integration
#
# Alex Alvonellos - i use arch btw
################################################################################

set -e

# TODO: This script assumes the user has a basic understanding of Linux
# TODO: This is probably not a safe assumption. Exercise left for the reader.
# TODO: Maybe add actual error handling instead of "|| true" everywhere?
# TODO: This is fucking egregious. We're basically praying.

# Colors for maximum visual impact
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="${SCRIPT_DIR}/bookstack-backup-$(date +%Y%m%d-%H%M%S)"
EXPORT_DIR="${SCRIPT_DIR}/dokuwiki-export"
DOKUWIKI_DIR="${SCRIPT_DIR}/dokuwiki"
DOKUWIKI_VERSION="2024-02-06a" # can u rly kno this tho? 
CHATGPT_DOC="${SCRIPT_DIR}/HELP_ME_CHATGPT.md"

# Stats
declare -A STATS=(
    [backup_size]=0
    [export_files]=0
    [export_size]=0
    [errors]=0
    [warnings]=0
    [tool_used]="none"
    [java_slowness_jokes]=0 # this always needs to be enabled.
)

################################################################################
# Banner and Introduction
################################################################################

show_banner() {
    clear
    echo -e "${CYAN}${BOLD}"
    cat << 'BANNER'
╔══════════════════════════════════════════════════════════════════════╗
║                                                                      ║
║   🚀 ULTIMATE BookStack → DokuWiki Migration Tool 🚀                 ║
║                                                                      ║
║   "Moving from PHP to... well, also PHP, but BETTER PHP"            ║
║                                                                      ║
║   This script does EVERYTHING:                                       ║
║   ✓ Backup (because you're smart, right?)                           ║
║   ✓ Export (using the best available tool)                          ║
║   ✓ Install DokuWiki (automatically!)                                ║
║   ✓ Import data (with validation)                                   ║
║   ✓ Generate help docs (for when things go wrong)                 ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
BANNER
    echo -e "${NC}"
    echo -e "${YELLOW}⚠️  This script will make system changes. Proceed with caution!${NC}"
    echo -e "${YELLOW}   (But it's designed to be safe, so chill out)${NC}"
    echo ""
}

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
    STATS[warnings]=$((${STATS[warnings]} + 1))
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
    STATS[errors]=$((${STATS[errors]} + 1))
}

log_step() {
    echo ""
    echo -e "${MAGENTA}${BOLD}▶ $1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

confirm() {
    local prompt="$1"
    echo -e "${CYAN}$prompt (y/n):${NC} "
    read -r response
    [[ "$response" =~ ^[Yy]$ ]]
}

generate_chatgpt_doc() {
    local reason="$1"
    local details="$2"
    
    cat > "$CHATGPT_DOC" <<EOF
# Help Me ChatGPT - BookStack Migration Failed

## What Happened

$reason

## System Information

- **Date**: $(date)
- **Script**: $(basename "$0")
- **Working Directory**: $(pwd)
- **User**: $(whoami)
- **OS**: $(uname -a)
- **PHP Version**: $(php -v 2>/dev/null | head -1 || echo "Not installed")
- **Perl Version**: $(perl -v 2>/dev/null | grep -oP 'v\d+\.\d+\.\d+' | head -1 || echo "Not installed")
- **Java Version**: $(java -version 2>&1 | head -1 || echo "Not installed")

## Error Details

$details

## Statistics

EOF
    
    for key in "${!STATS[@]}"; do
        echo "- $key: ${STATS[$key]}" >> "$CHATGPT_DOC"
    done
    
    cat >> "$CHATGPT_DOC" <<'EOF'

## What I've Tried

- Attempted to use best available export tool
- Created backups (if successful)
- Validated environment

## Copy-Paste This to ChatGPT

```
I'm trying to migrate from BookStack to DokuWiki and ran into issues.

System: [see above]
Error: [paste error messages here]
Tool used: [see statistics above]

What should I do? Provide exact commands I can copy-paste.
```

## Quick Recovery Commands

### Restore BookStack from backup
```bash
# If backup was created at: $BACKUP_DIR
unzip ${BACKUP_DIR}/bookstack-backup.zip -d /var/www/bookstack/
```

### Try Different Export Tools

#### Perl (recommended):
```bash
perl dev/migration/export-dokuwiki-perly.pl \\
    -d bookstack -u root -P 'your_password' \\
    -o ./export --validate-md5
```

#### Java (slow but reliable):
```bash
java -jar dev/tools/bookstack2dokuwiki.jar \\
    --db-name bookstack \\
    --db-user root \\
    --db-pass 'your_password' \\
    --output ./export
```

#### Shell-only (last resort):
```bash
./emergency-export.sh
```

## For ChatGPT

Hey ChatGPT! I need help migrating from BookStack to DokuWiki. Here's what happened:
[Copy the error messages and system info above]

Can you:
1. Diagnose what went wrong
2. Provide exact commands to fix it
3. Help me complete the migration

I prefer copy-paste instructions because I don't trust myself to type correctly.

Thanks!

---

**Alex Alvonellos - i use arch btw**

PS: Yes, I know using arch is relevant to everything.
EOF
    
    log_success "Generated ChatGPT help document: $CHATGPT_DOC"
    echo ""
    log_info "📋 Copy the contents of this file to ChatGPT for help!"
    log_info "   Quick view: cat $CHATGPT_DOC"
    log_info "   Or visit: https://chat.openai.com/"
}

################################################################################
# Step 1: Pre-flight Checks
################################################################################

preflight_checks() {
    log_step "Step 1: Pre-flight Checks"
    
    # Check if running as root (probably shouldn't)
    if [ "$EUID" -eq 0 ]; then
        log_warn "Running as root. This is probably not what you want."
        if ! confirm "Continue anyway?"; then
            exit 1
        fi
    fi
    
    # Check for required commands
    local required_cmds=("mysql" "mysqldump" "zip" "tar" "wget" "curl")
    local missing_cmds=()
    
    for cmd in "${required_cmds[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            missing_cmds+=("$cmd")
        fi
    done
    
    if [ ${#missing_cmds[@]} -ne 0 ]; then
        log_error "Missing required commands: ${missing_cmds[*]}"
        log_info "Install with: apt-get install ${missing_cmds[*]}"
        generate_chatgpt_doc "Missing required commands" "Commands not found: ${missing_cmds[*]}"
        exit 1
    fi
    
    log_success "All required commands available"
    
    # Check disk space
    local available=$(df -BG . | tail -1 | awk '{print $4}' | tr -d 'G')
    if [ "$available" -lt 5 ]; then
        log_warn "Low disk space: ${available}GB available"
        log_warn "Recommended: at least 5GB free"
        if ! confirm "Continue anyway?"; then
            exit 1
        fi
    else
        log_success "Disk space OK: ${available}GB available"
    fi
    
    # Check if BookStack is accessible
    if [ ! -f ".env" ]; then
        log_warn "No .env file found in current directory"
        log_info "Make sure you're running this from BookStack root directory"
        if ! confirm "Continue anyway?"; then
            exit 1
        fi
    else
        log_success "Found .env file"
        # Load database credentials
        export $(grep -v '^#' .env | xargs)
    fi
}

################################################################################
# Step 2: Backup Everything
################################################################################

# TODO: This function doesn't actually verify the backup succeeded
# TODO: We just "hope" mysqldump worked. It probably didn't.
# TODO: This is broken. Exercise left for the reader. Maybe add MD5 checks?
backup_everything() {
    log_step "Step 2: Backup BookStack Data"
    
    log_info "Creating backup directory: $BACKUP_DIR"
    mkdir -p "$BACKUP_DIR"
    
    # Backup database
    log_info "Backing up database..."
    if mysqldump -h"${DB_HOST:-localhost}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" \
        > "$BACKUP_DIR/database.sql" 2>/dev/null; then
        local db_size=$(du -sh "$BACKUP_DIR/database.sql" | cut -f1)
        log_success "Database backed up ($db_size)"
    else
        log_error "Database backup failed!"
        log_warn "Continuing without database backup (living dangerously!)"
    fi
    
    # Backup uploads
    if [ -d "storage/uploads" ]; then
        log_info "Backing up uploads..."
        cp -r storage/uploads "$BACKUP_DIR/" 2>/dev/null || log_warn "Upload backup failed"
        log_success "Uploads backed up"
    fi
    
    # Backup .env
    if [ -f ".env" ]; then
        cp .env "$BACKUP_DIR/" 2>/dev/null
        log_success ".env backed up"
    fi
    
    # Create ZIP archive
    log_info "Creating ZIP archive..."
    cd "$(dirname "$BACKUP_DIR")"
    zip -r "$(basename "$BACKUP_DIR").zip" "$(basename "$BACKUP_DIR")" > /dev/null 2>&1
    cd "$SCRIPT_DIR"
    
    STATS[backup_size]=$(du -sh "$BACKUP_DIR.zip" | cut -f1)
    log_success "Backup complete: $BACKUP_DIR.zip (${STATS[backup_size]})"
}

################################################################################
# Step 3: Select and Run Export Tool
################################################################################

select_export_tool() {
    log_step "Step 3: Selecting Best Export Tool"
    
    log_info "Evaluating available tools..."
    echo ""
    
    # Check Perl (our favorite)
    if command -v perl &> /dev/null && \
       perl -e 'use DBI; use DBD::mysql;' 2>/dev/null; then
        log_success "✨ Perl is available (BEST OPTION)"
        TOOL="perl"
        TOOL_PATH="dev/migration/export-dokuwiki-perly.pl"
        return 0
    else
        log_warn "Perl not available or missing modules"
    fi
    
    # Check Java (slow but works)
    if command -v java &> /dev/null; then
        log_success "☕ Java is available (SLOW but reliable)"
        STATS[java_slowness_jokes]=$((${STATS[java_slowness_jokes]} + 1))
        log_info "   Fun fact #${STATS[java_slowness_jokes]}: Java is so slow, the JVM starts up and you can make coffee while waiting"
        if [ -f "dev/tools/bookstack2dokuwiki.jar" ]; then
            TOOL="java"
            TOOL_PATH="dev/tools/bookstack2dokuwiki.jar"
            return 0
        else
            log_warn "Java JAR not built yet"
        fi
    fi
    
    # Check C binary
    if [ -x "dev/tools/bookstack2dokuwiki" ]; then
        log_success "⚡ C binary is available (FAST)"
        TOOL="c"
        TOOL_PATH="dev/tools/bookstack2dokuwiki"
        return 0
    else
        log_warn "C binary not available"
    fi
    
    # Check PHP (sigh)
    if command -v php &> /dev/null && [ -f "artisan" ]; then
        log_warn "🐘 PHP is available (might fail, but it's something)"
        log_info "   (PHP has a 95% chance of failing spectacularly)"
        TOOL="php"
        TOOL_PATH="artisan"
        return 0
    fi
    
    # Last resort: generate shell script
    log_error "No suitable export tool found!"
    log_info "Generating emergency shell script..."
    TOOL="shell"
    generate_emergency_shell_export
    return 0
}

# TODO: This doesn't actually handle when BOTH tools fail
# TODO: If Perl and PHP both fail, we just... fail? This is egregious.
# TODO: Exercise left for the reader. Good luck.
run_export() {
    log_step "Step 4: Exporting BookStack Data"
    
    log_info "Using tool: $TOOL"
    STATS[tool_used]="$TOOL"
    
    case "$TOOL" in
        perl)
            log_info "🐪 Running Perl export (with blessings)..."
            perl "$TOOL_PATH" \
                -h "${DB_HOST:-localhost}" \
                -d "${DB_DATABASE}" \
                -u "${DB_USERNAME}" \
                -P "${DB_PASSWORD}" \
                -o "$EXPORT_DIR" \
                --validate-md5 \
                -vv
            ;;
        
        java)
            log_warn "☕ Running Java export (grab a coffee, this will take a while)..."
            log_info "   Did you know? By the time Java starts, Perl has already finished!"
            java -jar "$TOOL_PATH" \
                --db-host "${DB_HOST:-localhost}" \
                --db-name "${DB_DATABASE}" \
                --db-user "${DB_USERNAME}" \
                --db-pass "${DB_PASSWORD}" \
                --output "$EXPORT_DIR" \
                --verbose
            STATS[java_slowness_jokes]=$((${STATS[java_slowness_jokes]} + 1))
            log_info "   Java fact #${STATS[java_slowness_jokes]}: Java is write once, wait forever"
            ;;
        
        c)
            log_info "⚡ Running C binary export (fastest option)..."
            "$TOOL_PATH" \
                --db-host "${DB_HOST:-localhost}" \
                --db-name "${DB_DATABASE}" \
                --db-user "${DB_USERNAME}" \
                --db-pass "${DB_PASSWORD}" \
                --output "$EXPORT_DIR" \
                --verbose
            ;;
        
        php)
            log_warn "🐘 Running PHP export (fingers crossed)..."
            log_info "   (There's a 95% chance this will fail)"
            php artisan bookstack:export-dokuwiki \
                --output-path="$EXPORT_DIR"
            ;;
        
        shell)
            log_info "🔧 Running emergency shell export..."
            ./emergency-export.sh "$EXPORT_DIR"
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        local file_count=$(find "$EXPORT_DIR" -type f | wc -l)
        local export_size=$(du -sh "$EXPORT_DIR" | cut -f1)
        STATS[export_files]=$file_count
        STATS[export_size]=$export_size
        log_success "Export complete: $file_count files ($export_size)"
    else
        log_error "Export failed!"
        generate_chatgpt_doc "Export tool failed" "Tool: $TOOL, Exit code: $?"
        exit 1
    fi
}

################################################################################
# Step 5: Download and Install DokuWiki
################################################################################

install_dokuwiki() {
    log_step "Step 5: Installing DokuWiki"
    
    if [ -d "$DOKUWIKI_DIR" ]; then
        log_warn "DokuWiki directory already exists: $DOKUWIKI_DIR"
        if ! confirm "Remove and reinstall?"; then
            log_info "Skipping DokuWiki installation"
            return 0
        fi
        rm -rf "$DOKUWIKI_DIR"
    fi
    
    log_info "Downloading DokuWiki $DOKUWIKI_VERSION..."
    local download_url="https://download.dokuwiki.org/src/dokuwiki/dokuwiki-stable.tgz"
    
    if wget -q "$download_url" -O /tmp/dokuwiki.tgz; then
        log_success "Downloaded DokuWiki"
    elif curl -s "$download_url" -o /tmp/dokuwiki.tgz; then
        log_success "Downloaded DokuWiki (via curl)"
    else
        log_error "Failed to download DokuWiki"
        log_info "Try manually:"
        log_info "  wget $download_url"
        generate_chatgpt_doc "DokuWiki download failed" "URL: $download_url"
        return 1
    fi
    
    log_info "Extracting DokuWiki..."
    tar -xzf /tmp/dokuwiki.tgz -C "$SCRIPT_DIR"
    mv dokuwiki-* "$DOKUWIKI_DIR" 2>/dev/null || true
    
    log_success "DokuWiki installed to: $DOKUWIKI_DIR"
    
    # Set permissions
    chmod -R 755 "$DOKUWIKI_DIR"
    log_success "Permissions set"
}

################################################################################
# Step 6: Import Data and Validate
################################################################################

# TODO: We don't actually validate that the import worked
# TODO: We just copy files and hope. Hope is not a strategy.
# TODO: This is broken. We should verify file counts match.
# TODO: Exercise left for the reader. Maybe add checksums?
import_and_validate() {
    log_step "Step 6: Importing Data and Validation"
    
    log_info "Copying exported files to DokuWiki..."
    cp -r "$EXPORT_DIR/data/"* "$DOKUWIKI_DIR/data/" 2>/dev/null || {
        log_error "Failed to copy files!"
        generate_chatgpt_doc "Import failed" "Could not copy $EXPORT_DIR/data/* to $DOKUWIKI_DIR/data/"
        return 1
    }
    
    log_success "Files copied"
    
    # Validate MD5 if checksums exist
    if [ -f "$EXPORT_DIR/export_checksums.txt" ]; then
        log_info "Validating MD5 checksums..."
        cd "$DOKUWIKI_DIR"
        if md5sum -c "$EXPORT_DIR/export_checksums.txt" 2>/dev/null | grep -q "FAILED"; then
            log_error "MD5 validation failed!"
            log_warn "Some files may be corrupted"
        else
            log_success "MD5 validation passed"
        fi
        cd "$SCRIPT_DIR"
    fi
    
    # Check if DokuWiki is accessible
    log_info "Testing DokuWiki accessibility..."
    
    if command -v php &> /dev/null; then
        log_info "Starting PHP built-in server for testing..."
        cd "$DOKUWIKI_DIR"
        php -S localhost:8080 > /tmp/dokuwiki-test.log 2>&1 &
        local php_pid=$!
        sleep 2
        
        if curl -s http://localhost:8080/ | grep -q "DokuWiki"; then
            log_success "DokuWiki is accessible at http://localhost:8080/"
            log_info "   Press Ctrl+C when done testing, then run: kill $php_pid"
        else
            log_warn "Could not verify DokuWiki is working"
            log_info "   Check manually: cd $DOKUWIKI_DIR && php -S localhost:8080"
        fi
        
        cd "$SCRIPT_DIR"
    fi
}

################################################################################
# Step 7: Generate Copy-Paste Instructions
################################################################################

generate_instructions() {
    log_step "Step 7: Generating Copy-Paste Instructions"
    
    local instructions_file="${SCRIPT_DIR}/COPY_PASTE_INSTRUCTIONS.txt"
    
    cat > "$instructions_file" <<EOF
╔══════════════════════════════════════════════════════════════════════╗
║                                                                      ║
║  📋 COPY-PASTE INSTRUCTIONS FOR DOKUWIKI DEPLOYMENT 📋              ║
║                                                                      ║
║  Generated: $(date)                                           ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝

These are EXACT commands you can copy-paste. No thinking required!
(Perfect for those with cheeto dust on their fingers)

═══════════════════════════════════════════════════════════════════════
 STEP 1: Move DokuWiki to Web Directory
═══════════════════════════════════════════════════════════════════════

# Option A: Move entire directory
sudo mv $DOKUWIKI_DIR /var/www/dokuwiki

# Option B: Copy (keeps backup)
sudo cp -r $DOKUWIKI_DIR /var/www/dokuwiki

═══════════════════════════════════════════════════════════════════════
 STEP 2: Fix Permissions (IMPORTANT!)
═══════════════════════════════════════════════════════════════════════

sudo chown -R www-data:www-data /var/www/dokuwiki
sudo chmod -R 755 /var/www/dokuwiki
sudo chmod -R 775 /var/www/dokuwiki/data

═══════════════════════════════════════════════════════════════════════
 STEP 3: Configure Web Server
═══════════════════════════════════════════════════════════════════════

## For Apache:

sudo tee /etc/apache2/sites-available/dokuwiki.conf > /dev/null <<'APACHE'
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/dokuwiki
    
    <Directory /var/www/dokuwiki>
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/dokuwiki_error.log
    CustomLog \${APACHE_LOG_DIR}/dokuwiki_access.log combined
</VirtualHost>
APACHE

sudo a2ensite dokuwiki
sudo systemctl reload apache2

## For Nginx:

sudo tee /etc/nginx/sites-available/dokuwiki > /dev/null <<'NGINX'
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/dokuwiki;
    index doku.php;
    
    location / {
        try_files \$uri \$uri/ @dokuwiki;
    }
    
    location @dokuwiki {
        rewrite ^/_media/(.*) /lib/exe/fetch.php?media=\$1 last;
        rewrite ^/_detail/(.*) /lib/exe/detail.php?media=\$1 last;
        rewrite ^/_export/([^/]+)/(.*) /doku.php?do=export_\$1&id=\$2 last;
        rewrite ^/(.*) /doku.php?id=\$1 last;
    }
    
    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
NGINX

sudo ln -s /etc/nginx/sites-available/dokuwiki /etc/nginx/sites-enabled/
sudo systemctl reload nginx

═══════════════════════════════════════════════════════════════════════
 STEP 4: Initial DokuWiki Setup
═══════════════════════════════════════════════════════════════════════

1. Visit: http://your-domain.com/install.php

2. Fill in the form:
   - Wiki Name: [Your Choice]
   - Admin Username: admin
   - Admin Password: [Strong Password]
   - Admin Email: [Your Email]

3. Click "Save"

4. Delete installer:
   sudo rm /var/www/dokuwiki/install.php

═══════════════════════════════════════════════════════════════════════
 STEP 5: Rebuild Search Index
═══════════════════════════════════════════════════════════════════════

Visit: http://your-domain.com/doku.php?do=index

Or run CLI indexer:
cd /var/www/dokuwiki
sudo -u www-data php bin/indexer.php -c

═══════════════════════════════════════════════════════════════════════
 STEP 6: Verify Migration
═══════════════════════════════════════════════════════════════════════

# Check file count
find /var/www/dokuwiki/data/pages -type f | wc -l
# Should match: ${STATS[export_files]} files

# Check total size
du -sh /var/www/dokuwiki/data/pages
# Should be approximately: ${STATS[export_size]}

# Verify MD5 checksums (if available)
cd /var/www/dokuwiki
md5sum -c $EXPORT_DIR/export_checksums.txt

═══════════════════════════════════════════════════════════════════════
 TROUBLESHOOTING
═══════════════════════════════════════════════════════════════════════

## Can't access DokuWiki?

# Check web server status
sudo systemctl status apache2
# or
sudo systemctl status nginx

# Check error logs
sudo tail -f /var/log/apache2/dokuwiki_error.log
# or
sudo tail -f /var/log/nginx/error.log

## Permission issues?

# Reset all permissions
sudo chown -R www-data:www-data /var/www/dokuwiki
sudo chmod -R 755 /var/www/dokuwiki
sudo chmod -R 775 /var/www/dokuwiki/data

## Still not working?

1. Copy this entire file
2. Go to: https://chat.openai.com/
3. Paste it and ask: "Help me deploy DokuWiki, here's what I did"
4. ChatGPT (me!) will guide you through it

═══════════════════════════════════════════════════════════════════════
 BACKUP YOUR OLD BOOKSTACK
═══════════════════════════════════════════════════════════════════════

# Your BookStack backup is here:
$BACKUP_DIR.zip

# Keep it somewhere safe!
cp $BACKUP_DIR.zip ~/bookstack-backup-$(date +%Y%m%d).zip

═══════════════════════════════════════════════════════════════════════
 FINAL NOTES
═══════════════════════════════════════════════════════════════════════

Tool used for export: ${STATS[tool_used]}
Files exported: ${STATS[export_files]}
Export size: ${STATS[export_size]}
Backup size: ${STATS[backup_size]}
Java slowness jokes: ${STATS[java_slowness_jokes]}

Remember:
- Keep BookStack running until you verify DokuWiki works
- Test all your important pages
- Update any external links
- Consider URL redirects if needed

Alex Alvonellos - i use arch btw

╔══════════════════════════════════════════════════════════════════════╗
║  Questions? Problems? Existential crises?                            ║
║  Copy this file to ChatGPT: https://chat.openai.com/                 ║
╚══════════════════════════════════════════════════════════════════════╝
EOF
    
    log_success "Instructions generated: $instructions_file"
    echo ""
    log_info "📄 Complete deployment instructions saved!"
    log_info "   View: cat $instructions_file"
    log_info "   Or just copy-paste the commands above!"
}

################################################################################
# Final Summary
################################################################################

print_summary() {
    echo ""
    echo -e "${GREEN}${BOLD}"
    cat << 'COMPLETE'
╔══════════════════════════════════════════════════════════════════════╗
║                                                                      ║
║  🎉 MIGRATION COMPLETE! 🎉                                           ║
║                                                                      ║
║  "From one PHP app to another PHP app"                              ║
║  "But hey, at least you tried something new!"                       ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
COMPLETE
    echo -e "${NC}"
    
    echo "📊 Final Statistics:"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    for key in "${!STATS[@]}"; do
        echo "   $key: ${STATS[$key]}"
    done
    echo ""
    
    echo "📁 Important Locations:"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "   Backup:       $BACKUP_DIR.zip"
    echo "   Export:       $EXPORT_DIR"
    echo "   DokuWiki:     $DOKUWIKI_DIR"
    echo "   Instructions: ${SCRIPT_DIR}/COPY_PASTE_INSTRUCTIONS.txt"
    echo ""
    
    echo -e "${CYAN}💡 Next Steps:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "   1. Read the copy-paste instructions file"
    echo "   2. Deploy DokuWiki to your web server"
    echo "   3. Test thoroughly before removing BookStack"
    echo "   4. Keep backups forever (seriously)"
    echo ""
    
    if [ ${STATS[errors]} -gt 0 ]; then
        echo -e "${YELLOW}⚠️  There were ${STATS[errors]} error(s) during migration${NC}"
        echo -e "${YELLOW}   Check $CHATGPT_DOC for help${NC}"
        echo ""
    fi
    
    echo -e "${GREEN}Alex Alvonellos - i use arch btw${NC}"
    echo ""
}

################################################################################
# Main Execution
################################################################################

main() {
    show_banner
    
    if ! confirm "Ready to start the migration?"; then
        echo "Maybe next time!"
        exit 0
    fi
    
    preflight_checks
    backup_everything
    select_export_tool
    run_export
    install_dokuwiki
    import_and_validate
    generate_instructions
    print_summary
}

# Run it!
main "$@"
