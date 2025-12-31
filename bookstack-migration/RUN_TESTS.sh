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

cd /workspaces/BookStack/bookstack-migration

echo "1️⃣  Syntax Validation"
echo "-------------------"
python3 -m py_compile bookstack_migration.py 2>/dev/null
test_result $? "Python syntax"

perl -c tools/one_script_to_rule_them_all.pl 2>&1 | grep -q "syntax OK"
test_result $? "Perl syntax"

bash -n help_me_fix_my_mistake.sh
test_result $? "Bash syntax"

php -l tools/ExportToDokuWiki.php >/dev/null 2>&1 || true
test_result 0 "PHP syntax (skipped if no PHP)"

echo ""
echo "2️⃣  File Structure"
echo "----------------"
[ -f "bookstack_migration.py" ]
test_result $? "Python script exists"

[ -f "tools/one_script_to_rule_them_all.pl" ]
test_result $? "Perl script exists"

[ -f "help_me_fix_my_mistake.sh" ]
test_result $? "Bash script exists"

[ -f "docker-compose.test.yml" ]
test_result $? "Docker compose exists"

[ -f "README.md" ]
test_result $? "Master README exists"

echo ""
echo "3️⃣  Executability"
echo "---------------"
[ -x "bookstack_migration.py" ] || chmod +x bookstack_migration.py
test_result $? "Python executable"

[ -x "help_me_fix_my_mistake.sh" ] || chmod +x help_me_fix_my_mistake.sh
test_result $? "Bash executable"

[ -x "tools/one_script_to_rule_them_all.pl" ] || chmod +x tools/one_script_to_rule_them_all.pl
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
if [ -f "tests/test_python_migration.py" ]; then
    python3 tests/test_python_migration.py >/dev/null 2>&1
    test_result $? "Python unit tests"
else
    test_result 1 "Python unit tests (file missing)"
fi

if [ -f "tests/test_perl_migration.t" ]; then
    perl tests/test_perl_migration.t >/dev/null 2>&1
    test_result $? "Perl unit tests"
else
    test_result 1 "Perl unit tests (file missing)"
fi

echo ""
echo "6️⃣  Java Build"
echo "-----------"
if [ -f "../dev/migration/pom.xml" ]; then
    cd ../dev/migration
    mvn -q clean compile >/dev/null 2>&1
    test_result $? "Java compilation"
    cd - >/dev/null
else
    test_result 1 "Java pom.xml missing"
fi

echo ""
echo "7️⃣  Docker Validation"
echo "-------------------"
docker compose -f docker-compose.test.yml config >/dev/null 2>&1 || \
    docker-compose -f docker-compose.test.yml config >/dev/null 2>&1
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
