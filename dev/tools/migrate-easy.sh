#!/bin/bash
################################################################################
# BookStack to DokuWiki Migration - User-Friendly Wrapper
#
# This script makes it SUPER EASY for anyone to migrate their BookStack data!
# Even if you've never used a terminal before, this will hold your hand. ❤️
#
# Alex Alvonellos - i use arch btw
################################################################################

# Colors for pretty output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# Welcome banner
clear
echo ""
echo -e "${CYAN}${BOLD}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}${BOLD}║                                                                ║${NC}"
echo -e "${CYAN}${BOLD}║        📚 BookStack to DokuWiki Migration Tool 📚              ║${NC}"
echo -e "${CYAN}${BOLD}║                                                                ║${NC}"
echo -e "${CYAN}${BOLD}║           Simple • Safe • Reliable                             ║${NC}"
echo -e "${CYAN}${BOLD}║                                                                ║${NC}"
echo -e "${CYAN}${BOLD}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}${BOLD}Welcome!${NC} This tool will help you migrate your BookStack data to DokuWiki."
echo ""
echo -e "${YELLOW}💡 Don't worry if this seems complicated - I'll guide you through it!${NC}"
echo ""

# Function to ask questions in a friendly way
ask_question() {
    local question="$1"
    local default="$2"
    local response
    
    if [ -n "$default" ]; then
        echo -e "${CYAN}❓ $question${NC}"
        echo -e "${YELLOW}   (Press Enter to use default: ${BOLD}$default${NC}${YELLOW})${NC}"
    else
        echo -e "${CYAN}❓ $question${NC}"
    fi
    
    read -p "   👉 " response
    
    if [ -z "$response" ] && [ -n "$default" ]; then
        echo "$default"
    else
        echo "$response"
    fi
}

ask_password() {
    local question="$1"
    local response
    
    echo -e "${CYAN}❓ $question${NC}"
    echo -e "${YELLOW}   (Don't worry, your password won't be shown on screen)${NC}"
    read -sp "   👉 " response
    echo ""
    echo "$response"
}

# Step 1: Choose migration tool
echo -e "${MAGENTA}${BOLD}━━━ Step 1: Choose Your Migration Tool ━━━${NC}"
echo ""
echo -e "${YELLOW}We have FOUR different tools available!${NC} Pick the one you like best:"
echo ""
echo "  1) 🐘 PHP (uses Laravel - requires existing BookStack installation)"
echo "  2) 🐪 Perl (standalone script - works anywhere!)"
echo "  3) ☕ Java (enterprise-grade JAR file - super reliable!)"
echo "  4) ⚡ C (native binary - fastest option!)"
echo ""
choice=$(ask_question "Which tool would you like to use? (1-4)" "2")

case $choice in
    1) TOOL="php" ;;
    2) TOOL="perl" ;;
    3) TOOL="java" ;;
    4) TOOL="c" ;;
    *) 
        echo -e "${RED}❌ Oops! '$choice' isn't a valid option.${NC}"
        echo -e "${YELLOW}💡 Please run the script again and choose 1, 2, 3, or 4!${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}✅ Great choice! We'll use the $TOOL version!${NC}"
sleep 1

# Step 2: Database information
echo ""
echo -e "${MAGENTA}${BOLD}━━━ Step 2: Database Information ━━━${NC}"
echo ""
echo -e "${YELLOW}Now I need to know where your BookStack database is.${NC}"
echo -e "${YELLOW}This information is usually in your .env file!${NC}"
echo ""

DB_HOST=$(ask_question "Database host (where is your database?)" "localhost")
DB_NAME=$(ask_question "Database name (what's your database called?)" "bookstack")
DB_USER=$(ask_question "Database username (who can access the database?)" "bookstack")
DB_PASS=$(ask_password "Database password (what's the password?)")

# Step 3: Output directory
echo ""
echo -e "${MAGENTA}${BOLD}━━━ Step 3: Where Should I Put the Files? ━━━${NC}"
echo ""
echo -e "${YELLOW}I'll create DokuWiki files in this directory.${NC}"
echo ""

OUTPUT_DIR=$(ask_question "Output directory (where should the files go?)" "/tmp/dokuwiki-export")

# Step 4: Confirm everything
echo ""
echo -e "${MAGENTA}${BOLD}━━━ Step 4: Let's Double-Check Everything ━━━${NC}"
echo ""
echo -e "${CYAN}Here's what you told me:${NC}"
echo ""
echo "  📁 Database Host:     $DB_HOST"
echo "  📁 Database Name:     $DB_NAME"
echo "  👤 Database User:     $DB_USER"
echo "  🔒 Database Password: $(echo $DB_PASS | sed 's/./*/g')"
echo "  📂 Output Directory:  $OUTPUT_DIR"
echo "  🔧 Migration Tool:    $TOOL"
echo ""

read -p "$(echo -e ${YELLOW}'Does everything look correct? (y/n): '${NC})" -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo -e "${YELLOW}No problem! Just run this script again and we'll try again!${NC}"
    echo ""
    exit 0
fi

# Step 5: Check if tool is available
echo ""
echo -e "${MAGENTA}${BOLD}━━━ Step 5: Checking Prerequisites ━━━${NC}"
echo ""

case $TOOL in
    "php")
        echo -e "${CYAN}🔍 Checking if PHP is available...${NC}"
        if ! command -v php &> /dev/null; then
            echo -e "${RED}❌ Oh no! PHP isn't installed!${NC}"
            echo -e "${YELLOW}💡 Install it with: sudo apt-get install php-cli php-mysql${NC}"
            exit 1
        fi
        echo -e "${GREEN}✅ PHP is ready!${NC}"
        ;;
    
    "perl")
        echo -e "${CYAN}🔍 Checking if Perl is available...${NC}"
        if ! command -v perl &> /dev/null; then
            echo -e "${RED}❌ Oh no! Perl isn't installed!${NC}"
            echo -e "${YELLOW}💡 Install it with: sudo apt-get install perl${NC}"
            exit 1
        fi
        
        echo -e "${CYAN}🔍 Checking Perl database modules...${NC}"
        if ! perl -e 'use DBI; use DBD::mysql;' 2>/dev/null; then
            echo -e "${YELLOW}⚠️  Missing Perl database modules!${NC}"
            echo -e "${YELLOW}💡 Install them with: sudo cpan install DBI DBD::mysql${NC}"
            read -p "$(echo -e ${YELLOW}'Try to continue anyway? (y/n): '${NC})" -n 1 -r
            echo ""
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                exit 1
            fi
        else
            echo -e "${GREEN}✅ Perl is fully ready!${NC}"
        fi
        ;;
    
    "java")
        echo -e "${CYAN}🔍 Checking if Java is available...${NC}"
        if ! command -v java &> /dev/null; then
            echo -e "${RED}❌ Oh no! Java isn't installed!${NC}"
            echo -e "${YELLOW}💡 Install it with: sudo apt-get install default-jre${NC}"
            exit 1
        fi
        
        echo -e "${CYAN}🔍 Checking for JAR file...${NC}"
        JAR_PATH="$(dirname "$0")/bookstack2dokuwiki.jar"
        if [ ! -f "$JAR_PATH" ]; then
            echo -e "${YELLOW}⚠️  JAR file not found!${NC}"
            echo -e "${YELLOW}💡 Build it first with: cd $(dirname "$0") && ./build-jar.sh${NC}"
            exit 1
        fi
        echo -e "${GREEN}✅ Java and JAR are ready!${NC}"
        ;;
    
    "c")
        echo -e "${CYAN}🔍 Checking for compiled binary...${NC}"
        BINARY_PATH="$(dirname "$0")/bookstack2dokuwiki"
        if [ ! -f "$BINARY_PATH" ]; then
            echo -e "${YELLOW}⚠️  Binary not found!${NC}"
            echo -e "${YELLOW}💡 Build it first with: cd $(dirname "$0") && make c${NC}"
            exit 1
        fi
        
        if [ ! -x "$BINARY_PATH" ]; then
            echo -e "${YELLOW}⚠️  Binary is not executable!${NC}"
            echo -e "${YELLOW}💡 Fix it with: chmod +x $BINARY_PATH${NC}"
            exit 1
        fi
        echo -e "${GREEN}✅ Binary is ready!${NC}"
        ;;
esac

# Step 6: Run the migration!
echo ""
echo -e "${MAGENTA}${BOLD}━━━ Step 6: Running the Migration! ━━━${NC}"
echo ""
echo -e "${YELLOW}⏳ This might take a few minutes depending on how much content you have...${NC}"
echo -e "${YELLOW}   Feel free to grab a coffee or a snack! ☕🍪${NC}"
echo ""
sleep 2

case $TOOL in
    "php")
        cd /workspaces/BookStack
        php artisan bookstack:export-dokuwiki \
            --output-path="$OUTPUT_DIR"
        ;;
    
    "perl")
        perl "$(dirname "$0")/bookstack2dokuwiki.pl" \
            --db-host="$DB_HOST" \
            --db-name="$DB_NAME" \
            --db-user="$DB_USER" \
            --db-pass="$DB_PASS" \
            --output="$OUTPUT_DIR" \
            --verbose
        ;;
    
    "java")
        java -jar "$JAR_PATH" \
            --db-host "$DB_HOST" \
            --db-name "$DB_NAME" \
            --db-user "$DB_USER" \
            --db-pass "$DB_PASS" \
            --output "$OUTPUT_DIR" \
            --verbose
        ;;
    
    "c")
        "$BINARY_PATH" \
            --db-host "$DB_HOST" \
            --db-name "$DB_NAME" \
            --db-user "$DB_USER" \
            --db-pass "$DB_PASS" \
            --output "$OUTPUT_DIR" \
            --verbose
        ;;
esac

# Check if it succeeded
if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}${BOLD}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}${BOLD}║                                                                ║${NC}"
    echo -e "${GREEN}${BOLD}║               🎉 SUCCESS! 🎉                                    ║${NC}"
    echo -e "${GREEN}${BOLD}║                                                                ║${NC}"
    echo -e "${GREEN}${BOLD}║        Your migration completed successfully!                   ║${NC}"
    echo -e "${GREEN}${BOLD}║                                                                ║${NC}"
    echo -e "${GREEN}${BOLD}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}📦 Your files are here: ${BOLD}$OUTPUT_DIR${NC}"
    echo ""
    echo -e "${YELLOW}📋 What to do next:${NC}"
    echo ""
    echo -e "  ${MAGENTA}1️⃣${NC}  Copy the files to your DokuWiki:"
    echo -e "     ${CYAN}cp -r $OUTPUT_DIR/data/pages/* /var/www/dokuwiki/data/pages/${NC}"
    echo ""
    echo -e "  ${MAGENTA}2️⃣${NC}  Fix the file permissions:"
    echo -e "     ${CYAN}chown -R www-data:www-data /var/www/dokuwiki/data/${NC}"
    echo ""
    echo -e "  ${MAGENTA}3️⃣${NC}  Rebuild the DokuWiki search index:"
    echo -e "     ${CYAN}Visit: http://your-wiki.com/doku.php?do=index${NC}"
    echo ""
    echo -e "  ${MAGENTA}4️⃣${NC}  Test it out and make sure everything looks good!"
    echo ""
    echo -e "${GREEN}🎊 Congratulations! You did it! 🎊${NC}"
    echo ""
    echo -e "${YELLOW}💡 Pro tip: Keep a backup of your BookStack data just in case!${NC}"
    echo ""
else
    echo ""
    echo -e "${RED}${BOLD}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${RED}${BOLD}║                                                                ║${NC}"
    echo -e "${RED}${BOLD}║               ⚠️  OOPS! Something Went Wrong! ⚠️                ║${NC}"
    echo -e "${RED}${BOLD}║                                                                ║${NC}"
    echo -e "${RED}${BOLD}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}Don't panic! Here's how to fix common problems:${NC}"
    echo ""
    echo -e "${CYAN}🔍 Common Issues:${NC}"
    echo ""
    echo -e "${BOLD}Can't connect to database?${NC}"
    echo -e "  • Double-check your username and password"
    echo -e "  • Make sure MySQL is running: ${CYAN}sudo systemctl status mysql${NC}"
    echo -e "  • Check if the database exists: ${CYAN}mysql -u$DB_USER -p -e 'SHOW DATABASES;'${NC}"
    echo ""
    echo -e "${BOLD}Permission errors?${NC}"
    echo -e "  • Make sure you can write to: $OUTPUT_DIR"
    echo -e "  • Try: ${CYAN}mkdir -p $OUTPUT_DIR && chmod 777 $OUTPUT_DIR${NC}"
    echo ""
    echo -e "${BOLD}Still stuck?${NC}"
    echo -e "  • Read the full docs: ${CYAN}less $(dirname "$0")/../MIGRATION_TOOLS.md${NC}"
    echo -e "  • Check the error messages above - they usually tell you what's wrong!"
    echo ""
    echo -e "${YELLOW}💪 Don't give up! You can do this!${NC}"
    echo ""
    exit 1
fi
