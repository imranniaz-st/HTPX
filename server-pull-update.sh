#!/bin/bash

# Server - Pull and Update Script
# Run this on the server to pull latest code and rebuild
# Usage: bash pull-and-update.sh

DEPLOY_PATH="/opt/server-manager"

echo "Pulling latest changes..."
echo ""

cd $DEPLOY_PATH || exit 1

echo "Pulling from Git repository..."
git pull origin main 2>/dev/null || echo "Git pull skipped (not a git repo)"

echo ""
echo "🏗️  Building Docker containers..."
docker-compose -f docker-compose.prod.yml build --no-cache

echo ""
echo "Starting services..."
docker-compose -f docker-compose.prod.yml up -d

echo ""
echo "⏳ Waiting for services to start..."
sleep 10

echo ""
echo "📦 Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate

echo ""
echo "🧹 Clearing caches..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:clear

echo ""
echo "✅ Update complete!"
echo ""
echo "Checking application status..."
docker-compose -f docker-compose.prod.yml ps
echo ""
echo "Testing HTTP..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/ 2>/dev/null)
echo "HTTP Status: $HTTP_STATUS"
echo ""

if [ "$HTTP_STATUS" == "200" ]; then
    echo "✅ Application is running!"
else
    echo "⚠️  Application may have issues (HTTP $HTTP_STATUS)"
    echo ""
    echo "View logs with:"
    echo "  docker-compose -f docker-compose.prod.yml logs -f"
fi
