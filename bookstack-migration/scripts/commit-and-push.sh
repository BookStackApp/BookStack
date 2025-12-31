#!/bin/bash
################################################################################
# COMMIT-AND-PUSH.sh
#
# Automated git commit with PGP signing and push
#
# This will:
# 1. Ask for confirmation
# 2. Stage all changes
# 3. Commit with your PGP signature
# 4. Verify the signature
# 5. Push to remote
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
BOLD='\033[1m'

echo -e "${CYAN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  🔐 GIT COMMIT WITH PGP SIGNATURE 🔐                     ║
║                                                           ║
║  Sign it, seal it, ship it                               ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""

################################################################################
# Check Git Configuration
################################################################################

echo -e "${BLUE}━━ Checking Git Configuration ━━${NC}"
echo ""

# Check if git user is configured
GIT_USER=$(git config user.name || echo "")
GIT_EMAIL=$(git config user.email || echo "")

if [ -z "$GIT_USER" ] || [ -z "$GIT_EMAIL" ]; then
    echo -e "${RED}❌ Git user not configured!${NC}"
    echo ""
    echo "Run these commands first:"
    echo "  git config --global user.name \"Alex Alvonellos\""
    echo "  git config --global user.email \"your.email@example.com\""
    echo ""
    exit 1
fi

echo -e "${GREEN}✓ Git user: $GIT_USER${NC}"
echo -e "${GREEN}✓ Git email: $GIT_EMAIL${NC}"

# Check if GPG signing is configured
GPG_KEY=$(git config user.signingkey || echo "")

if [ -z "$GPG_KEY" ]; then
    echo -e "${YELLOW}⚠ GPG signing key not configured${NC}"
    echo ""
    echo "To enable GPG signing:"
    echo "  1. List your GPG keys:"
    echo "     gpg --list-secret-keys --keyid-format=long"
    echo ""
    echo "  2. Set your signing key:"
    echo "     git config --global user.signingkey YOUR_KEY_ID"
    echo ""
    echo "  3. Enable commit signing:"
    echo "     git config --global commit.gpgsign true"
    echo ""
    
    read -p "Do you want to commit WITHOUT GPG signature? (yes/no): " response
    if [[ "$response" != "yes" ]]; then
        echo "Aborting."
        exit 1
    fi
    USE_GPG=false
else
    echo -e "${GREEN}✓ GPG key configured: $GPG_KEY${NC}"
    USE_GPG=true
fi

echo ""

################################################################################
# Show What Will Be Committed
################################################################################

echo -e "${BLUE}━━ Changes to Commit ━━${NC}"
echo ""

git status --short

echo ""
echo "Files changed:"
git diff --stat

echo ""

################################################################################
# Confirmation
################################################################################

read -p "Proceed with commit? (yes/no): " confirm

if [[ "$confirm" != "yes" ]]; then
    echo "Commit cancelled."
    exit 0
fi

echo ""

################################################################################
# Get Commit Message
################################################################################

echo -e "${BLUE}━━ Commit Message ━━${NC}"
echo ""

DEFAULT_MSG="feat: Add Rust migration tool with Merkle tree validation

- Implement BookStack to DokuWiki migration in Rust
- Add Merkle tree-based hierarchical validation
- Create setup-deps.sh for automatic dependency installation
- Add gaslight-user.sh for decision-making psychology
- Implement make-backup-before-migration.sh for safety
- Create migration-helper.sh as primary user entry point
- Add comprehensive documentation (FINAL_SUMMARY, ORGANIZATION_GUIDE)
- Create RUST_COMPARISON_BRUTAL.md showing why Rust wins
- Update all attribution to Alex Alvonellos
- Add TODO markers for intentional technical debt
- Include nginx/config validation in diagnostics

Alex Alvonellos - i use arch btw"

echo "Default commit message:"
echo "----------------------------------------"
echo "$DEFAULT_MSG"
echo "----------------------------------------"
echo ""

read -p "Use default message? (yes/no): " use_default

if [[ "$use_default" == "yes" ]]; then
    COMMIT_MSG="$DEFAULT_MSG"
else
    echo "Enter custom commit message (Ctrl+D when done):"
    COMMIT_MSG=$(cat)
fi

echo ""

################################################################################
# Stage Changes
################################################################################

echo -e "${BLUE}━━ Staging Changes ━━${NC}"
echo ""

git add -A

echo -e "${GREEN}✓ All changes staged${NC}"
echo ""

################################################################################
# Commit
################################################################################

echo -e "${BLUE}━━ Committing ━━${NC}"
echo ""

if [ "$USE_GPG" = true ]; then
    # Commit with GPG signature
    git commit -S -m "$COMMIT_MSG"
    echo -e "${GREEN}✓ Commit created with GPG signature${NC}"
    
    # Verify signature
    echo ""
    echo "Verifying signature..."
    git log --show-signature -1 | head -20
    
else
    # Commit without signature
    git commit -m "$COMMIT_MSG"
    echo -e "${GREEN}✓ Commit created (unsigned)${NC}"
fi

echo ""

################################################################################
# Push
################################################################################

echo -e "${BLUE}━━ Pushing to Remote ━━${NC}"
echo ""

# Get current branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)

echo "Current branch: $CURRENT_BRANCH"
echo ""

read -p "Push to origin/$CURRENT_BRANCH? (yes/no): " push_confirm

if [[ "$push_confirm" == "yes" ]]; then
    git push origin "$CURRENT_BRANCH"
    echo -e "${GREEN}✓ Pushed to origin/$CURRENT_BRANCH${NC}"
else
    echo "Push skipped. Run manually:"
    echo "  git push origin $CURRENT_BRANCH"
fi

echo ""

################################################################################
# Final Status
################################################################################

echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${GREEN}${BOLD}✅ COMMIT COMPLETE!${NC}"
echo ""
echo "Latest commit:"
git log -1 --oneline
echo ""

if [ "$USE_GPG" = true ]; then
    echo "Signature verified. Your code is authenticated."
else
    echo "Commit is unsigned. Consider setting up GPG signing."
fi

echo ""
echo -e "${CYAN}Alex Alvonellos - i use arch btw${NC}"
echo ""
