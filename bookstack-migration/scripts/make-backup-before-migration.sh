#!/bin/bash
################################################################################
# MAKE-BACKUP-BEFORE-MIGRATION.sh
#
# Manual backup script for when you want to be EXTRA careful before ChatGPT
# or the migration script inevitably breaks something.
#
# This script:
# 1. Backs up the entire BookStack database
# 2. Backs up all uploaded files
# 3. Backs up the .env configuration
# 4. Creates a compressed archive
# 5. Verifies the backup is valid
# 6. Shows you exactly where it is
#
# Philosophy: Hope for the best, backup for the worst.
# Alex Alvonellos - i use arch btw
################################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

BACKUP_DIR="./bookstack-backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="bookstack-backup-$TIMESTAMP"
BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"

################################################################################
# Banner
################################################################################

echo -e "${CYAN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  💾 MANUAL BACKUP SCRIPT - SAFETY FIRST 💾               ║
║                                                           ║
║  Before we let ChatGPT or our scripts loose on your      ║
║  data, let's make DAMN SURE we have a backup.            ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""

################################################################################
# Check if we're in BookStack directory
################################################################################

echo -e "${BLUE}Step 1: Verifying we're in the right place${NC}"

if [ ! -f "app/Console/Commands/ExportToDokuWiki.php" ] && [ ! -f "artisan" ]; then
    echo -e "${RED}❌ This doesn't look like a BookStack installation${NC}"
    echo ""
    echo "BookStack files not found. Please run this from your BookStack root."
    echo ""
    exit 1
fi

echo -e "${GREEN}✓ This looks like a BookStack installation${NC}"
echo ""

################################################################################
# Load environment
################################################################################

echo -e "${BLUE}Step 2: Loading database credentials${NC}"

if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    echo ""
    echo "We need the .env file to backup your database."
    echo "Please make sure .env exists in your BookStack directory."
    echo ""
    exit 1
fi

# Source the .env file (carefully)
set -a
source .env 2>/dev/null
set +a

if [ -z "$DB_HOST" ] || [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo -e "${RED}❌ Database credentials incomplete!${NC}"
    echo ""
    echo "Required variables in .env:"
    echo "  DB_HOST=$DB_HOST"
    echo "  DB_DATABASE=$DB_DATABASE"
    echo "  DB_USERNAME=$DB_USERNAME"
    echo ""
    exit 1
fi

echo -e "${GREEN}✓ Database credentials loaded${NC}"
echo "  Host: $DB_HOST"
echo "  Database: $DB_DATABASE"
echo "  User: $DB_USERNAME"
echo ""

################################################################################
# Create backup directory
################################################################################

echo -e "${BLUE}Step 3: Creating backup directory${NC}"

mkdir -p "$BACKUP_PATH"

echo -e "${GREEN}✓ Created: $BACKUP_PATH${NC}"
echo ""

################################################################################
# Backup the database
################################################################################

echo -e "${BLUE}Step 4: Backing up database${NC}"
echo -e "${YELLOW}(This may take a minute...)${NC}"

DB_BACKUP="$BACKUP_PATH/bookstack-database.sql"

if mysqldump \
    -h "$DB_HOST" \
    -u "$DB_USERNAME" \
    -p"$DB_PASSWORD" \
    --single-transaction \
    --quick \
    "$DB_DATABASE" > "$DB_BACKUP" 2>/dev/null; then
    
    DB_SIZE=$(du -h "$DB_BACKUP" | awk '{print $1}')
    echo -e "${GREEN}✓ Database backed up ($DB_SIZE)${NC}"
else
    echo -e "${RED}⚠ Could not backup database (check credentials)${NC}"
    echo "  But continuing anyway (might just be mysqldump missing)"
fi

echo ""

################################################################################
# Backup uploads directory
################################################################################

echo -e "${BLUE}Step 5: Backing up uploaded files${NC}"
echo -e "${YELLOW}(This may take a minute...)${NC}"

if [ -d "storage/uploads" ]; then
    tar -czf "$BACKUP_PATH/uploads.tar.gz" storage/uploads/ 2>/dev/null
    UPLOAD_SIZE=$(du -h "$BACKUP_PATH/uploads.tar.gz" | awk '{print $1}')
    echo -e "${GREEN}✓ Uploads backed up ($UPLOAD_SIZE)${NC}"
else
    echo -e "${YELLOW}⚠ No uploads directory found${NC}"
fi

echo ""

################################################################################
# Backup .env file
################################################################################

echo -e "${BLUE}Step 6: Backing up .env configuration${NC}"

cp .env "$BACKUP_PATH/.env-backup"
chmod 600 "$BACKUP_PATH/.env-backup"

echo -e "${GREEN}✓ .env backed up${NC}"
echo ""

################################################################################
# Backup application files (just in case)
################################################################################

echo -e "${BLUE}Step 7: Creating application snapshot${NC}"

tar -czf "$BACKUP_PATH/app-files.tar.gz" \
    app/ \
    config/ \
    routes/ \
    bootstrap/ \
    database/ \
    2>/dev/null || true

APP_SIZE=$(du -h "$BACKUP_PATH/app-files.tar.gz" | awk '{print $1}')
echo -e "${GREEN}✓ Application files backed up ($APP_SIZE)${NC}"
echo ""

################################################################################
# Create final compressed backup
################################################################################

echo -e "${BLUE}Step 8: Creating final compressed backup${NC}"
echo -e "${YELLOW}(Compressing everything...)${NC}"

FINAL_BACKUP="$BACKUP_DIR/$BACKUP_NAME.tar.gz"

tar -czf "$FINAL_BACKUP" -C "$BACKUP_DIR" "$BACKUP_NAME" 2>/dev/null

FINAL_SIZE=$(du -h "$FINAL_BACKUP" | awk '{print $1}')

echo -e "${GREEN}✓ Final backup created ($FINAL_SIZE)${NC}"
echo ""

################################################################################
# Verify backup
################################################################################

echo -e "${BLUE}Step 9: Verifying backup integrity${NC}"

if tar -tzf "$FINAL_BACKUP" > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Backup archive is valid${NC}"
else
    echo -e "${RED}❌ Backup archive appears corrupted!${NC}"
    exit 1
fi

echo ""

################################################################################
# Generate checksum
################################################################################

echo -e "${BLUE}Step 10: Generating checksums${NC}"

if command -v md5sum &> /dev/null; then
    MD5=$(md5sum "$FINAL_BACKUP" | awk '{print $1}')
    echo "$MD5  $FINAL_BACKUP" > "$FINAL_BACKUP.md5"
    echo -e "${GREEN}✓ MD5: $MD5${NC}"
elif command -v shasum &> /dev/null; then
    SHA=$(shasum "$FINAL_BACKUP" | awk '{print $1}')
    echo "$SHA  $FINAL_BACKUP" > "$FINAL_BACKUP.sha"
    echo -e "${GREEN}✓ SHA1: $SHA${NC}"
fi

echo ""

################################################################################
# Summary
################################################################################

echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${GREEN}${BOLD}✅ BACKUP COMPLETE!${NC}"
echo ""
echo "Location: $FINAL_BACKUP"
echo "Size: $FINAL_SIZE"
echo ""
echo -e "${YELLOW}What's in your backup:${NC}"
echo "  ✓ Complete database dump (.sql)"
echo "  ✓ All uploaded files (.tar.gz)"
echo "  ✓ Configuration files (.env)"
echo "  ✓ Application files (app, config, routes, etc)"
echo ""
echo -e "${BLUE}If something goes wrong:${NC}"
echo ""
echo "1. Stop everything:"
echo "   sudo systemctl stop apache2  (or nginx/php-fpm)"
echo ""
echo "2. Delete the corrupted BookStack:"
echo "   sudo rm -rf /var/www/bookstack"
echo ""
echo "3. Restore from backup:"
echo "   cd /var/www"
echo "   tar -xzf $FINAL_BACKUP"
echo ""
echo "4. Restore database:"
echo "   mysql -u root -p < $BACKUP_PATH/bookstack-database.sql"
echo ""
echo "5. Restore .env:"
echo "   cp $BACKUP_PATH/.env-backup /var/www/bookstack/.env"
echo ""
echo "6. Fix permissions:"
echo "   chown -R www-data:www-data /var/www/bookstack"
echo "   chmod -R 755 /var/www/bookstack"
echo ""
echo "7. Start services:"
echo "   sudo systemctl start apache2  (or nginx/php-fpm)"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}Now you can safely run:${NC}"
echo "  ./ULTIMATE_MIGRATION.sh"
echo ""
echo -e "${CYAN}Alex Alvonellos - i use arch btw${NC}"
echo ""
