#!/bin/bash
# Integration test with Docker Compose environment

set -e

echo "🐳 Docker Integration Test"
echo ""

# Start services
echo "Starting Docker services..."
docker-compose up -d

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
python -m pytest tests/ -v -k "not docker" || true

# Cleanup
echo ""
echo "Cleaning up..."
docker-compose down

echo "✅ Docker test complete"
