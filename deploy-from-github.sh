#!/bin/bash

echo "Cloning Server Manager from GitHub..."
cd /opt

# Remove old directory if exists
rm -rf server-manager

# Clone the repo
git clone https://github.com/imranniaz-st/HTPX server-manager

# Navigate to ServerManager folder
cd server-manager/ServerManager

echo ""
echo "Repository cloned successfully!"
echo "Location: $(pwd)"
echo ""
echo "Contents:"
ls -la

echo ""
echo "Stopping old containers..."
docker-compose -f docker-compose.prod.yml down 2>/dev/null || echo "No previous containers"

echo ""
echo "Building and starting new deployment from GitHub..."
docker-compose -f docker-compose.prod.yml up -d --build

echo ""
echo "⏳ Waiting 30 seconds for services to start..."
sleep 30

echo ""
echo "Container Status:"
docker-compose -f docker-compose.prod.yml ps

echo ""
echo "✨ Deployment complete!"
echo "Access your application at: http://167.99.13.48:8001"
echo "Login: admin@servermanager.local / admin123"
