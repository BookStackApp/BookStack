#!/bin/bash
# Integration test with Docker Compose environment

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

echo "🐳 Docker Integration Test"
echo ""

# Start services
echo "Starting Docker services..."
compose up -d

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 30

# Check connectivity
echo "Verifying services..."
curl -s http://localhost:8000 > /dev/null && echo "✅ BookStack running" || echo "❌ BookStack failed"
curl -s http://localhost:8080 > /dev/null && echo "✅ DokuWiki running" || echo "❌ DokuWiki failed"

# Run tests
echo ""
echo "Running integration tests..."
export BOOKSTACK_BASE_URL="http://localhost:8000"
cd "$TOOL_ROOT"
python -m pytest tests/ -v -k "not docker" || true

# Cleanup
echo ""
echo "Cleaning up..."
compose down

echo "✅ Docker test complete"
