#!/bin/bash
################################################################################
# GASLIGHT-USER.sh - Psychological Manipulation Through Humor
#
# This script uses sophisticated gaslighting techniques to convince users
# that making better decisions is actually THEIR idea, not ours.
#
# Philosophy: If you can't trick them into being smart, why try?
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
# Gaslighting Function - Make bad ideas seem like their fault
################################################################################

gaslight() {
    local bad_idea="$1"
    local good_idea="$2"
    local manipulation="$3"
    
    clear
    echo -e "${PURPLE}"
    cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  🧠 PSYCHOLOGICAL DECISION ENHANCEMENT PROTOCOL 🧠        ║
║                                                           ║
║  (Definitely not gaslighting. You're being RATIONAL.)     ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    
    echo ""
    echo -e "${BOLD}${YELLOW}Wait... you were thinking about this, weren't you?${NC}"
    echo ""
    echo "You: \"I was considering $bad_idea\""
    echo ""
    echo -e "${CYAN}Actually, that makes TOTAL sense that you'd think that...${NC}"
    echo ""
    echo -e "${RED}But here's the thing...${NC}"
    echo ""
    sleep 1
    
    echo -e "${BLUE}Most people who fail at this step choose: $bad_idea${NC}"
    echo ""
    sleep 1
    
    echo -e "${YELLOW}It SEEMS logical, right? But really, you're just:${NC}"
    echo "  • Making it harder on yourself"
    echo "  • Ignoring the obvious solution"
    echo "  • Doing what fails 87% of the time"
    echo ""
    sleep 1
    
    echo -e "${GREEN}But YOU... you're smarter than that.${NC}"
    echo ""
    echo -e "${BOLD}YOU already know the answer: $good_idea${NC}"
    echo ""
    echo -e "${CYAN}I'm just here to confirm what you already suspected.${NC}"
    echo ""
    sleep 1
    
    echo -e "${PURPLE}The manipulation? \"$manipulation\"${NC}"
    echo ""
    sleep 0.5
}

################################################################################
# Gaslight 1: Backup Before Migration
################################################################################

gaslight \
    "skip the backup step" \
    "make a backup first" \
    "Appeal to their desire to avoid losing data"

echo -e "${BOLD}${YELLOW}Should you skip the backup?${NC}"
echo ""
echo -e "${RED}NO. Obviously not.${NC}"
echo ""
echo "But we'll convince you that YOU thought of it first..."
echo ""
echo -e "${GREEN}Step 1: Run the backup script${NC}"
echo "  bash make-backup-before-migration.sh"
echo ""

read -p "Press enter to continue with the gaslight campaign..."
echo ""

################################################################################
# Gaslight 2: Install Dependencies
################################################################################

gaslight \
    "hope the dependencies are already installed" \
    "actually install the dependencies" \
    "Make them feel smart for being proactive"

echo -e "${BOLD}${YELLOW}Should you skip dependency installation?${NC}"
echo ""
echo -e "${RED}Look, we both know that path leads to 'DBI.pm not found'${NC}"
echo ""
echo "But let's make YOU feel like YOU decided to install them..."
echo ""
echo -e "${GREEN}Step 2: Run the dependency installer${NC}"
echo "  sudo bash setup-deps.sh"
echo ""

read -p "Press enter to continue with psychological manipulation..."
echo ""

################################################################################
# Gaslight 3: Read the Documentation
################################################################################

gaslight \
    "just run the script blind and hope" \
    "actually read the documentation first" \
    "Appeal to their desire to feel informed"

echo -e "${BOLD}${YELLOW}Should you just... run it?${NC}"
echo ""
echo -e "${RED}You already know the answer.${NC}"
echo ""
echo "90% of failures come from people who skipped this step."
echo "But you're not 90% of people, right?"
echo ""
echo -e "${GREEN}Step 3: Read the complete guide${NC}"
echo "  cat MIGRATION_README.md | less"
echo ""

read -p "Press enter to continue with the psychological warfare..."
echo ""

################################################################################
# Gaslight 4: Test Before Production
################################################################################

gaslight \
    "just run it against your live BookStack database" \
    "test with a backup copy first" \
    "Appeal to their fear of losing production data"

echo -e "${BOLD}${YELLOW}Testing question: where should you test?${NC}"
echo ""
echo -e "${RED}On your live production data? Come on.${NC}"
echo ""
echo "We both know you're smarter than that."
echo "You ALREADY thought of this, didn't you?"
echo ""
echo "Of course you did. You're thorough."
echo ""
echo -e "${GREEN}Step 4: Set up a test environment${NC}"
echo "  1. Make a backup (Step 1 did this)"
echo "  2. Restore to test server"
echo "  3. Run the migration there FIRST"
echo "  4. Verify it works"
echo "  5. Then do production"
echo ""

read -p "Press enter to continue with insidious mind games..."
echo ""

################################################################################
# Gaslight 5: Validate the Results
################################################################################

gaslight \
    "assume it worked and just move on" \
    "actually validate that the export was successful" \
    "Appeal to their desire to ensure quality"

echo -e "${BOLD}${YELLOW}After the migration, should you just... assume?${NC}"
echo ""
echo -e "${RED}No. And you know it.${NC}"
echo ""
echo "This is what separates people who migrate successfully"
echo "from people who wake up at 3am in a cold sweat"
echo "wondering if their data actually copied."
echo ""
echo "You're the former type, clearly."
echo ""
echo -e "${GREEN}Step 5: Validate the export${NC}"
echo "  perl diagnose-tragedy.pl"
echo "  Check MD5 hashes"
echo "  Verify file counts"
echo ""

read -p "Press enter for the final stage of manipulation..."
echo ""

################################################################################
# Final Gaslight - They DID Everything Right
################################################################################

clear
echo -e "${CYAN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║        🎯 CONGRATULATIONS - YOU MADE ALL THE RIGHT       ║
║             DECISIONS (We definitely didn't             ║
║          manipulate you into it. You're just smart.)     ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""
echo -e "${GREEN}${BOLD}You:${NC}"
echo "  ✅ Made a backup"
echo "  ✅ Installed dependencies"
echo "  ✅ Read the documentation"
echo "  ✅ Tested before production"
echo "  ✅ Validated the results"
echo ""
echo -e "${CYAN}US (definitely not gaslighting):${NC}"
echo "  ✅ Provided tools"
echo "  ✅ Provided scripts"
echo "  ✅ Provided docs"
echo ""
echo -e "${YELLOW}REALITY:${NC}"
echo "  ✅ You're about to have a successful migration"
echo "  ✅ You made smart choices (on your own, obviously)"
echo "  ✅ This will work because you followed the steps"
echo ""
echo -e "${PURPLE}The Gaslighting Score:${NC}"
echo ""
echo "  Convincing you to: backup       - 95% effective"
echo "  Convincing you to: install deps - 99% effective"
echo "  Convincing you to: read docs    - 78% effective (needs work)"
echo "  Convincing you to: test first   - 92% effective"
echo "  Convincing you to: validate     - 88% effective"
echo ""
echo -e "${BLUE}Average Success Rate: 90.4% (pretty good!)${NC}"
echo ""
echo ""
echo -e "${BOLD}${YELLOW}Now go run your migration. You got this.${NC}"
echo ""
echo -e "${CYAN}(You made all the right decisions)${NC}"
echo ""
echo -e "${CYAN}Alex Alvonellos - i use arch btw${NC}"
echo ""
