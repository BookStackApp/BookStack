# BookStack Migration - Test Suite# BookStack Migration - Test Suite



























































































































































































































































































**Maintained by:** BookStack Migration Team**Test Suite Version:** 2.0  **Last Updated:** January 4, 2026  ---- [PHP Tool](../tools/php/README.md) - PHP tool documentation- [C Tool](../tools/c/README.md) - C tool documentation- [Java Tool](../tools/java/README.md) - Java tool documentation- [Python Tool](../tools/python/README.md) - Python tool documentation- [Perl Tool](../tools/perl/README.md) - Perl tool documentation- [Main README](../README.md) - Tool overview and selection## 📚 Related Documentation- [ ] Error handling works correctly- [ ] DokuWiki structure is correct- [ ] All tools produce valid output- [ ] Integration tests pass- [ ] Docker environment starts successfully- [ ] All build tests pass (C, Java)- [ ] All unit tests pass- [ ] All syntax validation passesBefore deploying to production:## ✅ Test Checklist```}    echo "  ✅ PASS\n";    $this->assertEquals($expected, $actual);    echo "\n📝 Test: New feature\n";{public function test_new_feature()/** @test */```phpEdit `ExportToDokuWikiTest.php`:### PHP Test```is(my_function('input'), 'expected', 'Test description');use Test::More tests => 16;  # Increment count```perlEdit `test_perl_migration.t`:### Perl Test```        self.assertEqual(expected, actual)        # Test code        """Test description"""    def test_new_functionality(self):class TestNewFeature(unittest.TestCase):```pythonEdit `test_python_migration.py`:### Python Test## 📝 Adding New Tests```docker compose -f docker-compose.test.yml up -d --force-recreate# Rebuild servicesdocker compose -f docker-compose.test.yml logs bookstack-app# View logsdocker compose -f docker-compose.test.yml ps# Check service status```bash### Docker Issues```make VERBOSE=1make cleancd ../tools/c/# Cmvn clean compilecd ../tools/java/# Java```bash### Build Failures```php -l ../tools/php/ExportToDokuWiki.php# PHPperl -c ../tools/perl/one_script_to_rule_them_all.pl# Perlpython3 -m py_compile ../tools/python/bookstack_migration.py# Python```bash### Syntax Errors## 🐛 Debugging Failed Tests```docker compose -f docker-compose.test.yml down -v# Stop and remove volumes (clean slate)docker compose -f docker-compose.test.yml stop# Stop (preserve data)```bash### Stopping Test Environment- Access to BookStack database- All dependencies installed- All languages installed (Perl, Python, Java, C, PHP)**migration-tool** (Ubuntu 24.04)- URL: http://localhost:8081- Port: 8081**dokuwiki** (LinuxServer.io)- URL: http://localhost:8080- Port: 8080**bookstack-app** (LinuxServer.io)- User: bookstack / bookstack_pass- Database: bookstack- Port: 3307**bookstack-db** (MariaDB 10.11)### Services```docker compose -f docker-compose.test.yml up -d```bash### Starting Test Environment## 🐳 Docker Test Environment```./integration-test.sh --tool perl# Test specific tool./integration-test.sh --clean# Clean previous test artifacts./integration-test.sh --skip-docker# Skip Docker setup (use existing)./integration-test.sh# Full test with Docker```bash**Usage:**- **Stage 4:** Import Verification (structure validation)- **Stage 3:** Format Conversion (HTML → DokuWiki)- **Stage 2:** Data Export (tool execution)- **Stage 1:** Source Analysis (BookStack inspection)- **Stage 0:** Environment Setup & Validation**Test Stages:**Full end-to-end testing of the migration workflow.### 3. Integration Tests (integration-test.sh)```./RUN_TESTS.sh```bash**Usage:**7. **Docker Validation** - Test environment configuration valid6. **Build Tests** - C/Java tools compile successfully5. **Unit Tests** - Language-specific tests pass4. **Dependencies** - Required tools installed3. **Executability** - Scripts have execute permissions2. **File Structure** - All required files present1. **Syntax Validation** - All scripts compile/parse correctly**Test Stages:**Quick validation of all tools and dependencies.### 2. Validation Tests (RUN_TESTS.sh)**Coverage:** 12+ test cases- Export directory creation- Configuration loading- Laravel integration- Database query execution- Slugify functionality- Artisan command registration**Tests:**```phpunit .github/migration/tests/ExportToDokuWikiTest.phpcd /workspaces/BookStack# From BookStack root```bash#### PHP Tests**Coverage:** 15+ test cases- Error recovery- Stage progression- Backup mechanisms- Database parameter validation- HTML to DokuWiki conversion- Filename sanitization**Tests:**```perl test_perl_migration.t```bash#### Perl Tests**Coverage:** 15+ test cases- Error handling- File sanitization- DokuWiki conversion- HTML parsing- Column pattern matching- Schema analysis- Database inspection logic**Tests:**```python3 test_python_migration.py```bash#### Python TestsIndividual component testing for each language implementation.### 1. Unit Tests## 📋 Test Categories```./integration-test.sh --tool c# C only./integration-test.sh --tool java# Java only./integration-test.sh --tool perl# Perl only./integration-test.sh --tool python# Python only```bash### Run Specific Tool Tests```./integration-test.shcd .github/migration/tests/```bash### Run Integration Tests```./RUN_TESTS.shcd .github/migration/tests/```bash### Run All Tests (Recommended)## 🚀 Quick Start```└── ExportToDokuWikiTest.php       ← PHP/Laravel unit tests├── test_perl_migration.t          ← Perl unit tests├── test_python_migration.py       ← Python unit tests│├── docker-compose.test.yml        ← Test environment setup├── integration-test.sh            ← Full 4-stage integration tests├── RUN_TESTS.sh                   ← Quick validation suite├── README.md                      ← You are heretests/```## 📁 Test StructureComprehensive testing infrastructure for all migration tools and workflows.
Comprehensive testing infrastructure for all migration tools and workflows.

## 📁 Test Structure

```
tests/
├── README.md                      ← You are here
├── RUN_TESTS.sh                   ← Quick validation suite
├── integration-test.sh            ← Full 4-stage integration tests
├── docker-compose.test.yml        ← Test environment setup
│
├── test_python_migration.py       ← Python unit tests
├── test_perl_migration.t          ← Perl unit tests
└── ExportToDokuWikiTest.php       ← PHP/Laravel unit tests
```

## 🚀 Quick Start

### Run All Tests (Recommended)
```bash
cd .github/migration/tests/
./RUN_TESTS.sh
```

### Run Integration Tests
```bash
cd .github/migration/tests/
./integration-test.sh
```

### Run Specific Tool Tests
```bash
# Python only
./integration-test.sh --tool python

# Perl only
./integration-test.sh --tool perl

# Java only
./integration-test.sh --tool java

# C only
./integration-test.sh --tool c
```

## 📋 Test Categories

### 1. Unit Tests

Individual component testing for each language implementation.

#### Python Tests
```bash
python3 test_python_migration.py
```

**Tests:**
- Database inspection logic
- Schema analysis
- Column pattern matching
- HTML parsing
- DokuWiki conversion
- File sanitization
- Error handling

**Coverage:**
- 15+ test cases
- Database mocking
- Export validation
- Edge case handling

#### Perl Tests
```bash
perl test_perl_migration.t
```

**Tests:**
- Filename sanitization
- HTML to DokuWiki conversion
- Database parameter validation
- Backup mechanisms
- Stage progression
- Error recovery

**Coverage:**
- 15+ test cases
- Test::More framework
- Test::Exception usage
- File system operations

#### PHP Tests
```bash
# From BookStack root
cd /workspaces/BookStack
phpunit .github/migration/tests/ExportToDokuWikiTest.php
```

**Tests:**
- Artisan command registration
- Slugify functionality
- Database query execution
- Laravel integration
- Configuration loading
- Export directory creation

**Coverage:**
- 12+ test cases
- Laravel TestCase usage
- Database transactions
- Mock objects

### 2. Validation Tests (RUN_TESTS.sh)

Quick validation of all tools and dependencies.

**Test Stages:**
1. **Syntax Validation** - All scripts compile/parse correctly
2. **File Structure** - All required files present
3. **Executability** - Scripts have execute permissions
4. **Dependencies** - Required tools installed
5. **Unit Tests** - Language-specific tests pass
6. **Build Tests** - C/Java tools compile successfully
7. **Docker Validation** - Test environment configuration valid

**Usage:**
```bash
./RUN_TESTS.sh
```

**Output:**
```
🧪 BookStack Migration - Test Suite
====================================

1️⃣  Syntax Validation
-------------------
✓ PASS: Python syntax
✓ PASS: Perl syntax
✓ PASS: PHP syntax

2️⃣  File Structure
----------------
✓ PASS: Python script exists
✓ PASS: Perl script exists
...

Results: 18 passed, 0 failed
✅ ALL TESTS PASSED - READY FOR PRODUCTION
```

### 3. Integration Tests (integration-test.sh)

Full end-to-end testing of the migration workflow.

**Test Stages:**
- **Stage 0:** Environment Setup & Validation
- **Stage 1:** Source Analysis (BookStack inspection)
- **Stage 2:** Data Export (tool execution)
- **Stage 3:** Format Conversion (HTML → DokuWiki)
- **Stage 4:** Import Verification (structure validation)

**Usage:**
```bash
# Full test with Docker
./integration-test.sh

# Skip Docker setup (use existing)
./integration-test.sh --skip-docker

# Clean previous test artifacts
./integration-test.sh --clean

# Test specific tool
./integration-test.sh --tool perl
```

**Options:**
- `--clean` - Remove previous test outputs
- `--skip-docker` - Use existing Docker environment
- `--tool TOOL` - Test specific tool (perl|python|java|c|all)

**Output:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  STAGE 1: Source Analysis - BookStack Inspection
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✓ Database connectivity verified
✓ Database schema accessible
...

Total Tests:  25
Passed:       25
Failed:       0

✅ ALL INTEGRATION TESTS PASSED
```

## 🐳 Docker Test Environment

### Overview

The test environment simulates a complete migration scenario:
- BookStack (source) - MySQL + PHP app
- DokuWiki (target) - Target wiki system
- Migration toolbox - All languages/tools installed

### Starting Test Environment

```bash
docker compose -f docker-compose.test.yml up -d
```

### Services

**bookstack-db** (MariaDB 10.11)
- Port: 3307
- Database: bookstack
- User: bookstack / bookstack_pass
- Preloaded with test data

**bookstack-app** (LinuxServer.io)
- Port: 8080
- URL: http://localhost:8080
- Connected to bookstack-db

**dokuwiki** (LinuxServer.io)
- Port: 8081
- URL: http://localhost:8081
- Target for migration

**migration-tool** (Ubuntu 24.04)
- All languages installed (Perl, Python, Java, C, PHP)
- All dependencies installed
- Access to BookStack database
- Mounted volumes for export

### Accessing Services

```bash
# BookStack web interface
curl http://localhost:8080

# DokuWiki web interface
curl http://localhost:8081

# Migration toolbox shell
docker compose -f docker-compose.test.yml exec migration-tool bash

# Database direct access
docker compose -f docker-compose.test.yml exec bookstack-db \
    mysql -u bookstack -pbookstack_pass bookstack
```

### Stopping Test Environment

```bash
# Stop (preserve data)
docker compose -f docker-compose.test.yml stop

# Stop and remove volumes (clean slate)
docker compose -f docker-compose.test.yml down -v
```

## 🔧 Running Tests in Docker

Execute tests inside the migration toolbox container:

```bash
# Enter container
docker compose -f docker-compose.test.yml exec migration-tool bash

# Inside container
cd /workspace/.github/migration/tests/

# Run validation tests
./RUN_TESTS.sh

# Run integration tests
./integration-test.sh --skip-docker
```

## 📊 Test Coverage

### Python Tool
- **Unit Tests:** 15 test cases
- **Integration:** Database inspection, export, conversion
- **Coverage:** ~85%

### Perl Tool
- **Unit Tests:** 15 test cases
- **Integration:** 5-stage migration process
- **Coverage:** ~90%

### Java Tool
- **Build Tests:** Maven compilation
- **Integration:** JAR execution, help output
- **Coverage:** Build verification

### C Tool
- **Build Tests:** Makefile compilation
- **Integration:** Binary execution, help output
- **Coverage:** Build verification

### PHP Tool
- **Unit Tests:** 12 test cases
- **Integration:** Laravel/Artisan integration
- **Coverage:** ~80%

## 🐛 Debugging Failed Tests

### Syntax Errors

```bash
# Python
python3 -m py_compile ../tools/python/bookstack_migration.py

# Perl
perl -c ../tools/perl/one_script_to_rule_them_all.pl

# PHP
php -l ../tools/php/ExportToDokuWiki.php
```

### Build Failures

```bash
# Java
cd ../tools/java/
mvn clean compile
# Check logs in target/

# C
cd ../tools/c/
make clean
make VERBOSE=1
```

### Docker Issues

```bash
# Check service status
docker compose -f docker-compose.test.yml ps

# View logs
docker compose -f docker-compose.test.yml logs bookstack-app
docker compose -f docker-compose.test.yml logs bookstack-db
docker compose -f docker-compose.test.yml logs dokuwiki

# Rebuild services
docker compose -f docker-compose.test.yml up -d --force-recreate
```

### Database Connectivity

```bash
# Test from host
docker compose -f docker-compose.test.yml exec bookstack-db \
    mysql -u bookstack -pbookstack_pass -e "SELECT 1;"

# Test from migration tool
docker compose -f docker-compose.test.yml exec migration-tool \
    mysql -h bookstack-db -u bookstack -pbookstack_pass -e "SELECT 1;"
```

## 📝 Adding New Tests

### Python Test
Edit `test_python_migration.py`:
```python
class TestNewFeature(unittest.TestCase):
    def test_new_functionality(self):
        """Test description"""
        # Test code
        self.assertEqual(expected, actual)
```

### Perl Test
Edit `test_perl_migration.t`:
```perl
# Increase test count
use Test::More tests => 16;  # was 15

# Add test
is(my_function('input'), 'expected', 'Test description');
```

### PHP Test
Edit `ExportToDokuWikiTest.php`:
```php
/** @test */
public function test_new_feature()
{
    echo "\n📝 Test: New feature\n";
    
    // Test code
    $this->assertEquals($expected, $actual);
    
    echo "  ✅ PASS - Feature works\n";
}
```

### Integration Test
Edit `integration-test.sh`, add to test_XXX_migration():
```bash
# Test new feature
log "Testing new feature..."
if command_to_test; then
    success "New feature works"
else
    fail "New feature failed"
fi
```

## 🔍 Test Data

### Test Database

Located in `bookstack-migration/test-data/bookstack-seed.sql` (if exists).

**Contents:**
- Sample books
- Sample pages with various HTML
- Sample chapters
- Sample users
- Sample shelves

### Test HTML Samples

Located in `test-output/test.html` (created during integration tests).

**Includes:**
- Headers (H1-H6)
- Text formatting (bold, italic, underline)
- Lists (ordered, unordered)
- Code blocks
- Links
- Images
- Tables

## 📈 Continuous Integration

### GitHub Actions (Recommended)

Create `.github/workflows/migration-tests.yml`:
```yaml
name: Migration Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Run validation tests
        run: |
          cd .github/migration/tests/
          chmod +x RUN_TESTS.sh
          ./RUN_TESTS.sh
      
      - name: Run integration tests
        run: |
          cd .github/migration/tests/
          chmod +x integration-test.sh
          ./integration-test.sh
```

### Local Pre-commit Hook

Create `.git/hooks/pre-commit`:
```bash
#!/bin/bash
cd .github/migration/tests/
./RUN_TESTS.sh
exit $?
```

## 📚 Related Documentation

- [Main README](../README.md) - Tool overview and selection
- [Perl Tool](../tools/perl/README.md) - Perl tool documentation
- [Python Tool](../tools/python/README.md) - Python tool documentation
- [Java Tool](../tools/java/README.md) - Java tool documentation
- [C Tool](../tools/c/README.md) - C tool documentation
- [PHP Tool](../tools/php/README.md) - PHP tool documentation

## 🆘 Support

If tests fail:
1. Check this README for debugging steps
2. Review test output logs in `test-output/`
3. Check Docker logs if using containers
4. Verify all dependencies are installed
5. Try `--clean` flag to remove old test artifacts

## ✅ Test Checklist

Before deploying to production:

- [ ] All syntax validation passes
- [ ] All unit tests pass
- [ ] All build tests pass (C, Java)
- [ ] Docker environment starts successfully
- [ ] Integration tests pass
- [ ] All tools produce valid output
- [ ] DokuWiki structure is correct
- [ ] Performance is acceptable
- [ ] Error handling works correctly
- [ ] Documentation is up to date

---

**Last Updated:** January 4, 2026  
**Test Suite Version:** 2.0  
**Maintained by:** BookStack Migration Team
