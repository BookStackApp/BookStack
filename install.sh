#!/bin/bash
# BookStack Migration Tool - One-step install script
# Usage: bash install.sh
# Or:    curl https://raw.githubusercontent.com/alvonellos/BookStack/feature/standalone/install.sh | bash

set -e

VERSION="1.0.0"
INSTALL_DIR="${INSTALL_DIR:-/usr/local/bin}"
GITHUB_URL="https://github.com/alvonellos/BookStack"
RELEASE_URL="$GITHUB_URL/releases/download/v$VERSION"

SUDO=""

need_root_for_install() {
    [ ! -w "$INSTALL_DIR" ]
}

ensure_sudo_noninteractive() {
    if ! command -v sudo >/dev/null 2>&1; then
        echo "❌ No write permission to $INSTALL_DIR and sudo is not installed."
        exit 1
    fi

    # Require sudo to work without prompting (for automation/curl|bash flows)
    if ! sudo -n true >/dev/null 2>&1; then
        echo "❌ No write permission to $INSTALL_DIR and sudo requires a password prompt."
        echo "   Re-run in an interactive shell and run: sudo bash install.sh"
        exit 1
    fi

    SUDO="sudo -n"
}

echo "📦 BookStack Migration Tool Installer"
echo "Version: $VERSION"
echo ""

# Detect OS
OS=$(uname -s)
ARCH=$(uname -m)

case "$OS" in
    Linux)
        if [ "$ARCH" = "x86_64" ]; then
            BINARY="bookstack-migrate-linux"
        else
            echo "❌ Unsupported architecture: $ARCH"
            exit 1
        fi
        ;;
    Darwin)
        if [ "$ARCH" = "arm64" ]; then
            BINARY="bookstack-migrate-macos-arm64"
        elif [ "$ARCH" = "x86_64" ]; then
            BINARY="bookstack-migrate-macos"
        else
            echo "❌ Unsupported architecture: $ARCH"
            exit 1
        fi
        ;;
    *)
        echo "❌ Unsupported OS: $OS"
        echo "Please install manually from source:"
        echo "  pip install bookstack-migrate"
        exit 1
        ;;
esac

# Check for write permission (auto-escalate only if sudo works immediately)
if need_root_for_install; then
    echo "⚠️  No write permission to $INSTALL_DIR"
    ensure_sudo_noninteractive
    echo "✅ Using sudo for install"
fi

# Download binary
echo "⬇️  Downloading $BINARY..."
TEMP_FILE=$(mktemp)
if command -v curl &> /dev/null; then
    curl -sL "$RELEASE_URL/$BINARY" -o "$TEMP_FILE"
elif command -v wget &> /dev/null; then
    wget -q "$RELEASE_URL/$BINARY" -O "$TEMP_FILE"
else
    echo "❌ Neither curl nor wget found. Please install one."
    exit 1
fi

# Verify download
if [ ! -s "$TEMP_FILE" ]; then
    echo "❌ Download failed"
    rm -f "$TEMP_FILE"
    exit 1
fi

# Install
echo "📥 Installing to $INSTALL_DIR/$BINARY..."
$SUDO mv "$TEMP_FILE" "$INSTALL_DIR/$BINARY"

# Ensure executable permissions explicitly
$SUDO chmod 0755 "$INSTALL_DIR/$BINARY"

# Create symlink
if [ ! -L "$INSTALL_DIR/bookstack-migrate" ]; then
    $SUDO ln -s "$INSTALL_DIR/$BINARY" "$INSTALL_DIR/bookstack-migrate"
fi

echo ""
echo "✅ Installation complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Set API credentials:"
echo "      export BOOKSTACK_TOKEN_ID=\"your_token_id\""
echo "      export BOOKSTACK_TOKEN_SECRET=\"your_token_secret\""
echo ""
echo "   2. Run a command:"
echo "      bookstack-migrate detect"
echo "      bookstack-migrate version"
echo ""
echo "📚 Full documentation: $GITHUB_URL"
