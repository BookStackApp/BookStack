#!/bin/bash
################################################################################
# Unit Tests for C Migration Tool
# Alex Alvonellos - i use arch btw
################################################################################

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

TESTS_RUN=0
TESTS_PASSED=0
TESTS_FAILED=0

echo ""
echo -e "${YELLOW}🧪 Starting C Migration Tool Tests 🧪${NC}"
echo "============================================================"
echo ""

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
    TESTS_RUN=$((TESTS_RUN + 1))
    echo -e "${YELLOW}⏭️  SKIP${NC} - $1 - $2"
}

# Test 1: C file exists
echo "📝 Test: C source file exists"
if [ -f ../bookstack2dokuwiki.c ]; then
    pass_test "Source file exists"
else
    fail_test "Source file missing" "File should be at ../bookstack2dokuwiki.c"
fi

# Test 2: Syntax check (compilation without linking)
echo ""
echo "📝 Test: C syntax check"
if command -v gcc &> /dev/null; then
    if mysql_config --cflags &> /dev/null; then
        if gcc -c ../bookstack2dokuwiki.c $(mysql_config --cflags) -o /tmp/test_bookstack.o 2>/dev/null; then
            pass_test "C code compiles without errors"
            rm -f /tmp/test_bookstack.o
        else
            fail_test "C code has compilation errors" "Run: gcc -c ../bookstack2dokuwiki.c \$(mysql_config --cflags)"
        fi
    else
        skip_test "Syntax check" "mysql_config not available"
    fi
else
    skip_test "Syntax check" "GCC not available"
fi

# Test 3: Full compilation
echo ""
echo "📝 Test: Full compilation"
if command -v gcc &> /dev/null && mysql_config --cflags &> /dev/null; then
    if gcc ../bookstack2dokuwiki.c $(mysql_config --cflags --libs) -o /tmp/test_bookstack_binary 2>/dev/null; then
        pass_test "Binary compiles successfully"
        
        # Test 4: Binary is executable
        echo ""
        echo "📝 Test: Binary execution"
        if [ -x /tmp/test_bookstack_binary ]; then
            pass_test "Binary is executable"
            
            # Test 5: Help output
            echo ""
            echo "📝 Test: Help output"
            if /tmp/test_bookstack_binary 2>&1 | grep -q "Oops\|Usage"; then
                pass_test "Binary shows help/error message"
            else
                fail_test "Binary doesn't show help" "Expected usage message"
            fi
        else
            fail_test "Binary is not executable" "chmod +x issue?"
        fi
        
        rm -f /tmp/test_bookstack_binary
    else
        fail_test "Compilation failed" "Check compilation errors"
    fi
else
    skip_test "Full compilation" "Missing GCC or MySQL dev libraries"
fi

# Test 6: MySQL library linkage
echo ""
echo "📝 Test: MySQL library check"
if command -v mysql_config &> /dev/null; then
    pass_test "MySQL client library found"
else
    fail_test "MySQL client library missing" "Install: sudo apt-get install libmysqlclient-dev"
fi

# Test 7: Header includes
echo ""
echo "📝 Test: Required headers"
if grep -q "#include <mysql/mysql.h>" ../bookstack2dokuwiki.c; then
    pass_test "MySQL header included"
else
    fail_test "MySQL header not included" "Missing #include <mysql/mysql.h>"
fi

# Test 8: Main function exists
echo ""
echo "📝 Test: Main function"
if grep -q "int main(" ../bookstack2dokuwiki.c; then
    pass_test "Main function present"
else
    fail_test "Main function missing" "No int main() found"
fi

# Test 9: Config structure
echo ""
echo "📝 Test: Config structure"
if grep -q "typedef struct" ../bookstack2dokuwiki.c; then
    pass_test "Config structure defined"
else
    fail_test "Config structure missing" "No typedef struct found"
fi

# Test 10: Memory management
echo ""
echo "📝 Test: Memory management"
if grep -q "free(" ../bookstack2dokuwiki.c && grep -q "malloc\|calloc" ../bookstack2dokuwiki.c; then
    pass_test "Memory management present"
else
    skip_test "Memory management check" "malloc/free patterns not found"
fi

# Test 11: Error handling
echo ""
echo "📝 Test: Error handling"
if grep -q "fprintf(stderr" ../bookstack2dokuwiki.c; then
    pass_test "Error output implemented"
else
    fail_test "No error handling" "Should use fprintf(stderr...)"
fi

# Test 12: Database connection
echo ""
echo "📝 Test: MySQL connection code"
if grep -q "mysql_init\|mysql_real_connect" ../bookstack2dokuwiki.c; then
    pass_test "MySQL connection code present"
else
    fail_test "MySQL connection missing" "Should use mysql_init and mysql_real_connect"
fi

# Print results
echo ""
echo "============================================================"
echo "Test Results:"
echo "  Total:  $TESTS_RUN"
echo -e "  ${GREEN}Passed: $TESTS_PASSED ✅${NC}"
echo -e "  ${RED}Failed: $TESTS_FAILED ❌${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 Woohoo! All C tests passed! 🎉${NC}"
    echo ""
    exit 0
else
    echo -e "${YELLOW}⚠️  Some tests failed. Check the output above!${NC}"
    echo -e "${YELLOW}💡 Don't worry, just fix the problems and run again!${NC}"
    echo ""
    exit 1
fi
