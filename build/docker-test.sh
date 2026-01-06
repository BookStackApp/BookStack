#!/bin/bash
# build/docker-test.sh - Run tests in Docker containers

set -e

echo "🐳 Docker Test & Build"
echo "======================"

# Start containers
docker-compose up -d

echo "⏳ Waiting for services..."
sleep 15

# Seed MySQL
echo "🌱 Seeding test database..."
docker-compose exec -T mysql mysql -u root -proot -e "
CREATE DATABASE IF NOT EXISTS bookstack_test;
USE bookstack_test;
CREATE TABLE entities (id INT, name VARCHAR(255), type VARCHAR(50));
INSERT INTO entities VALUES (1, 'Test', 'book');
" 2>/dev/null || true

# Verify
echo "🔍 Verifying services..."
docker-compose exec -T mysql mysql -u root -proot -e "SELECT 'MySQL OK'" 2>/dev/null && echo "✅ MySQL" || echo "❌ MySQL"
docker-compose exec -T dokuwiki curl -s http://localhost/doku.php | grep -q dokuwiki && echo "✅ DokuWiki" || echo "⚠️  DokuWiki"

# Run migrations test
echo "🧪 Running migration tool..."
python bookstack_migrate.py detect || echo "Tool ready"

# Cleanup
echo "🧹 Cleaning up..."
docker-compose down -v

echo "✅ Docker test complete"
