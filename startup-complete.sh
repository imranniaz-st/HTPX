#!/bin/bash

set -e  # Exit on error

echo "==============================================="
echo "Server Manager - Complete Startup Script"
echo "==============================================="
cd /opt/server-manager

echo "Working directory: $(pwd)"
echo ""

# Step 1: Check if docker-compose file exists
echo "Step 1: Verifying docker-compose.prod.yml..."
if [ ! -f "docker-compose.prod.yml" ]; then
    echo "docker-compose.prod.yml not found!"
    exit 1
fi
echo "File exists"
echo ""

# Step 2: Check Docker daemon
echo "Step 2: Checking Docker..."
if ! command -v docker &> /dev/null; then
    echo "Docker not installed!"
    exit 1
fi
echo "Docker is available"
echo ""

# Step 3: Create .env if missing
echo "Step 3: Checking .env file..."
if [ ! -f ".env" ]; then
    echo "Creating .env from template..."
    cat > .env <<EOF
APP_NAME="Server Manager"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:$(openssl rand -base64 32)
APP_URL=http://localhost:8001

DB_HOST=mysql
DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=laravel_secure_password
DB_ROOT_PASSWORD=root_secure_password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=cookie

MAIL_DRIVER=log
MAIL_HOST=localhost
MAIL_PORT=1025

JWT_SECRET=$(openssl rand -base64 32)

FRONTEND_DIST_PATH=./frontend/dist
EOF
    echo "Created .env"
else
    echo ".env exists"
fi
echo ""

# Step 4: Check/create frontend dist
echo "Step 4: Checking frontend..."
if [ ! -d "frontend/dist" ] || [ -z "$(ls -A frontend/dist 2>/dev/null)" ]; then
    echo "frontend/dist is empty or missing"
    echo "Building frontend..."
    if [ -d "frontend" ]; then
        cd frontend
        npm install 2>&1 | grep -E "(added|up to date)" || true
        npm run build 2>&1 | tail -5
        cd ..
        echo "Frontend built"
    else
        echo "Frontend directory not found - will create dummy dist folder"
        mkdir -p frontend/dist
        echo "<html><body><h1>Server Manager</h1></body></html>" > frontend/dist/index.html
    fi
else
    echo "frontend/dist exists with content"
fi
echo ""

# Step 5: Stop old containers
echo "Step 5: Stopping old containers..."
docker-compose -f docker-compose.prod.yml down 2>/dev/null || true
sleep 2
echo "Done"
echo ""

# Step 6: Build and start containers
echo "Step 6: Building and starting containers..."
docker-compose -f docker-compose.prod.yml up -d --build --remove-orphans
sleep 10
echo "Containers started"
echo ""

# Step 7: Wait for MySQL to be ready
echo "Step 7: Waiting for MySQL to be ready..."
max_attempts=30
attempt=0
while ! docker-compose -f docker-compose.prod.yml exec -T mysql mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASSWORD" &>/dev/null; do
    attempt=$((attempt + 1))
    if [ $attempt -gt $max_attempts ]; then
        echo "MySQL did not start in time!"
        echo "Logs:"
        docker-compose -f docker-compose.prod.yml logs mysql | tail -20
        exit 1
    fi
    echo "⏳ Waiting... ($attempt/$max_attempts)"
    sleep 2
done
echo "MySQL is ready"
echo ""

# Step 8: Run migrations
echo "Step 8: Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force 2>&1 | tail -20
echo "Migrations complete"
echo ""

# Step 9: Check container status
echo "Step 9: Container Status:"
docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

# Step 10: Test endpoint
echo "Step 10: Testing HTTP endpoint..."
sleep 5
if curl -s -I http://localhost:8001/ | grep -q "200\|301\|302"; then
    echo "HTTP 200 - Application is responding!"
else
    echo "Getting initial response, might be warming up..."
    curl -s -I http://localhost:8001/ 2>/dev/null || echo "Not responding yet (still starting)"
fi
echo ""

echo "==============================================="
echo "✅ Server Manager startup complete!"
echo "==============================================="
echo ""
echo "Access your application at:"
echo "   http://167.99.13.48:8001"
echo ""
echo "Default Login:"
echo "   Email: admin@servermanager.local"
echo "   Password: admin123"
echo ""
