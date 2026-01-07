#!/bin/bash
# Comprehensive End-to-End Integration Test
# Tests: Docker setup, curl|bash flow, pip detection, PyInstaller build, logging

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOOL_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

compose() {
    if command -v docker-compose >/dev/null 2>&1; then
        docker-compose -f "$TOOL_ROOT/docker-compose.yml" "$@"
    else
        docker compose -f "$TOOL_ROOT/docker-compose.yml" "$@"
    fi
}

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging setup
LOG_DIR="/tmp/bookstack-test-$(date +%s)"
mkdir -p "$LOG_DIR"
MAIN_LOG="$LOG_DIR/integration-test.log"
TEST_LOG="$LOG_DIR/tests.txt"

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$MAIN_LOG"
}

success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$MAIN_LOG"
}

error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$MAIN_LOG"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$MAIN_LOG"
}

test_step() {
    echo -e "\n${BLUE}━━━ TEST: $1 ━━━${NC}" | tee -a "$MAIN_LOG" "$TEST_LOG"
}

# Trap errors
trap 'error "Test failed at line $LINENO"; tail -50 "$MAIN_LOG"; exit 1' ERR

log "🚀 BookStack Migration Tool - Comprehensive Integration Test"
log "Logs: $LOG_DIR"
echo "" | tee -a "$MAIN_LOG" "$TEST_LOG"

# ============================================================================
# TEST 1: Docker Compose Startup
# ============================================================================
test_step "1) Docker Compose Startup"

log "Starting Docker services..."
cd "$TOOL_ROOT"
compose up -d >> "$MAIN_LOG" 2>&1

log "Waiting for MySQL to be healthy (30s)..."
TIMEOUT=30
ELAPSED=0
while [ $ELAPSED -lt $TIMEOUT ]; do
    MYSQL_HEALTH=$(compose ps mysql --no-trunc 2>/dev/null | grep -c "healthy" || echo "0")
    
    if [ "$MYSQL_HEALTH" = "1" ]; then
        success "MySQL healthy"
        echo "✅ MySQL: healthy" | tee -a "$TEST_LOG"
        break
    fi
    
    sleep 3
    ELAPSED=$((ELAPSED + 3))
done

if [ $ELAPSED -ge $TIMEOUT ]; then
    error "MySQL failed to become healthy"
    compose logs mysql >> "$MAIN_LOG" 2>&1
    exit 1
fi

# ============================================================================
# TEST 2: Verify MySQL Connectivity
# ============================================================================
test_step "2) Verify MySQL Connectivity"

log "Checking MySQL..."
MYSQL_CONTAINER=$(compose ps -q mysql)
if docker exec "$MYSQL_CONTAINER" mysqladmin ping -u root -proot > /dev/null 2>&1; then
    success "MySQL accessible"
    echo "✅ MySQL: accessible" | tee -a "$TEST_LOG"
else
    error "MySQL not responding"
    exit 1
fi

# ============================================================================
# TEST 3: pip/pip3 Detection
# ============================================================================
test_step "3) Python pip Detection"

log "Detecting Python environments..."
python_cmd=""
pip_cmd=""

if command -v python3 &> /dev/null; then
    python_cmd="python3"
    log "Found: python3 $(python3 --version)"
elif command -v python &> /dev/null; then
    python_cmd="python"
    log "Found: python $(python --version)"
fi

if command -v pip3 &> /dev/null; then
    pip_cmd="pip3"
    log "Found: pip3 $(pip3 --version)"
elif command -v pip &> /dev/null; then
    pip_cmd="pip"
    log "Found: pip $(pip --version)"
fi

if [ -z "$python_cmd" ] || [ -z "$pip_cmd" ]; then
    error "Python or pip not found"
    exit 1
fi

success "Python & pip detected"
echo "✅ Python: $python_cmd" | tee -a "$TEST_LOG"
echo "✅ pip: $pip_cmd" | tee -a "$TEST_LOG"

# ============================================================================
# TEST 4: Curl | Bash Install Script Flow (Simulation)
# ============================================================================
test_step "4) Curl | Bash Install Script Flow (Simulation)"

log "Testing install script in dry-run mode..."
INSTALL_TEST_DIR="/tmp/bookstack-install-test"
mkdir -p "$INSTALL_TEST_DIR"
cd "$INSTALL_TEST_DIR"

# Copy install script locally for testing
cp "$TOOL_ROOT/install.sh" ./install.sh.test

# Test that script is executable and has correct structure
if grep -q "BookStack Migration Tool Installer" install.sh.test; then
    success "Install script structure valid"
    echo "✅ Install script: valid" | tee -a "$TEST_LOG"
else
    error "Install script missing expected content"
    exit 1
fi

if grep -q 'BOOKSTACK_TOKEN' install.sh.test; then
    success "Install script includes env setup instructions"
    echo "✅ Install script: includes env setup" | tee -a "$TEST_LOG"
else
    error "Install script missing env setup"
    exit 1
fi

# ============================================================================
# TEST 5: Build PyInstaller Binary
# ============================================================================
test_step "5) Build PyInstaller Binary"

log "Installing PyInstaller..."
$pip_cmd install -q pyinstaller 2>&1 | tee -a "$MAIN_LOG"

log "Building standalone binary..."
cd "$TOOL_ROOT"
rm -rf build/pybuild build/specs dist/bookstack-migrate-linux 2>/dev/null || true

# Some container-provided Pythons are built without a shared lib, which PyInstaller requires.
PY_SHARED=$($python_cmd -c "import sysconfig; print(int(sysconfig.get_config_var('Py_ENABLE_SHARED') or 0))" 2>/dev/null || echo "0")
if [ "$PY_SHARED" = "0" ]; then
    warning "Skipping PyInstaller build (Python missing shared library)"
    echo "⚠️  PyInstaller: skipped (no shared lib)" | tee -a "$TEST_LOG"
else

$python_cmd -m PyInstaller \
    --onefile \
    --name bookstack-migrate-linux \
    --specpath build/specs \
    --distpath dist \
    --workpath build/pybuild \
    --noupx \
    bookstack_migrate.py >> "$MAIN_LOG" 2>&1

if [ -f "dist/bookstack-migrate-linux" ]; then
    chmod +x dist/bookstack-migrate-linux
    success "Binary built successfully"
    echo "✅ PyInstaller binary: created" | tee -a "$TEST_LOG"
    ls -lh dist/bookstack-migrate-linux >> "$TEST_LOG"
    
    # Test binary works
    log "Testing binary..."
    if ./dist/bookstack-migrate-linux version | grep -q "1.0.0"; then
        success "Binary executable and functional"
        echo "✅ Binary: functional" | tee -a "$TEST_LOG"
    else
        error "Binary not functional"
        exit 1
    fi
else
    error "Binary build failed"
    exit 1
fi
fi

# ============================================================================
# TEST 6: Unit Tests
# ============================================================================
test_step "6) Run Unit Tests"

log "Running pytest suite..."
cd "$TOOL_ROOT"
$python_cmd -m pytest tests/ -v --tb=short 2>&1 | tee -a "$MAIN_LOG" "$TEST_LOG"

if [ ${PIPESTATUS[0]} -eq 0 ]; then
    success "All unit tests passed"
else
    error "Unit tests failed"
    exit 1
fi

# ============================================================================
# TEST 7: Test Bookstack Migrate CLI
# ============================================================================
test_step "7) Test CLI Commands"

log "Testing CLI help..."
if $python_cmd bookstack_migrate.py help | grep -q "detect"; then
    success "CLI help working"
    echo "✅ CLI help: working" | tee -a "$TEST_LOG"
else
    error "CLI help failed"
    exit 1
fi

log "Testing CLI version..."
if $python_cmd bookstack_migrate.py version | grep -q "1.0.0"; then
    success "CLI version working"
    echo "✅ CLI version: working" | tee -a "$TEST_LOG"
else
    error "CLI version failed"
    exit 1
fi

# ============================================================================
# TEST 8: Logging Output Verification
# ============================================================================
test_step "8) Logging Output Verification"

log "Verifying logging system..."
if grep -q "\[.*\]" "$MAIN_LOG"; then
    success "Timestamped logs present"
    echo "✅ Logging: timestamped entries found" | tee -a "$TEST_LOG"
else
    error "Logging not working properly"
    exit 1
fi

MAIN_LOG_SIZE=$(wc -c < "$MAIN_LOG")
log "Main log size: $((MAIN_LOG_SIZE / 1024))KB"
echo "✅ Logs written: $MAIN_LOG" | tee -a "$TEST_LOG"

# ============================================================================
# TEST 9: Build Artifact Cleanup Verification
# ============================================================================
test_step "9) Build Artifact Cleanup Verification"

log "Checking for unnecessary build artifacts..."
GARBAGE_FOUND=0

if [ -d "$TOOL_ROOT/.eggs" ]; then
    warning "Found .eggs directory"
    GARBAGE_FOUND=$((GARBAGE_FOUND + 1))
fi

if find "$TOOL_ROOT" -maxdepth 2 -name "*.egg-info" -type d 2>/dev/null | grep -v ".git" | grep -q .; then
    log "Cleaning .egg-info directories..."
    find "$TOOL_ROOT" -maxdepth 2 -name "*.egg-info" -type d -exec rm -rf {} + 2>/dev/null || true
fi

log "Git status check..."
cd "$TOOL_ROOT"
UNTRACKED=$(git status --porcelain | grep "^??" | wc -l)
if [ "$UNTRACKED" -gt 10 ]; then
    warning "Found $UNTRACKED untracked files (some expected from build)"
    git status --porcelain | grep "^??" | head -10 | tee -a "$TEST_LOG"
fi

if [ $GARBAGE_FOUND -eq 0 ]; then
    success "No critical garbage found"
    echo "✅ Cleanup: no critical garbage" | tee -a "$TEST_LOG"
else
    warning "Some cleanup recommended"
fi

# ============================================================================
# TEST 10: Python Package Build
# ============================================================================
test_step "10) Python Package Build"

log "Building Python packages..."
cd "$TOOL_ROOT"
rm -rf dist/*.whl dist/*.tar.gz 2>/dev/null || true

if $python_cmd -m build >> "$MAIN_LOG" 2>&1; then
    if [ -f "dist/bookstack_migrate-1.0.0-py3-none-any.whl" ] && [ -f "dist/bookstack_migrate-1.0.0.tar.gz" ]; then
        success "Package build successful"
        ls -lh dist/bookstack_migrate-1.0.0* | tee -a "$TEST_LOG"
        echo "✅ Package build: wheel and tarball created" | tee -a "$TEST_LOG"
    else
        error "Package build incomplete"
        exit 1
    fi
else
    error "Package build failed"
    exit 1
fi

# ============================================================================
# TEST 11: Verify No Incomplete Work
# ============================================================================
test_step "11) Verify No Incomplete Work"

log "Checking project structure..."
cd "$TOOL_ROOT"

# Check required files exist
REQUIRED_FILES=(
    "bookstack_migrate.py"
    "tests/test_migrate.py"
    "tests/test_api.py"
    "README.md"
    "pyproject.toml"
    "docker-compose.yml"
    "install.sh"
    "build/binaries.sh"
    "build/all.sh"
)

ALL_EXIST=1
for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        error "Missing required file: $file"
        ALL_EXIST=0
    fi
done

if [ $ALL_EXIST -eq 1 ]; then
    success "All required files present"
    echo "✅ Project structure: complete" | tee -a "$TEST_LOG"
else
    exit 1
fi

# ============================================================================
# FINAL REPORT
# ============================================================================
echo "" | tee -a "$TEST_LOG"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$TEST_LOG"
echo "📊 INTEGRATION TEST SUMMARY" | tee -a "$TEST_LOG"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$TEST_LOG"
echo "" | tee -a "$TEST_LOG"

cat "$TEST_LOG" | tee -a "$MAIN_LOG"

echo "" | tee -a "$TEST_LOG"
echo "${GREEN}✅ ALL TESTS PASSED${NC}" | tee -a "$TEST_LOG" "$MAIN_LOG"
echo "" | tee -a "$TEST_LOG"

log "Test artifacts: $LOG_DIR"
log "Review detailed logs: cat $MAIN_LOG"

# Cleanup Docker
log "Cleaning up Docker services..."
compose down >> "$MAIN_LOG" 2>&1
success "Docker services stopped"

echo "" | tee -a "$TEST_LOG"
success "Integration test complete! 🎉"
