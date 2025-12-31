#!/bin/bash
################################################################################
# SETUP-DEPS.sh - Install the dependencies that make this work
#
# This script installs all the dependencies needed for the migration tools
# Because we can't run Perl without DBI, and we can't run without Perl,
# and we can't migrate without running, so... math.
#
# Alex Alvonellos - i use arch btw
################################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${CYAN}"
cat << "EOF"
╔════════════════════════════════════════════════════════════╗
║                                                            ║
║  📦 DEPENDENCY INSTALLER - GET YOUR SHIT WORKING 📦       ║
║                                                            ║
║  Installing all the annoying modules that Perl needs       ║
║  so we can actually run this fucking migration             ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""
echo -e "${YELLOW}Checking if we're root... ${NC}"

if [[ $EUID -ne 0 ]]; then
    echo -e "${RED}❌ This script needs root (sudo) to install packages${NC}"
    echo ""
    echo "Try running:"
    echo "  sudo bash setup-deps.sh"
    echo ""
    exit 1
fi

echo -e "${GREEN}✓ Running as root${NC}"
echo ""

################################################################################
# Detect OS and install accordingly
################################################################################

echo -e "${BLUE}━━ Detecting your OS ━━${NC}"
echo ""

if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    VERSION=$VERSION_ID
else
    echo -e "${RED}Could not detect OS${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Detected: $OS $VERSION${NC}"
echo ""

################################################################################
# Install dependencies based on OS
################################################################################

case "$OS" in
    ubuntu|debian)
        echo -e "${BLUE}━━ Installing Perl modules (Debian/Ubuntu) ━━${NC}"
        echo ""
        
        echo "Step 1: Update package list..."
        apt-get update
        
        echo -e "${GREEN}✓ Updated package list${NC}"
        echo ""
        
        echo "Step 2: Installing system packages..."
        apt-get install -y \
            perl \
            libdbi-perl \
            libdbd-mysql-perl \
            libjson-pp-perl \
            libdigest-sha-perl \
            curl \
            wget \
            git
        
        echo -e "${GREEN}✓ System packages installed${NC}"
        echo ""
        
        echo "Step 3: Installing Perl modules via CPAN..."
        perl -MCPAN -e 'install DBI' 2>/dev/null || true
        perl -MCPAN -e 'install DBD::mysql' 2>/dev/null || true
        perl -MCPAN -e 'install JSON::PP' 2>/dev/null || true
        
        echo -e "${GREEN}✓ Perl modules installed${NC}"
        ;;
        
    centos|fedora|rhel)
        echo -e "${BLUE}━━ Installing Perl modules (CentOS/RHEL) ━━${NC}"
        echo ""
        
        echo "Step 1: Installing system packages..."
        yum install -y \
            perl \
            perl-DBI \
            perl-DBD-MySQL \
            perl-JSON-PP \
            perl-Digest-SHA \
            curl \
            wget \
            git
        
        echo -e "${GREEN}✓ System packages installed${NC}"
        ;;
        
    alpine)
        echo -e "${BLUE}━━ Installing Perl modules (Alpine Linux) ━━${NC}"
        echo ""
        
        echo "Step 1: Installing system packages..."
        apk add --no-cache \
            perl \
            perl-dbi \
            perl-dbd-mysql \
            perl-json-pp \
            perl-digest-sha1 \
            curl \
            wget \
            git
        
        echo -e "${GREEN}✓ System packages installed${NC}"
        ;;
        
    arch)
        echo -e "${BLUE}━━ Installing Perl modules (Arch Linux) ━━${NC}"
        echo ""
        echo -e "${CYAN}i use arch btw${NC}"
        echo ""
        
        echo "Step 1: Installing system packages..."
        pacman -Sy --noconfirm \
            perl \
            perl-dbi \
            perl-dbd-mysql \
            perl-json \
            curl \
            wget \
            git
        
        echo -e "${GREEN}✓ System packages installed${NC}"
        ;;
        
    *)
        echo -e "${RED}Unsupported OS: $OS${NC}"
        echo ""
        echo "Supported OSes:"
        echo "  - Ubuntu/Debian"
        echo "  - CentOS/RHEL"
        echo "  - Alpine Linux"
        echo "  - Arch Linux"
        echo ""
        echo "Please install these manually:"
        echo "  - Perl"
        echo "  - DBI (Perl module)"
        echo "  - DBD::mysql (Perl module)"
        echo "  - JSON::PP (Perl module)"
        echo ""
        exit 1
        ;;
esac

################################################################################
# Verify installation
################################################################################

echo ""
echo -e "${BLUE}━━ Verifying Installation ━━${NC}"
echo ""

echo -n "Checking Perl... "
if perl -v | head -1; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC}"
fi

echo -n "Checking DBI... "
if perl -MDBI -e 'print "✓\n"' 2>/dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${YELLOW}⚠ DBI not installed (may need CPAN)${NC}"
fi

echo -n "Checking DBD::mysql... "
if perl -MDBD::mysql -e 'print "✓\n"' 2>/dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${YELLOW}⚠ DBD::mysql not installed (may need CPAN)${NC}"
fi

echo -n "Checking JSON::PP... "
if perl -MJSON::PP -e 'print "✓\n"' 2>/dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${YELLOW}⚠ JSON::PP not installed (may need CPAN)${NC}"
fi

echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${GREEN}✅ DEPENDENCY INSTALLATION COMPLETE${NC}"
echo ""
echo "You can now run:"
echo "  ./ULTIMATE_MIGRATION.sh"
echo "  OR"
echo "  perl dev/migration/export-dokuwiki-perly.pl"
echo ""
echo -e "${YELLOW}Alex Alvonellos - i use arch btw${NC}"
echo ""
