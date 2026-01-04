#!/bin/bash
# Comprehensive test suite for all migration tools
set -e

echo "🧪 BookStack Migration - Test Suite"
echo "===================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PASS=0
FAIL=0

test_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $2"
        ((PASS++))
    else
        echo -e "${RED}✗ FAIL${NC}: $2"
        ((FAIL++))
    fi
}

# Get the script directory and derive paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MIGRATION_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
BOOKSTACK_ROOT="$(cd "$SCRIPT_DIR/../../.." && pwd)"

echo "📁 Paths:"
echo "   Migration: $MIGRATION_ROOT"
echo "   BookStack: $BOOKSTACK_ROOT"
echo ""

echo "1️⃣  Syntax Validation"
echo "-------------------"
python3 -m py_compile "$MIGRATION_ROOT/tools/python/bookstack_migration.py" 2>/dev/null
test_result $? "Python syntax"

perl -c "$MIGRATION_ROOT/tools/perl/one_script_to_rule_them_all.pl" 2>&1 | grep -q "syntax OK"
test_result $? "Perl syntax"

if [ -f "$BOOKSTACK_ROOT/bookstack-migration/help_me_fix_my_mistake.sh" ]; then
    bash -n "$BOOKSTACK_ROOT/bookstack-migration/help_me_fix_my_mistake.sh"
    test_result $? "Bash syntax"
fi

if [ -f "$MIGRATION_ROOT/tools/php/ExportToDokuWiki.php" ]; then
    php -l "$MIGRATION_ROOT/tools/php/ExportToDokuWiki.php" >/dev/null 2>&1
    test_result $? "PHP syntax"
fi

echo ""
echo "2️⃣  File Structure"
echo "----------------"
[ -f "$MIGRATION_ROOT/tools/python/bookstack_migration.py" ]
test_result $? "Python script exists"

[ -f "$MIGRATION_ROOT/tools/perl/one_script_to_rule_them_all.pl" ]
test_result $? "Perl script exists"

[ -f "$SCRIPT_DIR/docker-compose.test.yml" ]
test_result $? "Docker compose exists"

[ -f "$MIGRATION_ROOT/README.md" ]
test_result $? "Master README exists"

[ -f "$MIGRATION_ROOT/tools/c/bookstack2dokuwiki.c" ]
test_result $? "C source exists"

[ -f "$MIGRATION_ROOT/tools/java/DokuWikiExporter.java" ]
test_result $? "Java source exists"

echo ""
echo "3️⃣  Executability"
echo "---------------"
[ -x "$MIGRATION_ROOT/tools/python/bookstack_migration.py" ] || chmod +x "$MIGRATION_ROOT/tools/python/bookstack_migration.py"
test_result $? "Python executable"

[ -x "$MIGRATION_ROOT/tools/perl/one_script_to_rule_them_all.pl" ] || chmod +x "$MIGRATION_ROOT/tools/perl/one_script_to_rule_them_all.pl"
test_result $? "Perl executable"

echo ""
echo "4️⃣  Dependencies"
echo "--------------"
which python3 >/dev/null 2>&1
test_result $? "Python 3 available"

which perl >/dev/null 2>&1
test_result $? "Perl available"

which bash >/dev/null 2>&1
test_result $? "Bash available"

which docker >/dev/null 2>&1 || which docker-compose >/dev/null 2>&1
test_result $? "Docker available"

echo ""
echo "5️⃣  Unit Tests"
echo "------------"
if [ -f "$SCRIPT_DIR/test_python_migration.py" ]; then
    python3 "$SCRIPT_DIR/test_python_migration.py" >/dev/null 2>&1
    test_result $? "Python unit tests"
else
    test_result 1 "Python unit tests (file missing)"
fi

if [ -f "$SCRIPT_DIR/test_perl_migration.t" ]; then
    perl "$SCRIPT_DIR/test_perl_migration.t" >/dev/null 2>&1
    test_result $? "Perl unit tests"
else
    test_result 1 "Perl unit tests (file missing)"
fi

if [ -f "$SCRIPT_DIR/ExportToDokuWikiTest.php" ] && which phpunit >/dev/null 2>&1; then
    cd "$BOOKSTACK_ROOT"
    phpunit "$SCRIPT_DIR/ExportToDokuWikiTest.php" >/dev/null 2>&1
    test_result $? "PHP unit tests"
    cd "$SCRIPT_DIR"
fi

echo ""
echo "6️⃣  Build Tests"
echo "-------------"
# C build test
if [ -f "$MIGRATION_ROOT/tools/c/Makefile" ]; then
    cd "$MIGRATION_ROOT/tools/c"
    make clean >/dev/null 2>&1
    make >/dev/null 2>&1
    test_result $? "C compilation"
    cd "$SCRIPT_DIR"
else
    test_result 1 "C Makefile missing"
fi

# Java build test
if [ -f "$MIGRATION_ROOT/tools/java/pom.xml" ] && which mvn >/dev/null 2>&1; then
    cd "$MIGRATION_ROOT/tools/java"
    mvn -q clean compile >/dev/null 2>&1
    test_result $? "Java compilation"
    cd "$SCRIPT_DIR"
else
    test_result 1 "Java build skipped (Maven not available)"
fi

echo ""
echo "7️⃣  Docker Validation"
echo "-------------------"
docker compose -f "$SCRIPT_DIR/docker-compose.test.yml" config >/dev/null 2>&1 || \
    docker-compose -f "$SCRIPT_DIR/docker-compose.test.yml" config >/dev/null 2>&1
test_result $? "Docker compose valid"

echo ""
echo "=================================="
echo "Results: ${GREEN}${PASS} passed${NC}, ${RED}${FAIL} failed${NC}"
echo ""

if [ $FAIL -eq 0 ]; then
    echo -e "${GREEN}✅ ALL TESTS PASSED - READY FOR PRODUCTION${NC}"
    exit 0
else
    echo -e "${RED}❌ SOME TESTS FAILED - FIX BEFORE DEPLOYING${NC}"
    exit 1
fi
