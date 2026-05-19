#!/bin/bash

# Server Manager - Local to Server Sync Script
# Usage: bash sync-to-server.sh [server_ip] [rebuild]
# Example: bash sync-to-server.sh 167.99.13.48
#          bash sync-to-server.sh 167.99.13.48 rebuild

SERVER_IP="${1:-167.99.13.48}"
REBUILD="${2:-}"
DEPLOY_PATH="/opt/server-manager"

echo "Syncing local changes to $SERVER_IP..."
echo ""

# Verify SSH connection
if ! ssh -o ConnectTimeout=5 root@$SERVER_IP "echo 'Connected'" > /dev/null 2>&1; then
    echo "Cannot connect to $SERVER_IP"
    exit 1
fi

echo "Syncing backend code..."
rsync -avz --delete \
  --exclude='.env' \
  --exclude='storage/logs/*' \
  --exclude='bootstrap/cache/*' \
  --exclude='vendor/*' \
  --exclude='node_modules/*' \
  backend/ root@$SERVER_IP:$DEPLOY_PATH/backend/

echo ""
echo "Syncing frontend code..."
rsync -avz --delete \
  --exclude='dist/*' \
  --exclude='node_modules/*' \
  --exclude='.env' \
  frontend/ root@$SERVER_IP:$DEPLOY_PATH/frontend/

echo ""
echo "Syncing docker configurations..."
rsync -avz \
  docker-compose.prod.yml \
  docker/ \
  root@$SERVER_IP:$DEPLOY_PATH/

echo ""
echo "Files synced successfully"
echo ""

# If rebuild flag is set
if [ "$REBUILD" == "rebuild" ]; then
    echo "Rebuilding application on server..."
    ssh root@$SERVER_IP "cd $DEPLOY_PATH && \
        echo 'Rebuilding containers...' && \
        docker-compose -f docker-compose.prod.yml build --no-cache && \
        echo 'Restarting services...' && \
        docker-compose -f docker-compose.prod.yml up -d && \
        echo 'Running migrations...' && \
        docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate && \
        echo 'Rebuild complete!'"
    
    echo ""
    echo "Testing application..."
    RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://$SERVER_IP/)
    if [ "$RESPONSE" == "200" ]; then
        echo "Application is responding (HTTP 200)"
    else
        echo "Application returned HTTP $RESPONSE"
    fi
else
    echo "To rebuild containers, run:"
    echo "   bash sync-to-server.sh $SERVER_IP rebuild"
    echo ""
    echo "Or manually on server:"
    echo "   cd $DEPLOY_PATH"
    echo "   docker-compose -f docker-compose.prod.yml up -d --build"
    echo "   docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate"
fi

echo ""
echo "Sync complete!"
