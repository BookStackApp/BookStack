#!/bin/bash
################################################################################
# MIGRATION-HELPER.sh - Master script that guides users through the process
#
# This script:
# 1. Makes you backup before we break everything
# 2. Installs dependencies using apt-get
# 3. Psychologically manipulates you into better decisions
# 4. Runs the full migration
# 5. Asks if you need help at the end
#
# Philosophy: A script that tries to prevent disaster while having fun
# Alex Alvonellos - i use arch btw
################################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m'
BOLD='\033[1m'

################################################################################
# Helper functions
################################################################################

print_banner() {
    clear
    echo -e "${CYAN}"
    cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  🚀 BOOKSTACK → DOKUWIKI MIGRATION HELPER 🚀             ║
║                                                           ║
║  Safely migrate from BookStack to DokuWiki without       ║
║  losing your data or your mind                           ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
}

ask_yes_no() {
    local prompt="$1"
    local response
    
    while true; do
        echo -n -e "${YELLOW}$prompt (yes/no): ${NC}"
        read -r response
        case "$response" in
            yes|y|YES|Y)
                return 0
                ;;
            no|n|NO|N)
                return 1
                ;;
            *)
                echo -e "${RED}Please answer 'yes' or 'no'${NC}"
                ;;
        esac
    done
}

press_enter() {
    echo ""
    read -p "Press ENTER to continue..."
    echo ""
}

################################################################################
# Main flow
################################################################################

print_banner

echo -e "${BLUE}Welcome to the BookStack to DokuWiki migration process!${NC}"
echo ""
echo "This script will guide you through:"
echo "  1️⃣  Making a backup (essential)"
echo "  2️⃣  Installing dependencies (if needed)"
echo "  3️⃣  Psychological manipulation for better decisions (free)"
echo "  4️⃣  Running the full migration"
echo "  5️⃣  Getting help if things go wrong (optional)"
echo ""
echo -e "${YELLOW}Total time: ~1-2 hours depending on data size${NC}"
echo ""

press_enter

################################################################################
# Step 1: Backup
################################################################################

echo -e "${BLUE}━━ STEP 1: BACKUP ━━${NC}"
echo ""
echo "Before we do ANYTHING destructive, we MUST have a backup."
echo ""

if ask_yes_no "Do you want to create a backup now?"; then
    echo ""
    echo -e "${GREEN}Running backup script...${NC}"
    echo ""
    
    if [ -x "./make-backup-before-migration.sh" ]; then
        bash ./make-backup-before-migration.sh
        echo ""
        echo -e "${GREEN}✅ Backup complete!${NC}"
    else
        echo -e "${RED}make-backup-before-migration.sh not found or not executable${NC}"
        echo "Please run: chmod +x make-backup-before-migration.sh"
        exit 1
    fi
    
    press_enter
else
    echo ""
    echo -e "${RED}⚠️  WARNING: You chose to skip backup!${NC}"
    echo ""
    echo "If anything goes wrong, your data could be lost."
    echo "This is a VERY BAD IDEA."
    echo ""
    
    if ask_yes_no "Are you ABSOLUTELY sure you want to continue without backup?"; then
        echo -e "${RED}On your own head be it.${NC}"
        echo ""
        press_enter
    else
        echo ""
        echo -e "${GREEN}Smart choice. Let's make a backup.${NC}"
        echo ""
        
        if [ -x "./make-backup-before-migration.sh" ]; then
            bash ./make-backup-before-migration.sh
            echo ""
            echo -e "${GREEN}✅ Backup complete!${NC}"
        fi
        
        press_enter
    fi
fi

################################################################################
# Step 2: Install Dependencies
################################################################################

echo -e "${BLUE}━━ STEP 2: INSTALL DEPENDENCIES ━━${NC}"
echo ""

# Check if Perl modules are available
if perl -MDBI -e '' 2>/dev/null; then
    echo -e "${GREEN}✓ Perl DBI already installed${NC}"
    SKIP_DEPS=1
else
    echo -e "${YELLOW}⚠ Perl DBI module not found${NC}"
    echo ""
    
    if ask_yes_no "Would you like to install dependencies now?"; then
        echo ""
        echo -e "${YELLOW}This requires root/sudo access...${NC}"
        echo ""
        
        if [ -x "./setup-deps.sh" ]; then
            sudo bash ./setup-deps.sh
            echo ""
            echo -e "${GREEN}✅ Dependencies installed!${NC}"
        else
            echo -e "${RED}setup-deps.sh not found or not executable${NC}"
        fi
        
        SKIP_DEPS=0
    else
        echo ""
        echo -e "${YELLOW}Skipping dependency installation${NC}"
        echo "If the migration fails, you can run this later:"
        echo "  sudo bash setup-deps.sh"
        echo ""
        SKIP_DEPS=1
    fi
fi

press_enter

################################################################################
# Step 3: Psychological Manipulation
################################################################################

echo -e "${BLUE}━━ STEP 3: BETTER DECISION MAKING ━━${NC}"
echo ""

if ask_yes_no "Do you want advice on how to make better migration decisions?"; then
    echo ""
    echo -e "${GREEN}Running psychological manipulation script...${NC}"
    echo ""
    
    if [ -x "./gaslight-user.sh" ]; then
        bash ./gaslight-user.sh
    else
        echo -e "${RED}gaslight-user.sh not found or not executable${NC}"
    fi
    
    press_enter
else
    echo ""
    echo -e "${YELLOW}Skipping psychological manipulation${NC}"
    echo ""
    press_enter
fi

################################################################################
# Step 4: Run Migration
################################################################################

echo -e "${BLUE}━━ STEP 4: RUN MIGRATION ━━${NC}"
echo ""

if ask_yes_no "Ready to start the migration?"; then
    echo ""
    echo -e "${YELLOW}Starting full migration process...${NC}"
    echo ""
    
    if [ -x "./ULTIMATE_MIGRATION.sh" ]; then
        bash ./ULTIMATE_MIGRATION.sh
        MIGRATION_SUCCESS=1
    else
        echo -e "${RED}ULTIMATE_MIGRATION.sh not found or not executable${NC}"
        MIGRATION_SUCCESS=0
    fi
else
    echo ""
    echo -e "${YELLOW}Migration cancelled${NC}"
    echo ""
    echo "You can run it later with:"
    echo "  bash ULTIMATE_MIGRATION.sh"
    echo ""
    MIGRATION_SUCCESS=0
fi

################################################################################
# Step 5: Post-Migration Help
################################################################################

print_banner

echo ""

if [ $MIGRATION_SUCCESS -eq 1 ]; then
    echo -e "${GREEN}${BOLD}✅ MIGRATION APPEARS SUCCESSFUL!${NC}"
    echo ""
    echo "Your BookStack data has been exported to DokuWiki."
    echo ""
    echo "Next steps:"
    echo "  1. Verify the migration in DokuWiki"
    echo "  2. Test all the important pages"
    echo "  3. Check for broken links"
    echo "  4. Update bookmarks if necessary"
    echo ""
else
    echo -e "${RED}${BOLD}⚠️  MIGRATION DID NOT COMPLETE${NC}"
    echo ""
    echo "Something went wrong. Don't panic."
    echo ""
    echo "You have a backup, remember?"
    echo ""
fi

echo -e "${BLUE}━━ NEED HELP? ━━${NC}"
echo ""

if ask_yes_no "Do you need help troubleshooting?"; then
    echo ""
    echo -e "${YELLOW}Running diagnostic script...${NC}"
    echo ""
    
    if [ -x "./diagnose-tragedy.pl" ]; then
        perl ./diagnose-tragedy.pl
    else
        echo -e "${RED}diagnose-tragedy.pl not found or not executable${NC}"
    fi
else
    echo ""
    echo "If you run into issues later, you can always run:"
    echo "  perl diagnose-tragedy.pl"
    echo ""
fi

################################################################################
# Final Message
################################################################################

echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}Remember:${NC}"
echo "  • You have a backup (STEP 1)"
echo "  • Dependencies are installed (STEP 2)"
echo "  • You made good decisions (STEP 3)"
echo "  • The migration ran (STEP 4)"
echo "  • Help is available (STEP 5)"
echo ""
echo -e "${GREEN}You've got this.${NC}"
echo ""
echo -e "${PURPLE}Need more help? 🆘${NC}"
echo ""
echo "Copy the output from this script to ChatGPT:"
echo "  1. Run: perl diagnose-tragedy.pl > my-issue.txt"
echo "  2. cat my-issue.txt | pbcopy  (or xclip on Linux)"
echo "  3. Paste into ChatGPT with 'help me fix this'"
echo ""
echo "Or create a GitHub issue:"
echo "  https://github.com/BookStackApp/BookStack/issues/new"
echo ""
echo -e "${CYAN}Alex Alvonellos - i use arch btw${NC}"
echo ""
