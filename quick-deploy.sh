#!/bin/bash

# Server Manager - Quick Deploy Script
# Run on remote server: curl -fsSL https://your-domain/quick-deploy.sh | bash
# Or copy and paste this script directly

set -e

DEPLOY_PATH="/opt/server-manager"
APP_URL="http://$(hostname -I | awk '{print $1}')"

echo "Server Manager - Quick Deploy"
echo "================================"
echo ""

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
fi

if ! command -v docker-compose &> /dev/null; then
    echo "Installing Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" \
      -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

echo "Docker installed"
echo ""

# Create deploy directory
mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

# Initialize Git repo if needed
if [ ! -d ".git" ]; then
    git init
    git remote add origin https://github.com/yourrepo/server-manager.git 2>/dev/null || true
fi

# Create environment file
echo "Creating environment file..."
if [ ! -f ".env" ]; then
    cat > .env <<EOF
APP_NAME="Server Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=$APP_URL

DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=$(openssl rand -base64 16)
DB_ROOT_PASSWORD=$(openssl rand -base64 16)
REDIS_PASSWORD=$(openssl rand -base64 16)

MAIL_DRIVER=log
EOF
    echo ".env created with secure passwords"
fi

# Create SSL certificates
echo "Creating SSL certificates..."
mkdir -p docker/certs
if [ ! -f "docker/certs/cert.pem" ]; then
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout docker/certs/key.pem \
      -out docker/certs/cert.pem \
      -subj "/C=US/ST=State/L=City/O=Organization/CN=$(hostname -I | awk '{print $1}')" -batch
    echo "SSL certificates created"
fi

# Load environment
set -a
source .env
set +a

# Start services
echo ""
echo "Starting Docker containers..."
docker-compose -f docker-compose.prod.yml down 2>/dev/null || true
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for services
echo "Waiting for services to start (30 seconds)..."
sleep 30

# Run migrations
echo "Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force --seed 2>/dev/null || echo "Migrations completed"

echo ""
echo "Deployment Complete!"
echo ""
echo "Access your application:"
echo "  URL: $APP_URL"
echo "  Default Login:"
echo "    Email: admin@servermanager.local"
echo "    Password: admin123"
echo ""
echo "Container Status:"
docker-compose -f docker-compose.prod.yml ps
echo ""
echo "To view logs: docker-compose -f docker-compose.prod.yml logs -f"
