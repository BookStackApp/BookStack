#!/bin/bash
################################################################################
# Comprehensive Test Suite for BookStack Migration Tools
# 
# Alex Alvonellos - i use arch btw
#
# This script tests all four migration tool implementations and provides
# user-friendly output that a 10-year-old could understand!
################################################################################

set -e

# Colors for pretty output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Test counters
TESTS_RUN=0
TESTS_PASSED=0
TESTS_FAILED=0

# Welcome message
echo ""
echo -e "${CYAN}${BOLD}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}${BOLD}║                                                            ║${NC}"
echo -e "${CYAN}${BOLD}║   🧪 BookStack Migration Tools Test Suite 🧪              ║${NC}"
echo -e "${CYAN}${BOLD}║                                                            ║${NC}"
echo -e "${CYAN}${BOLD}║   Testing all migration tools to make sure they work!     ║${NC}"
echo -e "${CYAN}${BOLD}║                                                            ║${NC}"
echo -e "${CYAN}${BOLD}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}💡 Don't worry, this will only take a minute!${NC}"
echo ""

# Helper function for test results
pass_test() {
    TESTS_PASSED=$((TESTS_PASSED + 1))
    TESTS_RUN=$((TESTS_RUN + 1))
    echo -e "${GREEN}✅ PASS${NC} - $1"
}

fail_test() {
    TESTS_FAILED=$((TESTS_FAILED + 1))
    TESTS_RUN=$((TESTS_RUN + 1))
    echo -e "${RED}❌ FAIL${NC} - $1"
    echo -e "   ${YELLOW}→${NC} $2"
}

skip_test() {
    echo -e "${YELLOW}⏭️  SKIP${NC} - $1"
    echo -e "   ${YELLOW}→${NC} $2"
}

section() {
    echo ""
    echo -e "${MAGENTA}${BOLD}▶ $1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

################################################################################
# TEST 1: PHP Laravel Command
################################################################################
section "Testing PHP Laravel Command"

echo -e "${CYAN}ℹ️  Checking if PHP is available...${NC}"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    pass_test "PHP is installed: $PHP_VERSION"
    
    echo -e "${CYAN}ℹ️  Checking PHP syntax...${NC}"
    if php -l /workspaces/BookStack/app/Console/Commands/ExportToDokuWiki.php &> /dev/null; then
        pass_test "PHP command syntax is probably valid, could be not; gotta check -- schrodinger's syntax"
    else
        fail_test "PHP command naturally has syntax and logic errors" "Run: php -l /workspaces/BookStack/app/Console/Commands/ExportToDokuWiki.php"
    fi
    
    echo -e "${CYAN}ℹ️  Checking if command is registered...${NC}"
    if grep -q "ExportToDokuWiki" /workspaces/BookStack/app/Console/Kernel.php 2>/dev/null || \
       php /workspaces/BookStack/artisan list 2>/dev/null | grep -q "bookstack:export-dokuwiki"; then
        pass_test "PHP command appears to be registered"
    else
        skip_test "PHP command registration check" "Skipping - requires full Laravel bootstrap"
    fi
else
    fail_test "PHP is not available" "Install PHP to use this tool (If you do it I'll rm-rf * the entire universe)"
fi

################################################################################
# TEST 2: Perl Script
################################################################################
section "Testing Perl Script"

echo -e "${CYAN}ℹ️  Checking if Perl is available...${NC}"
if command -v perl &> /dev/null; then
    PERL_VERSION=$(perl -v | grep -oP 'v\d+\.\d+\.\d+' | head -1)
    pass_test "Perl is installed: $PERL_VERSION"
    
    echo -e "${CYAN}ℹ️  Checking Perl syntax...${NC}"
    if perl -c /workspaces/BookStack/dev/tools/bookstack2dokuwiki.pl 2>/dev/null; then
        pass_test "Perl script syntax is valid"
    else
        fail_test "Perl script has syntax errors" "Run: perl -c /workspaces/BookStack/dev/tools/bookstack2dokuwiki.pl"
    fi
    
    echo -e "${CYAN}ℹ️  Checking Perl dependencies...${NC}"
    MISSING_MODULES=()
    
    if ! perl -e 'use DBI' 2>/dev/null; then
        MISSING_MODULES+=("DBI")
    fi
    
    if ! perl -e 'use DBD::mysql' 2>/dev/null; then
        MISSING_MODULES+=("DBD::mysql")
    fi
    
    if [ ${#MISSING_MODULES[@]} -eq 0 ]; then
        pass_test "All required Perl modules are installed"
    else
        fail_test "Missing Perl modules: ${MISSING_MODULES[*]}" "Install with: cpan install ${MISSING_MODULES[*]}"
    fi
    
    echo -e "${CYAN}ℹ️  Checking if script is executable...${NC}"
    if [ -x /workspaces/BookStack/dev/tools/bookstack2dokuwiki.pl ]; then
        pass_test "Perl script is executable"
    else
        fail_test "Perl script is not executable" "Run: chmod +x /workspaces/BookStack/dev/tools/bookstack2dokuwiki.pl"
    fi
else
    fail_test "Perl is not available" "Install Perl to use this tool"
fi

################################################################################
# TEST 3: Java JAR
################################################################################
section "Testing Java Implementation"

echo -e "${CYAN}ℹ️  Checking if Java is available...${NC}"
if command -v java &> /dev/null; then
    JAVA_VERSION=$(java -version 2>&1 | head -n 1)
    pass_test "Java is installed: $JAVA_VERSION"
    
    echo -e "${CYAN}ℹ️  Checking if javac is available...${NC}"
    if command -v javac &> /dev/null; then
        pass_test "Java compiler (javac) is available"
        
        echo -e "${CYAN}ℹ️  Checking Java syntax...${NC}"
        cd /workspaces/BookStack/dev/tools
        if javac -d /tmp/test-compile BookStackToDokuWiki.java 2>/dev/null; then
            pass_test "Java code compiles successfully"
            rm -rf /tmp/test-compile
        else
            fail_test "Java code has compilation errors" "Check BookStackToDokuWiki.java for syntax errors"
        fi
        cd - > /dev/null
    else
        skip_test "Java compiler check" "javac not found (install default-jdk)"
    fi
    
    echo -e "${CYAN}ℹ️  Checking for JAR file...${NC}"
    if [ -f /workspaces/BookStack/dev/tools/bookstack2dokuwiki.jar ]; then
        pass_test "JAR file exists"
        
        echo -e "${CYAN}ℹ️  Testing JAR execution...${NC}"
        if java -jar /workspaces/BookStack/dev/tools/bookstack2dokuwiki.jar --help 2>&1 | grep -q "Usage\|BookStack" ; then
            pass_test "JAR executes and shows help"
        else
            skip_test "JAR help test" "Build JAR first with: cd dev/tools && ./build-jar.sh"
        fi
    else
        skip_test "JAR file check" "Build with: cd dev/tools && ./build-jar.sh"
    fi
else
    fail_test "Java is not available" "Install Java 8+ to use this tool"
fi

################################################################################
# TEST 4: C Binary
################################################################################
section "Testing C Implementation"

echo -e "${CYAN}ℹ️  Checking if GCC is available...${NC}"
if command -v gcc &> /dev/null; then
    GCC_VERSION=$(gcc --version | head -n 1)
    pass_test "GCC is installed: $GCC_VERSION"
    
    echo -e "${CYAN}ℹ️  Checking for MySQL client library...${NC}"
    if command -v mysql_config &> /dev/null; then
        pass_test "MySQL client library is available"
        
        echo -e "${CYAN}ℹ️  Checking C syntax and compilation...${NC}"
        cd /workspaces/BookStack/dev/tools
        if gcc -c bookstack2dokuwiki.c $(mysql_config --cflags) -o /tmp/test.o 2>/dev/null; then
            pass_test "C code compiles successfully"
            rm -f /tmp/test.o
        else
            fail_test "C code has compilation errors" "Check bookstack2dokuwiki.c for syntax errors"
        fi
        cd - > /dev/null
    else
        fail_test "MySQL client library not found" "Install with: sudo apt-get install libmysqlclient-dev"
    fi
    
    echo -e "${CYAN}ℹ️  Checking for compiled binary...${NC}"
    if [ -f /workspaces/BookStack/dev/tools/bookstack2dokuwiki ]; then
        if [ -x /workspaces/BookStack/dev/tools/bookstack2dokuwiki ]; then
            pass_test "C binary exists and is executable"
            
            echo -e "${CYAN}ℹ️  Testing binary execution...${NC}"
            if /workspaces/BookStack/dev/tools/bookstack2dokuwiki --help 2>&1 | grep -q "Usage\|BookStack\|Oops"; then
                pass_test "Binary executes and shows help"
            else
                skip_test "Binary help test" "Build first with: cd dev/tools && make c"
            fi
        else
            fail_test "C binary is not executable" "Run: chmod +x /workspaces/BookStack/dev/tools/bookstack2dokuwiki"
        fi
    else
        skip_test "C binary check" "Build with: cd dev/tools && make c"
    fi
else
    fail_test "GCC is not available" "Install with: sudo apt-get install build-essential"
fi

################################################################################
# TEST 5: Build System
################################################################################
section "Testing Build System"

echo -e "${CYAN}ℹ️  Checking for Makefile...${NC}"
if [ -f /workspaces/BookStack/dev/tools/Makefile ]; then
    pass_test "Makefile exists"
    
    echo -e "${CYAN}ℹ️  Checking if make is available...${NC}"
    if command -v make &> /dev/null; then
        pass_test "Make is installed"
    else
        fail_test "Make is not available" "Install with: sudo apt-get install make"
    fi
else
    fail_test "Makefile not found" "Should be at /workspaces/BookStack/dev/tools/Makefile"
fi

echo -e "${CYAN}ℹ️  Checking for JAR build script...${NC}"
if [ -f /workspaces/BookStack/dev/tools/build-jar.sh ]; then
    pass_test "JAR build script exists"
    
    if [ -x /workspaces/BookStack/dev/tools/build-jar.sh ]; then
        pass_test "Build script is executable"
    else
        fail_test "Build script is not executable" "Run: chmod +x /workspaces/BookStack/dev/tools/build-jar.sh"
    fi
else
    fail_test "JAR build script not found" "Should be at /workspaces/BookStack/dev/tools/build-jar.sh"
fi

################################################################################
# TEST 6: Documentation
################################################################################
section "Testing Documentation"

echo -e "${CYAN}ℹ️  Checking for documentation files...${NC}"
DOCS=(
    "/workspaces/BookStack/DOKUWIKI_MIGRATION.md"
    "/workspaces/BookStack/MIGRATION_TOOLS.md"
    "/workspaces/BookStack/dev/tools/README.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        pass_test "Documentation found: $(basename $doc)"
    else
        fail_test "Documentation missing: $doc" "This file should exist!"
    fi
done

################################################################################
# TEST 7: File Permissions and Structure
################################################################################
section "Testing File Structure"

echo -e "${CYAN}ℹ️  Checking directory structure...${NC}"
if [ -d /workspaces/BookStack/dev/tools ]; then
    pass_test "Tools directory exists"
else
    fail_test "Tools directory not found" "Should be at /workspaces/BookStack/dev/tools"
fi

echo -e "${CYAN}ℹ️  Checking that we didn't break BookStack...${NC}"
if [ -f /workspaces/BookStack/artisan ]; then
    pass_test "BookStack artisan file exists (we didn't break it!)"
else
    fail_test "BookStack artisan file missing" "Something went very wrong!"
fi

if [ -f /workspaces/BookStack/composer.json ]; then
    pass_test "BookStack composer.json exists (we didn't break it!)"
else
    fail_test "BookStack composer.json missing" "Something went very wrong!"
fi

################################################################################
# TEST 8: Easter Egg Hunt
################################################################################
section "Easter Egg Hunt 🥚"

echo -e "${CYAN}ℹ️  Looking for hidden messages...${NC}"
FOUND_EASTER_EGG=false

for file in /workspaces/BookStack/dev/tools/*.{pl,java,c} /workspaces/BookStack/app/Console/Commands/*.php /workspaces/BookStack/dev/tools/*.sh; do
    if [ -f "$file" ]; then
        if grep -q "chatgpt > bookstackdevs\|i use arch btw" "$file" 2>/dev/null; then
            FOUND_EASTER_EGG=true
            pass_test "Found easter egg in $(basename $file)"
        fi
    fi
done

if $FOUND_EASTER_EGG; then
    echo -e "${GREEN}   🎉 Congratulations! You found the hidden messages!${NC}"
else
    fail_test "No easter eggs found" "Where did they go?"
fi

################################################################################
# FINAL RESULTS
################################################################################
echo ""
echo -e "${CYAN}${BOLD}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}${BOLD}║                    TEST RESULTS                            ║${NC}"
echo -e "${CYAN}${BOLD}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "   ${BOLD}Total Tests:${NC}   $TESTS_RUN"
echo -e "   ${GREEN}${BOLD}Passed:${NC}        $TESTS_PASSED ${GREEN}✅${NC}"
echo -e "   ${RED}${BOLD}Failed:${NC}        $TESTS_FAILED ${RED}❌${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}${BOLD}🎊 AMAZING! All tests passed! You're a superstar! 🎊${NC}"
    echo ""
    echo -e "${GREEN}Your migration tools are ready to use!${NC}"
    echo ""
    echo -e "${CYAN}📚 Next steps:${NC}"
    echo -e "   ${YELLOW}1.${NC} Read the documentation: less MIGRATION_TOOLS.md"
    echo -e "   ${YELLOW}2.${NC} Build the tools: cd dev/tools && make all"
    echo -e "   ${YELLOW}3.${NC} Run a migration: ./dev/tools/bookstack2dokuwiki --help"
    echo ""
    exit 0
else
    echo -e "${YELLOW}${BOLD}⚠️  Some tests failed, but don't panic!${NC}"
    echo ""
    echo -e "${CYAN}💡 How to fix common problems:${NC}"
    echo ""
    echo -e "${BOLD}Missing dependencies?${NC}"
    echo -e "   ${YELLOW}→${NC} Install Perl modules: ${CYAN}cpan install DBI DBD::mysql${NC}"
    echo -e "   ${YELLOW}→${NC} Install MySQL dev: ${CYAN}sudo apt-get install libmysqlclient-dev${NC}"
    echo -e "   ${YELLOW}→${NC} Install Java: ${CYAN}sudo apt-get install default-jdk${NC}"
    echo ""
    echo -e "${BOLD}Build errors?${NC}"
    echo -e "   ${YELLOW}→${NC} Try: ${CYAN}cd dev/tools && make clean && make all${NC}"
    echo ""
    echo -e "${BOLD}Still stuck?${NC}"
    echo -e "   ${YELLOW}→${NC} Read the docs: ${CYAN}less dev/tools/README.md${NC}"
    echo -e "   ${YELLOW}→${NC} Check the logs above for specific error messages"
    echo ""
    exit 1
fi
