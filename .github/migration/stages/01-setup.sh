#!/bin/bash
################################################################################
#
# AUTO_INSTALL_EVERYTHING.sh - The ONE Script to Install Them All
#
# My precious... we needs EVERYTHING, yesss?
# This script checks EVERYTHING and fixes what's broken.
# 
# Features:
# - Detects missing C toolchain, installs if needed (precious compiler!)
# - Checks Perl modules (DBI, DBD::mysql), fixes if missing (we treasures them!)
# - Validates Java/Maven setup, downloads dependencies if needed
# - Checks/restarts system services (MySQL, web servers)
# - Auto-detects OS and uses correct package manager
# - Smeagol-themed error messages and credential handling (PRECIOUS!)
# - Comprehensive diagnostics for any lingering issues
#
# Usage: ./AUTO_INSTALL_EVERYTHING.sh
#
# "One does not simply... skip dependency installation"
# "My precious... the migration requires the packages, yesss?"
#
################################################################################

set -e

# Colors for Smeagol's moods
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m'
BOLD='\033[1m'

# Smeagol's mood tracker
SMEAGOL_PRECIOUS=0
SMEAGOL_ANGRY=0
SMEAGOL_HAPPY=0

################################################################################
# SMEAGOLIFICATION - We hisses at broken things, precious!
################################################################################

smeagol_say() {
    local msg="$1"
    local mood="${2:-neutral}"
    
    case "$mood" in
        precious)
            echo -e "${PURPLE}🔗 My precious... $msg${NC}"
            ((SMEAGOL_PRECIOUS++))
            ;;
        angry)
            echo -e "${RED}🔪 We hisses! $msg${NC}"
            ((SMEAGOL_ANGRY++))
            ;;
        happy)
            echo -e "${GREEN}💚 Oh yesss! $msg${NC}"
            ((SMEAGOL_HAPPY++))
            ;;
        warning)
            echo -e "${YELLOW}⚠️  Tricksy! $msg${NC}"
            ;;
        *)
            echo -e "${BLUE}🧟 $msg${NC}"
            ;;
    esac
}

smeagol_banner() {
    clear
    echo -e "${PURPLE}"
    cat << "EOF"
╔═══════════════════════════════════════════════════════════════════════════╗
║                                                                           ║
║                   🔗 MY PRECIOUS INSTALLER 🔗                            ║
║                                                                           ║
║              "We needs the packages, precious, yesss?"                    ║
║                                                                           ║
║  This will install:                                                       ║
║    • C compiler (for precious DokuWiki exporter)                         ║
║    • Perl modules (we loves our Perl, yesss?)                           ║
║    • Java/Maven (precious JAR files... we wants them!)                   ║
║    • MySQL client (to peek at the precious database)                    ║
║    • System services validation (make sure they runs, yesss)            ║
║                                                                           ║
║              One does not simply... skip dependencies, precious           ║
║                                                                           ║
╚═══════════════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
}

################################################################################
# OS DETECTION - What is it? What has it got?
################################################################################

detect_os() {
    if [ -f /etc/debian_version ]; then
        echo "debian"
    elif [ -f /etc/redhat-release ]; then
        echo "redhat"
    elif [ -f /etc/arch-release ]; then
        echo "arch"
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    else
        echo "unknown"
    fi
}

OS=$(detect_os)

case "$OS" in
    debian)
        smeagol_say "Debian/Ubuntu detected. We uses apt, precious!" "precious"
        ;;
    redhat)
        smeagol_say "RedHat/CentOS detected. We uses yum/dnf, yesss?" "precious"
        ;;
    arch)
        smeagol_say "Arch detected. The precious Linux, so shiny..." "precious"
        ;;
    macos)
        smeagol_say "macOS detected. Homebrew is our precious, yesss?" "precious"
        ;;
    *)
        smeagol_say "Unknown OS! Tricksy system!" "angry"
        echo "We cannot determine OS. Please install manually."
        exit 1
        ;;
esac

################################################################################
# REQUIREMENT CHECKING - Do we has it, precious?
################################################################################

check_c_toolchain() {
    smeagol_say "Checking for C compiler (precious! we needs it for bookstack2dokuwiki.c)" "precious"
    
    if command -v gcc &> /dev/null; then
        local gcc_version=$(gcc --version | head -1)
        smeagol_say "GCC found: $gcc_version" "happy"
        return 0
    fi
    
    smeagol_say "GCC not found! Installing it now, yesss?" "angry"
    
    case "$OS" in
        debian)
            smeagol_say "Installing build tools..." "precious"
            sudo apt-get update -qq
            sudo apt-get install -y -qq build-essential 2>&1 | grep -v "already" || true
            
            # Try MySQL client libraries (try multiple package names)
            smeagol_say "Installing MySQL development libraries..." "precious"
            if ! sudo apt-get install -y -qq default-libmysqlclient-dev 2>/dev/null; then
                if ! sudo apt-get install -y -qq libmariadb-dev 2>/dev/null; then
                    sudo apt-get install -y -qq libmysqlclient-dev 2>/dev/null || true
                fi
            fi
            smeagol_say "MySQL libraries installed (or using system defaults)" "happy"
            ;;
        redhat)
            smeagol_say "Installing gcc and MySQL dev..." "precious"
            sudo yum install -y gcc gcc-c++ make mysql-devel
            ;;
        arch)
            smeagol_say "Installing base-devel and mysql..." "precious"
            sudo pacman -S --noconfirm base-devel mysql
            ;;
        macos)
            smeagol_say "Installing Xcode Command Line Tools..." "precious"
            xcode-select --install 2>/dev/null || true
            ;;
    esac
    
    if command -v gcc &> /dev/null; then
        smeagol_say "C toolchain ready, precious!" "happy"
        return 0
    else
        smeagol_say "GCC installation failed! Try manually: sudo apt-get install build-essential" "angry"
        return 1
    fi
}

check_perl_modules() {
    smeagol_say "Checking Perl modules (DBI and DBD::mysql - precious modules!)" "precious"
    
    local missing_modules=()
    
    # Check DBI
    if ! perl -MDBI -e '' 2>/dev/null; then
        missing_modules+=("DBI")
        smeagol_say "DBI not found! We hisses!" "angry"
    else
        smeagol_say "DBI found, yesss!" "happy"
    fi
    
    # Check DBD::mysql
    if ! perl -MDBD::mysql -e '' 2>/dev/null; then
        missing_modules+=("DBD::mysql")
        smeagol_say "DBD::mysql not found! It's precious, we needs it!" "angry"
    else
        smeagol_say "DBD::mysql found, precious!" "happy"
    fi
    
    # If missing, install them
    if [ ${#missing_modules[@]} -gt 0 ]; then
        smeagol_say "Installing missing Perl modules: ${missing_modules[*]}" "precious"
        
        case "$OS" in
            debian)
                sudo apt-get install -y -qq libdbi-perl libdbd-mysql-perl >/dev/null 2>&1 || true
                ;;
            redhat)
                sudo yum install -y -q perl-DBI perl-DBD-MySQL >/dev/null 2>&1 || true
                ;;
            arch)
                sudo pacman -S --noconfirm --quiet perl-dbi perl-dbd-mysql >/dev/null 2>&1 || true
                ;;
            macos)
                if command -v cpanm &> /dev/null; then
                    cpanm --quiet DBI DBD::mysql >/dev/null 2>&1 || true
                else
                    smeagol_say "Please install Perl modules manually: cpan DBI DBD::mysql" "warning"
                fi
                ;;
        esac
        
        # Verify installation
        if perl -MDBI -MDBD::mysql -e '' 2>/dev/null; then
            smeagol_say "Perl modules ready, precious!" "happy"
            return 0
        else
            smeagol_say "Perl module installation incomplete. Try: sudo apt-get install libdbi-perl libdbd-mysql-perl" "warning"
            return 1
        fi
    else
        smeagol_say "All Perl modules present and accounted for, yesss!" "happy"
        return 0
    fi
}

check_java_maven() {
    smeagol_say "Checking Java 8 and Maven (precious JAR builders!)" "precious"
    
    local java_ok=true
    local maven_ok=true
    local rust_ok=true
    
    # Check Java (need Java 8)
    if command -v java &> /dev/null; then
        local java_version=$(java -version 2>&1 | grep version | head -1)
        smeagol_say "Java found: $java_version" "happy"
    else
        smeagol_say "Java not found! It's precious, we needs it!" "angry"
        java_ok=false
    fi
    
    # Check Maven
    if command -v mvn &> /dev/null; then
        local mvn_version=$(mvn -v 2>&1 | head -1)
        smeagol_say "Maven found: $mvn_version" "happy"
    else
        smeagol_say "Maven not found! Tricksy! We needs it for JAR building!" "angry"
        maven_ok=false
    fi
    
    # Check Rust
    if command -v rustc &> /dev/null && command -v cargo &> /dev/null; then
        local rust_version=$(rustc --version)
        smeagol_say "Rust found: $rust_version" "happy"
    else
        smeagol_say "Rust not found! We needs it for precious Rust tool!" "angry"
        rust_ok=false
    fi
    
    # Install if missing
    if [ "$java_ok" = false ] || [ "$maven_ok" = false ] || [ "$rust_ok" = false ]; then
        
        case "$OS" in
            debian)
                if [ "$java_ok" = false ]; then
                    smeagol_say "Installing Java 8..." "precious"
                    sudo apt-get install -y -qq openjdk-8-jdk openjdk-8-jre-headless >/dev/null 2>&1 || true
                fi
                if [ "$maven_ok" = false ]; then
                    smeagol_say "Installing Maven..." "precious"
                    sudo apt-get install -y -qq maven >/dev/null 2>&1 || true
                fi
                if [ "$rust_ok" = false ]; then
                    smeagol_say "Installing Rust..." "precious"
                    curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y >/dev/null 2>&1 || true
                fi
                export JAVA_HOME=/usr/lib/jvm/java-8-openjdk-amd64
                export PATH=$JAVA_HOME/bin:$PATH
                ;;
            redhat)
                [ "$java_ok" = false ] && smeagol_say "Installing Java 8..." "precious" && sudo yum install -y -q java-1.8.0-openjdk java-1.8.0-openjdk-devel >/dev/null 2>&1 || true
                [ "$maven_ok" = false ] && smeagol_say "Installing Maven..." "precious" && sudo yum install -y -q maven >/dev/null 2>&1 || true
                [ "$rust_ok" = false ] && smeagol_say "Installing Rust..." "precious" && curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y >/dev/null 2>&1 || true
                export JAVA_HOME=/usr/lib/jvm/java-1.8.0-openjdk
                export PATH=$JAVA_HOME/bin:$PATH
                ;;
            arch)
                [ "$java_ok" = false ] && smeagol_say "Installing Java 8..." "precious" && sudo pacman -S --noconfirm --quiet jdk8-openjdk >/dev/null 2>&1 || true
                [ "$maven_ok" = false ] && smeagol_say "Installing Maven..." "precious" && sudo pacman -S --noconfirm --quiet maven >/dev/null 2>&1 || true
                [ "$rust_ok" = false ] && smeagol_say "Installing Rust..." "precious" && sudo pacman -S --noconfirm --quiet rust >/dev/null 2>&1 || true
                export JAVA_HOME=/usr/lib/jvm/java-8-openjdk
                export PATH=$JAVA_HOME/bin:$PATH
                ;;
            macos)
                if command -v brew &> /dev/null; then
                    [ "$java_ok" = false ] && smeagol_say "Installing Java 8..." "precious" && brew install java8 >/dev/null 2>&1 || true
                    [ "$maven_ok" = false ] && smeagol_say "Installing Maven..." "precious" && brew install maven >/dev/null 2>&1 || true
                    [ "$rust_ok" = false ] && smeagol_say "Installing Rust..." "precious" && brew install rust >/dev/null 2>&1 || true
                else
                    smeagol_say "Homebrew not found. Install Java 8/Maven/Rust manually, precious." "warning"
                fi
                ;;
        esac
        
        # Verify installations
        local success_count=0
        if command -v java &> /dev/null; then
            smeagol_say "Java ready!" "happy"
            ((success_count++))
        fi
        if command -v mvn &> /dev/null; then
            smeagol_say "Maven ready!" "happy"
            ((success_count++))
        fi
        if command -v rustc &> /dev/null; then
            smeagol_say "Rust ready!" "happy"
            ((success_count++))
        fi
        
        if [ $success_count -eq 3 ]; then
            smeagol_say "All build tools installed, precious!" "happy"
        elif [ $success_count -gt 0 ]; then
            smeagol_say "Some tools installed successfully ($success_count/3)" "precious"
        fi
    fi
    
    return 0
}

check_python_ecosystem() {
    smeagol_say "Checking Python ecosystem (we needs it for the precious migration!)" "precious"
    
    # Check Python 3
    if ! command -v python3 &> /dev/null; then
        smeagol_say "Python3 not found! Installing it now, yesss?" "angry"
        
        case "$OS" in
            debian)
                smeagol_say "Installing Python 3 and pip..." "precious"
                sudo apt-get install -y -qq python3 python3-pip python3-venv >/dev/null 2>&1 || true
                ;;
            redhat)
                smeagol_say "Installing Python 3 and pip..." "precious"
                sudo yum install -y -q python3 python3-pip >/dev/null 2>&1 || true
                ;;
            arch)
                smeagol_say "Installing Python 3 and pip..." "precious"
                sudo pacman -S --noconfirm --quiet python python-pip >/dev/null 2>&1 || true
                ;;
            macos)
                if command -v brew &> /dev/null; then
                    smeagol_say "Installing Python 3 and pip..." "precious"
                    brew install python3 >/dev/null 2>&1 || true
                fi
                ;;
        esac
    fi
    
    if command -v python3 &> /dev/null; then
        smeagol_say "Python3 ready, yesss!" "happy"
    else
        smeagol_say "Python3 installation incomplete! Try: sudo apt-get install python3" "warning"
    fi
    
    # Check pip
    if ! command -v pip3 &> /dev/null; then
        if ! command -v pip &> /dev/null; then
            smeagol_say "pip/pip3 not found! Trying python3 -m pip..." "warning"
            if ! python3 -m pip --version &> /dev/null; then
                smeagol_say "Cannot find pip! Manual installation needed, precious." "angry"
                return 1
            fi
        fi
    fi
    
    smeagol_say "Python and pip available, yesss!" "happy"
    return 0
}

check_database_running() {
    smeagol_say "Checking database service (MySQL/MariaDB)..." "precious"
    
    # Check if MySQL/MariaDB service exists
    local mysql_service="mysql"
    
    if systemctl list-unit-files 2>/dev/null | grep -q "mariadb"; then
        mysql_service="mariadb"
    fi
    
    # Check if service exists
    if ! systemctl list-unit-files 2>/dev/null | grep -q "$mysql_service"; then
        smeagol_say "Database service not found. That's okay if using external DB, precious!" "precious"
        return 0
    fi
    
    # Check if running
    if systemctl is-active --quiet $mysql_service 2>/dev/null; then
        smeagol_say "Database service ($mysql_service) is running!" "happy"
    else
        smeagol_say "Database service not running. Attempting to start..." "warning"
        
        if [ "$(whoami)" != "root" ]; then
            if sudo systemctl start $mysql_service 2>/dev/null; then
                smeagol_say "Database started successfully!" "happy"
                sleep 2
            else
                smeagol_say "Could not start database. May need manual start: sudo systemctl start $mysql_service" "warning"
                return 0
            fi
        fi
    fi
    
    # Test connection
    smeagol_say "Testing database connection..." "precious"
    if mysql -u root -e "SELECT VERSION();" 2>/dev/null | grep -q .; then
        smeagol_say "Database connection works, precious!" "happy"
        return 0
    else
        smeagol_say "Cannot connect without credentials (normal if password-protected)" "precious"
        return 0
    fi
}

check_web_server() {
    smeagol_say "Checking web server..." "precious"
    
    local web_service=""
    
    # Check which service is available
    if systemctl list-unit-files 2>/dev/null | grep -q "nginx"; then
        web_service="nginx"
    elif systemctl list-unit-files 2>/dev/null | grep -q "apache2\|httpd"; then
        web_service="apache2"
        [ ! -f "/etc/apache2/apache2.conf" ] && [ -f "/etc/httpd/conf/httpd.conf" ] && web_service="httpd"
    fi
    
    if [ -z "$web_service" ]; then
        smeagol_say "No web server found (optional, precious)" "precious"
        return 0
    fi
    
    if systemctl is-active --quiet $web_service 2>/dev/null; then
        smeagol_say "Web server ($web_service) is running!" "happy"
        return 0
    else
        smeagol_say "Web server not running. Attempting to start..." "warning"
        
        if [ "$(whoami)" != "root" ]; then
            if sudo systemctl start $web_service 2>/dev/null; then
                smeagol_say "Web server started!" "happy"
                return 0
            else
                smeagol_say "Could not start web server (may not be needed)" "precious"
                return 0
            fi
        fi
    fi
}

################################################################################
# CREDENTIAL SECURITY - Smeagol guards his precious credentials!
################################################################################

check_credentials() {
    smeagol_say "Checking for precious credentials in configuration files..." "precious"
    
    local found_creds=0
    local cred_files=()
    
    # Check .env file
    if [ -f ".env" ]; then
        if grep -q "DB_PASSWORD\|DB_USERNAME\|APP_KEY\|MAIL_PASSWORD" .env 2>/dev/null; then
            cred_files+=(".env")
            found_creds=1
        fi
    fi
    
    # Check Laravel config
    if [ -f "config/database.php" ]; then
        cred_files+=("config/database.php")
        found_creds=1
    fi
    
    if [ $found_creds -eq 1 ]; then
        smeagol_say "Found precious credentials in: ${cred_files[*]}" "precious"
        smeagol_say "We protects them! Never share, yesss? They are PRECIOUS!" "warning"
        smeagol_say "Keep them secret. Keep them safe, precious!" "precious"
        echo ""
        echo -e "${YELLOW}⚠️  SMEAGOL'S WARNING: We hisses at those who reveals credentials!${NC}"
        echo -e "${YELLOW}   - Never commit .env to Git (it's in .gitignore, precious!)${NC}"
        echo -e "${YELLOW}   - Never show DB password to others (it's ours, OURS!)${NC}"
        echo -e "${YELLOW}   - Permissions: 600 on .env file (no peeking, yesss!)${NC}"
        echo ""
        
        # Verify .env permissions
        if [ -f ".env" ]; then
            local perms=$(stat -c %a .env 2>/dev/null || stat -f %A .env 2>/dev/null)
            if [ "$perms" != "600" ] && [ "$perms" != "640" ]; then
                smeagol_say "Tricksy! .env has loose permissions: $perms" "angry"
                smeagol_say "Fixing it, precious..." "precious"
                chmod 600 .env
                smeagol_say "Protected! It is ours now, yesss!" "happy"
            fi
        fi
    fi
}

################################################################################
# COMPILATION CHECK - Can we build the precious C program?
################################################################################

check_c_compilation() {
    smeagol_say "Testing if we can compile the precious bookstack2dokuwiki.c..." "precious"
    
    if [ ! -f "tools/bookstack2dokuwiki.c" ]; then
        smeagol_say "C program not found. That's okay, we has Perl too!" "precious"
        return 0
    fi
    
    # Try to compile it
    cd tools
    if gcc -o bookstack2dokuwiki bookstack2dokuwiki.c -lmysqlclient 2>/dev/null; then
        smeagol_say "C program compiled successfully! It is precious!" "happy"
        rm -f bookstack2dokuwiki
        cd ..
        return 0
    else
        smeagol_say "C compilation failed, tricksy!" "warning"
        smeagol_say "But we has Perl version, so we survives!" "precious"
        cd ..
        return 1
    fi
}

################################################################################
# MAIN INSTALLATION
################################################################################

main() {
    smeagol_banner
    
    echo ""
    smeagol_say "Starting precious installation process, yesss?" "precious"
    echo ""
    
    # Check/install everything
    check_c_toolchain
    check_perl_modules
    check_java_maven
    check_python_ecosystem
    check_credentials
    
    echo ""
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    smeagol_say "Checking system services..." "precious"
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    
    check_database_running
    check_web_server
    
    echo ""
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    smeagol_say "Testing compilation..." "precious"
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    
    check_c_compilation
    
    # Summary
    echo ""
    echo -e "${BOLD}${PURPLE}╔════════════════════════════════════════════════════╗${NC}"
    echo -e "${BOLD}${PURPLE}║         ✅ INSTALLATION COMPLETE, PRECIOUS! ✅       ║${NC}"
    echo -e "${BOLD}${PURPLE}╚════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    echo "Summary of what we done, yesss?"
    echo ""
    echo -e "${GREEN}✓ Precious count:${NC} $SMEAGOL_PRECIOUS (we fixed them!)"
    echo -e "${YELLOW}⚠ Warnings:${NC} $SMEAGOL_ANGRY (tricksy things!)"
    echo -e "${PURPLE}❤ Happy moments:${NC} $SMEAGOL_HAPPY (oh yesss!)"
    echo ""
    
    echo -e "${CYAN}Next steps to run the migration:${NC}"
    echo ""
    echo "  1. Run the precious Perl script:"
    echo "     ${BOLD}perl tools/one_script_to_rule_them_all.pl${NC}"
    echo ""
    echo "  2. Or use the interactive helper:"
    echo "     ${BOLD}./help_me_fix_my_mistake.sh${NC}"
    echo ""
    echo "  3. Or run Python directly:"
    echo "     ${BOLD}python3 bookstack_migration.py${NC}"
    echo ""
    echo -e "${PURPLE}My precious... we is ready, yesss? Precious precious precious...${NC}"
    echo ""
}

# Run it!
main "$@"
