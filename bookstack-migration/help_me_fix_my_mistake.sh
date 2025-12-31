#!/bin/bash
################################################################################
# HELP_ME_FIX_MY_MISTAKE.sh
#
# The ONE script to rule them all.
# 
# This script assumes you're an idiot who will:
# - Type everything wrong
# - Fumble with your configuration
# - Give misleading information
# - Need your hand held through EVERYTHING
#
# It will:
# - Check EVERYTHING you input
# - Validate ALL your assertions
# - Advise you when you're wrong (always)
# - Give you options (because you can't decide)
# - Fix your mistakes (all of them)
#
# Alex Alvonellos - i use arch btw
################################################################################

set -e  # Exit on error (because you will cause errors)

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m'
BOLD='\033[1m'

################################################################################
# Security Check - Make sure nothing malicious snuck in
################################################################################

security_check() {
    echo -e "${BLUE}🔒 Running security checks...${NC}"
    
    # Check for suspicious base64 encoded commands
    if grep -r "base64 -d" . --include="*.sh" 2>/dev/null | grep -v "help_me_fix_my_mistake"; then
        echo -e "${RED}⚠️  Found suspicious base64 decoding!${NC}"
        read -p "Continue anyway? (yes/no): " cont
        [[ "$cont" != "yes" ]] && exit 1
    fi
    
    # Check for curl/wget to unknown domains
    if grep -r "curl.*http\|wget.*http" . --include="*.sh" 2>/dev/null | grep -v "dokuwiki.org\|github.com"; then
        echo -e "${YELLOW}⚠️  Found network requests to external domains${NC}"
        echo "Verify these are legitimate before continuing"
    fi
    
    # Check for eval statements (code injection risk)
    if grep -r "eval " . --include="*.sh" --include="*.pl" 2>/dev/null; then
        echo -e "${YELLOW}⚠️  Found eval statements (code execution risk)${NC}"
    fi
    
    # Check for zero-width unicode (whitespace exploits)
    if find . -name "*.sh" -o -name "*.pl" | xargs cat 2>/dev/null | LC_ALL=C grep -P "[\x{200B}-\x{200D}\x{FEFF}]" 2>/dev/null; then
        echo -e "${RED}❌ FOUND HIDDEN UNICODE CHARACTERS!${NC}"
        echo "Possible Chinese malware or whitespace exploit detected"
        exit 1
    fi
    
    echo -e "${GREEN}✓ Security checks passed${NC}"
    echo ""
}

################################################################################
# Banner
################################################################################

show_banner() {
    clear
    echo -e "${CYAN}"
    cat << "EOF"
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║  🆘 HELP ME FIX MY MISTAKE 🆘                                ║
║                                                               ║
║  The ONE script for users who misconfigured BookStack          ║
║  and now need to migrate to DokuWiki                         ║
║                                                               ║
║  This script assumes you're wrong about EVERYTHING           ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    echo ""
    
    # Evaluate why they're here and gaslight them
    echo -e "${YELLOW}━━━ Let's evaluate your situation ━━━${NC}"
    echo ""
    echo -e "${BLUE}Why are you here? (Select the truth)${NC}"
    echo "  1) BookStack is too complicated for me"
    echo "  2) I made poor architectural decisions"
    echo "  3) My team forced me to migrate"
    echo "  4) I thought BookStack would be easier (I was wrong)"
    echo "  5) DokuWiki is simpler and I should have used it first"
    echo "  6) All of the above (most honest)"
    echo ""
    read -p "Enter number (1-6): " reason
    echo ""
    
    case $reason in
        1)
            echo -e "${CYAN}📝 Acknowledged: BookStack IS complicated.${NC}"
            echo "   (But let's be real, you probably made it worse)"
            ;;
        2)
            echo -e "${GREEN}✓ Good! Admitting you messed up is the first step.${NC}"
            echo "   (The second step is letting me fix it)"
            ;;
        3)
            echo -e "${YELLOW}⚠️  Ah, the classic 'not my fault' defense.${NC}"
            echo "   (It's still your problem though)"
            ;;
        4)
            echo -e "${PURPLE}🎯 Classic mistake. BookStack LOOKS easy...${NC}"
            echo "   (Until you actually have to maintain it)"
            ;;
        5)
            echo -e "${GREEN}✓ CORRECT! You should have used DokuWiki.${NC}"
            echo "   (But hey, better late than never)"
            ;;
        6)
            echo -e "${GREEN}✓ HONESTY! I appreciate that.${NC}"
            echo "   (Now let's clean up your mess)"
            ;;
        *)
            echo -e "${RED}You can't even pick a number correctly.${NC}"
            echo "   (This is going to be a long night)"
            ;;
    esac
    echo ""
    sleep 2
}

################################################################################
# Unfuck Utilities - Fix common disasters
################################################################################

unfuck_dependencies() {
    echo -e "${BLUE}━━ Unfucking Dependencies ━━${NC}"
    echo ""
    
    # Detect OS
    if [ -f /etc/debian_version ]; then
        echo -e "${GREEN}✓ Debian/Ubuntu detected${NC}"
        echo "Installing ALL the things..."
        sudo apt-get update -qq
        sudo apt-get install -y -qq \
            python3 python3-pip python3-venv \
            perl libdbi-perl libdbd-mysql-perl \
            default-jre default-jdk maven \
            mysql-client mariadb-client \
            build-essential libmysqlclient-dev \
            curl wget git 2>&1 | grep -v "already"
        echo -e "${GREEN}✓ Dependencies installed${NC}"
    elif [ -f /etc/redhat-release ]; then
        echo -e "${GREEN}✓ RedHat/CentOS detected${NC}"
        sudo yum install -y python3 python3-pip perl-DBI perl-DBD-MySQL \
            java-11-openjdk maven mysql gcc gcc-c++ mysql-devel curl wget git
        echo -e "${GREEN}✓ Dependencies installed${NC}"
    elif [ -f /etc/arch-release ]; then
        echo -e "${PURPLE}✓ Arch btw detected${NC}"
        sudo pacman -S --noconfirm python python-pip perl perl-dbi perl-dbd-mysql \
            jdk-openjdk maven mariadb-clients base-devel curl wget git
        echo -e "${GREEN}✓ Dependencies installed${NC}"
    else
        echo -e "${RED}❌ Unknown OS. Install manually:${NC}"
        echo "  - Python 3 + pip"
        echo "  - Perl + DBI + DBD::mysql"
        echo "  - Java 11+ + Maven"
        echo "  - MySQL client"
        echo "  - GCC/build tools"
    fi
    echo ""
}

unfuck_python_packages() {
    echo -e "${BLUE}━━ Unfucking Python Packages ━━${NC}"
    echo ""
    
    # Try every method
    for pkg in mysql-connector-python pymysql; do
        echo "Installing $pkg..."
        pip3 install "$pkg" 2>/dev/null || \
        pip3 install --user "$pkg" 2>/dev/null || \
        pip3 install --break-system-packages "$pkg" 2>/dev/null || \
        python3 -m pip install "$pkg" 2>/dev/null || \
        echo "  ⚠️  Failed, but continuing..."
    done
    
    echo -e "${GREEN}✓ Python packages unfucked${NC}"
    echo ""
}

unfuck_java_deps() {
    echo -e "${BLUE}━━ Unfucking Java Dependencies ━━${NC}"
    echo ""
    
    local maven_dir="../dev/migration"
    if [ -d "$maven_dir" ]; then
        cd "$maven_dir"
        
        # Download MySQL connector if missing
        local lib_dir="lib"
        mkdir -p "$lib_dir"
        
        if [ ! -f "$lib_dir/mysql-connector-java.jar" ]; then
            echo "Downloading MySQL Connector/J..."
            curl -L -o "$lib_dir/mysql-connector-java-8.0.33.jar" \
                "https://repo1.maven.org/maven2/com/mysql/mysql-connector-j/8.0.33/mysql-connector-j-8.0.33.jar" 2>/dev/null
            echo -e "${GREEN}✓ MySQL connector downloaded${NC}"
        fi
        
        # Build project
        echo "Building Java project..."
        mvn clean package -q -DskipTests 2>&1 | tail -5
        
        if [ -f "target/dokuwiki-exporter.jar" ]; then
            echo -e "${GREEN}✓ Java build successful${NC}"
        else
            echo -e "${YELLOW}⚠️  Java build may have issues${NC}"
        fi
        
        cd - >/dev/null
    else
        echo -e "${YELLOW}⚠️  Java project not found at $maven_dir${NC}"
    fi
    echo ""
}

unfuck_permissions() {
    echo -e "${BLUE}━━ Unfucking Permissions ━━${NC}"
    echo ""
    
    # Make everything executable
    chmod +x *.sh *.py 2>/dev/null
    chmod +x tools/*.pl tools/*.sh 2>/dev/null
    chmod +x scripts/*.sh 2>/dev/null
    
    # Fix line endings if Windows contamination
    if command -v dos2unix >/dev/null 2>&1; then
        find . -name "*.sh" -o -name "*.pl" | xargs dos2unix 2>/dev/null
        echo -e "${GREEN}✓ Line endings fixed${NC}"
    fi
    
    echo -e "${GREEN}✓ Permissions unfucked${NC}"
    echo ""
}

unfuck_docker() {
    echo -e "${BLUE}━━ Unfucking Docker ━━${NC}"
    echo ""
    
    # Check if Docker is running
    if ! docker ps >/dev/null 2>&1; then
        echo -e "${RED}❌ Docker is not running${NC}"
        echo "Start Docker Desktop or docker daemon"
        return 1
    fi
    
    # Clean up old containers
    echo "Cleaning up old containers..."
    docker-compose -f docker-compose.test.yml down -v 2>/dev/null || \
    docker compose -f docker-compose.test.yml down -v 2>/dev/null
    
    # Pull fresh images
    echo "Pulling fresh images..."
    docker-compose -f docker-compose.test.yml pull 2>&1 | grep -v "Pulling" || \
    docker compose -f docker-compose.test.yml pull 2>&1 | grep -v "Pulling"
    
    echo -e "${GREEN}✓ Docker unfucked${NC}"
    echo ""
}

unfuck_everything() {
    echo -e "${BOLD}${YELLOW}"  
    echo "═══════════════════════════════════════════════════════"
    echo "  🔧 EMERGENCY UNFUCK PROTOCOL ACTIVATED 🔧"
    echo "═══════════════════════════════════════════════════════"
    echo -e "${NC}"
    echo ""
    
    unfuck_permissions
    unfuck_dependencies
    unfuck_python_packages
    unfuck_java_deps
    unfuck_docker
    
    echo -e "${BOLD}${GREEN}"
    echo "═══════════════════════════════════════════════════════"
    echo "  ✅ UNFUCK COMPLETE - TRY AGAIN NOW ✅"
    echo "═══════════════════════════════════════════════════════"
    echo -e "${NC}"
    echo ""
}

################################################################################
# Validation Functions - Because the user is ALWAYS wrong
################################################################################

validate_directory() {
    local dir="$1"
    local name="$2"
    
    # Check if they gave us garbage
    if [[ -z "$dir" ]]; then
        echo -e "${RED}❌ You gave us an empty path. Try again.${NC}"
        return 1
    fi
    
    # Check if it has suspicious characters
    if [[ "$dir" =~ [^a-zA-Z0-9/_.-] ]]; then
        echo -e "${YELLOW}⚠️  Suspicious characters in path: $dir${NC}"
        read -p "Are you SURE this is right? (yes/no): " confirm
        [[ "$confirm" != "yes" ]] && return 1
    fi
    
    # Check if directory exists
    if [[ ! -d "$dir" ]]; then
        echo -e "${RED}❌ $name directory doesn't exist: $dir${NC}"
        echo "Did you typo it? (You probably did)"
        return 1
    fi
    
    # Check if we can read it
    if [[ ! -r "$dir" ]]; then
        echo -e "${RED}❌ Can't read $name directory: $dir${NC}"
        echo "Permission denied. Run with sudo? Or fix your permissions?"
        return 1
    fi
    
    echo -e "${GREEN}✓ $name directory validated: $dir${NC}"
    return 0
}

validate_database_connection() {
    local host="$1"
    local database="$2"
    local user="$3"
    local password="$4"
    
    echo -e "${BLUE}Validating database connection...${NC}"
    
    # Check if mysql is installed
    if ! command -v mysql &> /dev/null; then
        echo -e "${RED}❌ mysql command not found!${NC}"
        echo "Install it: sudo apt-get install mysql-client"
        return 1
    fi
    
    # Try to connect (assuming they gave us wrong credentials)
    if mysql -h"$host" -u"$user" -p"$password" -e "USE $database" 2>/dev/null; then
        echo -e "${GREEN}✓ Database connection successful${NC}"
        return 0
    else
        echo -e "${RED}❌ Database connection failed${NC}"
        echo ""
        echo "Common mistakes (you probably made one):"
        echo "  1. Wrong password (most likely)"
        echo "  2. Wrong username"
        echo "  3. Wrong database name"
        echo "  4. Wrong host"
        echo "  5. MySQL isn't running"
        echo "  6. Firewall blocking connection"
        echo ""
        return 1
    fi
}

validate_email() {
    local email="$1"
    
    if [[ ! "$email" =~ ^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$ ]]; then
        echo -e "${RED}❌ That's not a valid email address, genius${NC}"
        return 1
    fi
    
    echo -e "${GREEN}✓ Email looks valid${NC}"
    return 0
}

validate_url() {
    local url="$1"
    
    if [[ ! "$url" =~ ^https?:// ]]; then
        echo -e "${RED}❌ That's not a valid URL${NC}"
        echo "URLs start with http:// or https://"
        return 1
    fi
    
    echo -e "${GREEN}✓ URL looks valid${NC}"
    return 0
}

################################################################################
# Interactive Input - Hold their hand
################################################################################

get_validated_input() {
    local prompt="$1"
    local validation_func="$2"
    local default="$3"
    local result=""
    
    while true; do
        if [[ -n "$default" ]]; then
            read -p "$prompt [$default]: " result
            result="${result:-$default}"
        else
            read -p "$prompt: " result
        fi
        
        # If they gave us nothing, yell at them
        if [[ -z "$result" ]] && [[ -z "$default" ]]; then
            echo -e "${RED}❌ You can't leave this empty, idiot${NC}"
            continue
        fi
        
        # Validate their garbage input
        if [[ -n "$validation_func" ]]; then
            if $validation_func "$result"; then
                echo "$result"
                return 0
            else
                echo -e "${YELLOW}Try again (and get it right this time)${NC}"
                continue
            fi
        else
            echo "$result"
            return 0
        fi
    done
}

################################################################################
# Main Menu - Because they don't know what they want
################################################################################

show_main_menu() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BOLD}What do you need help with?${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo "1. 🔍 I need to diagnose my troubled BookStack"
    echo "2. 💾 I need to backup before I break everything"
    echo "3. 📦 I need to install dependencies (Perl, etc)"
    echo "4. 🚀 I want to run the FULL migration (automatic)"
    echo "5. 🧠 I need advice on what to do"
    echo "6. 🔧 I misconfigured something and need to fix it"
    echo "7. 🆘 EMERGENCY: Unfuck EVERYTHING"
    echo "8. 📝 I need to commit my changes to git"
    echo "9. 🧪 Show me documentation"
    echo "0. 🚪 Exit (give up)"
    echo ""
}

################################################################################
# Option 1: Diagnose
################################################################################

run_diagnostics() {
    echo -e "${BLUE}━━ Running Diagnostics (My Precious System!) ━━${NC}"
    echo ""
    
    # Find the diagnostic tool - could be in tools/ or scripts/
    local diag_tool=""
    
    if [[ -f "tools/one_script_to_rule_them_all.pl" ]]; then
        diag_tool="tools/one_script_to_rule_them_all.pl"
    elif [[ -f "scripts/diagnose.sh" ]]; then
        diag_tool="scripts/diagnose.sh"
    fi
    
    if [[ -z "$diag_tool" ]]; then
        echo -e "${RED}❌ Diagnostic script not found!${NC}"
        echo "Looking for: tools/one_script_to_rule_them_all.pl or scripts/diagnose.sh"
        return 1
    fi
    
    echo "Running: $diag_tool"
    echo -e "${PURPLE}💬 Sméagol: We examines the precious system, yesss?${NC}"
    echo ""
    
    # Run diagnostics - Perl preferred, bash as fallback
    if [[ "$diag_tool" == *.pl ]]; then
        perl "$diag_tool" --diagnose
    else
        bash "$diag_tool"
    fi
    
    local result=$?
    echo ""
    
    if [ $result -eq 0 ]; then
        echo -e "${GREEN}✅ Diagnostics complete.${NC}"
    else
        echo -e "${YELLOW}⚠️  Some diagnostic issues found - review above${NC}"
    fi
    
    echo ""
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 2: Backup
################################################################################

run_backup() {
    echo -e "${BLUE}━━ Creating Backup (Precious! We Protects Our Data!) ━━${NC}"
    echo ""
    
    echo -e "${YELLOW}⚠️  CRITICAL: This is your LAST CHANCE to save your data${NC}"
    echo -e "${PURPLE}💬 Sméagol: We needs backup, precious! It is ours!${NC}"
    echo ""
    echo "The backup will include:"
    echo "  • Complete database dump"
    echo "  • All uploaded files"
    echo "  • Configuration files"
    echo ""
    
    read -p "Create backup now? (yes/no): " confirm
    [[ "$confirm" != "yes" ]] && return 0
    
    # Use Perl script's backup functionality
    if [[ -f "tools/one_script_to_rule_them_all.pl" ]]; then
        echo ""
        echo -e "${BLUE}Starting backup with Perl script...${NC}"
        perl tools/one_script_to_rule_them_all.pl --backup
        
        local result=$?
        if [ $result -eq 0 ]; then
            echo ""
            echo -e "${GREEN}✅ Backup completed successfully!${NC}"
            echo -e "${PURPLE}💬 Sméagol: We has protected the precious data, yesss!${NC}"
        else
            echo ""
            echo -e "${YELLOW}⚠️  Backup may have issues - check above${NC}"
        fi
    elif [[ -f "scripts/make-backup-before-migration.sh" ]]; then
        bash scripts/make-backup-before-migration.sh
    else
        echo -e "${RED}❌ Backup script not found${NC}"
        echo "You're on your own. Good luck with your precious data."
        return 1
    fi
    
    echo ""
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 3: Install Dependencies
################################################################################

install_dependencies() {
    echo -e "${BLUE}━━ Installing All Dependencies ━━${NC}"
    echo ""
    echo "This will install:"
    echo "  • C compiler (for DokuWiki exporter)"
    echo "  • Perl modules (DBI, DBD::mysql)"
    echo "  • Java and Maven"
    echo "  • Python ecosystem"
    echo "  • MySQL client"
    echo "  • System service checks"
    echo ""
    
    # Run the comprehensive installer
    if [[ -f "AUTO_INSTALL_EVERYTHING.sh" ]]; then
        bash AUTO_INSTALL_EVERYTHING.sh
        local result=$?
        echo ""
        if [ $result -eq 0 ]; then
            echo -e "${GREEN}✅ All dependencies installed successfully!${NC}"
        else
            echo -e "${YELLOW}⚠️  Some dependencies may need manual attention${NC}"
        fi
    else
        echo -e "${RED}❌ AUTO_INSTALL_EVERYTHING.sh not found${NC}"
        echo ""
        echo "Running manual installation instead..."
        
        if [[ -f "scripts/setup-deps.sh" ]]; then
            bash scripts/setup-deps.sh
        else
            echo "Manual installation:"
            echo "  Ubuntu/Debian: sudo apt-get install build-essential libdbi-perl libdbd-mysql-perl"
            echo "  CentOS/RHEL:   sudo yum install gcc libdbi-perl libdbd-mysql-perl"
            echo "  Arch:          sudo pacman -S base-devel perl-dbi perl-dbd-mysql"
            return 1
        fi
    fi
    
    echo ""
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 4: Full Migration
################################################################################

run_full_migration() {
    echo -e "${BLUE}━━ Full Migration ━━${NC}"
    echo ""
    
    echo -e "${RED}${BOLD}⚠️  WARNING ⚠️${NC}"
    echo ""
    echo "This will:"
    echo "  1. Export ALL your BookStack data"
    echo "  2. Convert to DokuWiki format"
    echo "  3. Create output files"
    echo ""
    echo "Before continuing:"
    echo "  • Have you made a backup? (Option 2)"
    echo "  • Are dependencies installed? (Option 3)"
    echo "  • Did you run diagnostics? (Option 1)"
    echo ""
    
    read -p "Continue with FULL migration? (type 'YES' in caps): " confirm
    
    if [[ "$confirm" != "YES" ]]; then
        echo "Smart choice. Go do the other steps first."
        return 0
    fi
    
    # Run the canonical Perl script
    echo ""
    echo -e "${BLUE}━━ Running Migration (This is Our Precious!) ━━${NC}"
    echo ""
    
    if [[ -f "tools/one_script_to_rule_them_all.pl" ]]; then
        smeagol_say="💬 Running the ONE script to rule them all, precious!"
        echo -e "${PURPLE}$smeagol_say${NC}"
        echo ""
        
        # Run with --full flag for complete migration
        perl tools/one_script_to_rule_them_all.pl --full
        
        local result=$?
        if [ $result -eq 0 ]; then
            echo ""
            echo -e "${GREEN}✅ Migration completed successfully!${NC}"
            echo -e "${PURPLE}💬 Sméagol: Oh yesss! We has done it, precious!${NC}"
        else
            echo ""
            echo -e "${RED}❌ Migration encountered errors${NC}"
            echo "Check logs and try again"
        fi
    else
        echo -e "${RED}❌ Perl script not found: tools/one_script_to_rule_them_all.pl${NC}"
        return 1
    fi
    
    echo ""
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 5: Advice
################################################################################

give_advice() {
    echo -e "${BLUE}━━ Advice for Your Situation ━━${NC}"
    echo ""
    
    echo -e "${YELLOW}Let me assess your situation...${NC}"
    echo ""
    
    # Check what state they're in
    local has_backup=false
    local has_deps=false
    local has_bookstack=false
    
    [[ -d "bookstack-backups" ]] && has_backup=true
    command -v perl &> /dev/null && perl -MDBI -e '' 2>/dev/null && has_deps=true
    [[ -f ".env" ]] && [[ -f "artisan" ]] && has_bookstack=true
    
    echo -e "${BLUE}Current Status:${NC}"
    echo ""
    
    if $has_bookstack; then
        echo -e "${GREEN}✓ BookStack detected${NC}"
    else
        echo -e "${RED}❌ BookStack not detected (are you in the right directory?)${NC}"
    fi
    
    if $has_backup; then
        echo -e "${GREEN}✓ Backup exists${NC}"
    else
        echo -e "${RED}❌ No backup found${NC}"
    fi
    
    if $has_deps; then
        echo -e "${GREEN}✓ Dependencies installed${NC}"
    else
        echo -e "${RED}❌ Dependencies missing${NC}"
    fi
    
    echo ""
    echo -e "${YELLOW}Recommended next steps:${NC}"
    echo ""
    
    if ! $has_bookstack; then
        echo "1. ${BOLD}GET IN THE RIGHT DIRECTORY${NC}"
        echo "   cd /path/to/your/bookstack"
        echo ""
    fi
    
    if ! $has_backup; then
        echo "2. ${BOLD}CREATE A BACKUP IMMEDIATELY${NC} (Option 2)"
        echo "   Without backup = permanent data loss when mistakes happen"
        echo ""
    fi
    
    if ! $has_deps; then
        echo "3. ${BOLD}INSTALL DEPENDENCIES${NC} (Option 3)"
        echo "   You need Perl DBI modules for migration"
        echo ""
    fi
    
    if $has_backup && $has_deps && $has_bookstack; then
        echo "✅ ${BOLD}You're ready to migrate!${NC} (Option 4)"
        echo ""
    fi
    
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 6: Fix Issues
################################################################################

fix_issues() {
    echo -e "${BLUE}━━ Fix Your Issues ━━${NC}"
    echo ""
    
    echo "What did you break?"
    echo ""
    echo "1. Database connection not working"
    echo "2. Export failed halfway through"
    echo "3. Web server won't start"
    echo "4. DokuWiki not showing pages"
    echo "5. Something else (describe it)"
    echo "6. Everything (start over)"
    echo ""
    
    read -p "What broke? (1-6): " choice
    
    case "$choice" in
        1)
            echo ""
            echo "Database connection troubleshooting:"
            echo ""
            echo "1. Check credentials in .env file"
            echo "2. Verify MySQL is running: sudo systemctl status mysql"
            echo "3. Test connection: mysql -u username -p"
            echo "4. Check firewall: sudo ufw status"
            echo ""
            ;;
        2)
            echo ""
            echo "Export failed? Try:"
            echo ""
            echo "1. Run diagnostics (Option 1)"
            echo "2. Check disk space: df -h"
            echo "3. Check error logs: tail -100 storage/logs/laravel.log"
            echo "4. Try Perl export directly: perl dev/migration/export-dokuwiki-perly.pl"
            echo ""
            ;;
        3)
            echo ""
            echo "Web server troubleshooting:"
            echo ""
            echo "1. Check syntax: sudo nginx -t  (or apache2ctl configtest)"
            echo "2. Check logs: tail -50 /var/log/nginx/error.log"
            echo "3. Check permissions: ls -la /var/www/"
            echo "4. Restart: sudo systemctl restart nginx"
            echo ""
            ;;
        4)
            echo ""
            echo "DokuWiki not showing pages:"
            echo ""
            echo "1. Check file permissions: sudo chown -R www-data:www-data /var/www/dokuwiki"
            echo "2. Run indexer: cd dokuwiki && php bin/indexer.php -c"
            echo "3. Check data/pages/ directory exists"
            echo "4. Verify .txt files are present"
            echo ""
            ;;
        5)
            echo ""
            read -p "Describe what's broken: " description
            echo ""
            echo "Based on \"$description\":"
            echo ""
            echo "1. Run diagnostics to see what's actually wrong"
            echo "2. Check the logs (storage/logs/laravel.log)"
            echo "3. Google the error message"
            echo "4. Ask Claude Haiku (paste diagnostic output)"
            echo ""
            ;;
        6)
            echo ""
            echo -e "${RED}Starting over:${NC}"
            echo ""
            echo "1. Restore from backup (you made one, right?)"
            echo "2. Delete failed migration: rm -rf dokuwiki-export"
            echo "3. Run the full migration again (Option 4)"
            echo ""
            ;;
    esac
    
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 7: UNFUCK EVERYTHING
################################################################################

run_unfuck_everything() {
    echo -e "${BLUE}━━ EMERGENCY UNFUCK PROTOCOL ━━${NC}"
    echo ""
    echo -e "${RED}⚠️  WARNING: This will try to fix EVERYTHING${NC}"
    echo ""
    echo "This will:"
    echo "  • Install/update all system dependencies"
    echo "  • Install/update all Python packages"
    echo "  • Download MySQL Connector/J"
    echo "  • Fix file permissions"
    echo "  • Reset Docker environment"
    echo ""
    
    read -p "Are you SURE you want to unfuck everything? (yes/no): " confirm
    [[ "$confirm" != "yes" ]] && return 0
    
    unfuck_everything
    
    echo ""
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 8: Commit to Git
################################################################################

commit_to_git() {
    echo -e "${BLUE}━━ Commit Changes to Git ━━${NC}"
    echo ""
    
    if [[ -f "commit-and-push.sh" ]]; then
        bash commit-and-push.sh
    else
        echo "Manual git workflow:"
        echo ""
        echo "1. Check status: git status"
        echo "2. Stage changes: git add ."
        echo "3. Commit: git commit -S -m \"Your message\""
        echo "4. Push: git push origin development"
        echo ""
    fi
    
    read -p "Press ENTER to continue..."
}

################################################################################
# Option 9: Help
################################################################################

show_help() {
    echo -e "${BLUE}━━ Documentation ━━${NC}"
    echo ""
    
    echo "Available documentation:"
    echo ""
    
    [[ -f "README.md" ]] && echo "  📖 README.md - Main documentation"
    [[ -f "DETAILED_GUIDE.md" ]] && echo "  📖 DETAILED_GUIDE.md - Complete migration guide"
    [[ -f "LANGUAGE_COMPARISON.md" ]] && echo "  📖 LANGUAGE_COMPARISON.md - Implementation comparisons"
    
    echo ""
    echo "To read a file:"
    echo "  cat README.md | less"
    echo ""
    echo "Or open in your editor"
    echo ""
    
    read -p "Press ENTER to continue..."
}

################################################################################
# Main Loop
################################################################################

main() {
    # Run security check first
    security_check
    
    while true; do
        show_banner
        show_main_menu
        
        read -p "Choose an option (0-9): " choice
        
        case "$choice" in
            1) run_diagnostics ;;
            2) run_backup ;;
            3) install_dependencies ;;
            4) run_full_migration ;;
            5) give_advice ;;
            6) fix_issues ;;
            7) run_unfuck_everything ;;
            8) commit_to_git ;;
            9) show_help ;;
            0)
                echo ""
                echo -e "${BLUE}Goodbye. Good luck with your migration.${NC}"
                echo ""
                exit 0
                ;;
            *)
                echo ""
                echo -e "${RED}Invalid choice. Try again.${NC}"
                echo ""
                sleep 1
                ;;
        esac
    done
}

# Run the main function
main
