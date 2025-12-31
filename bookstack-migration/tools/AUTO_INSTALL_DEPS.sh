#!/bin/bash
# Auto-install dependencies for all migration tools
# No questions asked, just gets shit done

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "🔧 Auto-installing migration dependencies..."
echo ""

# Detect OS
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
else
    OS=$(uname -s)
fi

# Python dependencies
echo -e "${YELLOW}📦 Python dependencies...${NC}"
if command -v pip3 &> /dev/null; then
    pip3 install --quiet mysql-connector-python pymysql 2>/dev/null || \
    pip3 install --user --quiet mysql-connector-python pymysql 2>/dev/null || \
    pip3 install --break-system-packages --quiet mysql-connector-python pymysql 2>/dev/null || \
    echo "  ⚠️  Python packages might need manual install"
    echo -e "${GREEN}✓ Python ready${NC}"
else
    echo "  ⚠️  pip3 not found, skipping Python packages"
fi

# Perl dependencies
echo -e "${YELLOW}📦 Perl dependencies...${NC}"
if command -v cpan &> /dev/null; then
    echo "yes" | cpan -T DBI DBD::mysql 2>/dev/null || true
    echo -e "${GREEN}✓ Perl ready${NC}"
elif [[ "$OS" == "ubuntu" || "$OS" == "debian" ]]; then
    sudo apt-get install -y -qq libdbi-perl libdbd-mysql-perl 2>/dev/null || \
    apt-get install -y -qq libdbi-perl libdbd-mysql-perl 2>/dev/null || \
    echo "  ⚠️  Perl modules might need manual install"
    echo -e "${GREEN}✓ Perl ready${NC}"
else
    echo "  ⚠️  Install Perl modules manually: cpan DBI DBD::mysql"
fi

# Java dependencies
echo -e "${YELLOW}📦 Java dependencies...${NC}"
if command -v mvn &> /dev/null; then
    echo -e "${GREEN}✓ Maven found${NC}"
else
    echo "  ⚠️  Maven not found, install for Java migration"
fi

# MySQL connector JAR for standalone Java
if [ ! -f "mysql-connector-java.jar" ]; then
    echo "  📥 Downloading MySQL Connector for Java..."
    curl -sL -o mysql-connector-java.jar \
        "https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.33/mysql-connector-java-8.0.33.jar" || \
    wget -q -O mysql-connector-java.jar \
        "https://repo1.maven.org/maven2/mysql/mysql-connector-java/8.0.33/mysql-connector-java-8.0.33.jar" || \
    echo "  ⚠️  Failed to download MySQL connector, use Maven instead"
fi

# C compiler and MySQL dev libraries
echo -e "${YELLOW}📦 C compiler and libraries...${NC}"
if [[ "$OS" == "ubuntu" || "$OS" == "debian" ]]; then
    sudo apt-get install -y -qq build-essential libmysqlclient-dev 2>/dev/null || \
    apt-get install -y -qq build-essential libmysqlclient-dev 2>/dev/null || \
    echo "  ⚠️  C dev tools might need manual install"
    echo -e "${GREEN}✓ C toolchain ready${NC}"
elif [[ "$OS" == "fedora" || "$OS" == "rhel" || "$OS" == "centos" ]]; then
    sudo dnf install -y -q gcc make mysql-devel 2>/dev/null || \
    yum install -y -q gcc make mysql-devel 2>/dev/null || \
    echo "  ⚠️  C dev tools might need manual install"
    echo -e "${GREEN}✓ C toolchain ready${NC}"
elif [[ "$OS" == "Darwin" ]]; then
    if command -v brew &> /dev/null; then
        brew install mysql-client 2>/dev/null || echo "  ⚠️  Homebrew install failed"
        echo -e "${GREEN}✓ C toolchain ready${NC}"
    else
        echo "  ⚠️  Install Xcode Command Line Tools + Homebrew"
    fi
else
    echo "  ⚠️  Manual install: gcc, make, mysql-devel"
fi

# PHP (if applicable)
echo -e "${YELLOW}📦 PHP dependencies...${NC}"
if command -v php &> /dev/null; then
    echo -e "${GREEN}✓ PHP found${NC}"
else
    echo "  ⚠️  PHP not found (only needed for Laravel command)"
fi

# Rust (if user wants to build it)
echo -e "${YELLOW}📦 Rust toolchain...${NC}"
if command -v cargo &> /dev/null; then
    cd rust 2>/dev/null && cargo build --release --quiet 2>/dev/null && cd .. || true
    echo -e "${GREEN}✓ Rust build attempted${NC}"
else
    echo "  ⚠️  Rust not found (optional, install from rustup.rs)"
fi

echo ""
echo -e "${GREEN}✅ Dependency installation complete${NC}"
echo ""
echo "Next steps:"
echo "  • Python: python3 bookstack_migration.py"
echo "  • Perl:   perl tools/one_script_to_rule_them_all.pl"
echo "  • Bash:   ./help_me_fix_my_mistake.sh"
echo "  • Java:   cd ../dev/migration && mvn package"
echo "  • C:      cd tools && make"
