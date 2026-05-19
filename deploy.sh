#!/bin/bash
#hirepentester
################################################################################
# Server Manager - Production Deployment Script
# Deploy to server: bash deploy.sh 167.99.13.48
################################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOY_USER="root"
DEPLOY_HOST="${1:-167.99.13.48}"
DEPLOY_PATH="/opt/server-manager"
REPO_URL="$(git config --get remote.origin.url)"
BRANCH="${2:-main}"

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}Server Manager - Deployment${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Validate inputs
if [ -z "$DEPLOY_HOST" ]; then
    echo -e "${RED}❌ Usage: bash deploy.sh <host> [branch]${NC}"
    echo "Example: bash deploy.sh 167.99.13.48 main"
    exit 1
fi

echo -e "${YELLOW}Deploy Configuration:${NC}"
echo "  Host: $DEPLOY_HOST"
echo "  User: $DEPLOY_USER"
echo "  Path: $DEPLOY_PATH"
echo "  Branch: $BRANCH"
echo "  Repository: $REPO_URL"
echo ""

# Test connection
echo -e "${YELLOW}Testing SSH connection...${NC}"
if ! ssh -o ConnectTimeout=5 "$DEPLOY_USER@$DEPLOY_HOST" "echo 'SSH OK'"; then
    echo -e "${RED}❌ Cannot connect to $DEPLOY_HOST${NC}"
    exit 1
fi
echo -e "${GREEN}✓ SSH connection OK${NC}"
echo ""

# Build frontend
echo -e "${YELLOW}Building frontend...${NC}"
cd frontend
npm install --silent
npm run build
cd ..
echo -e "${GREEN}✓ Frontend built${NC}"
echo ""

# Deploy script to run on server
REMOTE_SCRIPT=$(cat <<'REMOTE_SCRIPT_END'
#!/bin/bash
set -e
DEPLOY_PATH="/opt/server-manager"
DEPLOY_BRANCH="$1"

echo "📦 Deploying to $DEPLOY_PATH..."

# Create deployment directory if not exists
if [ ! -d "$DEPLOY_PATH" ]; then
    echo "Creating deployment directory..."
    mkdir -p "$DEPLOY_PATH"
    cd "$DEPLOY_PATH"
    git init
    git remote add origin "$2"
    git fetch origin "$DEPLOY_BRANCH"
    git checkout "$DEPLOY_BRANCH"
else
    cd "$DEPLOY_PATH"
    git fetch origin
    git checkout "$DEPLOY_BRANCH"
    git pull origin "$DEPLOY_BRANCH"
fi

echo "✓ Code updated"

# Copy environment file if not exists
if [ ! -f "$DEPLOY_PATH/.env" ]; then
    echo "Creating .env file..."
    cp "$DEPLOY_PATH/.env.example" "$DEPLOY_PATH/.env" || true
    cat > "$DEPLOY_PATH/.env" <<EOF
APP_NAME="Server Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://$(hostname -I | awk '{print $1}')

DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=$(openssl rand -base64 16)
DB_ROOT_PASSWORD=$(openssl rand -base64 16)

REDIS_PASSWORD=$(openssl rand -base64 16)

MAIL_DRIVER=log
EOF
    echo "✓ .env file created with secure passwords"
fi

# Create SSL certificates if not exists
CERTS_DIR="$DEPLOY_PATH/docker/certs"
if [ ! -f "$CERTS_DIR/cert.pem" ]; then
    echo "Generating SSL certificates..."
    mkdir -p "$CERTS_DIR"
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout "$CERTS_DIR/key.pem" \
        -out "$CERTS_DIR/cert.pem" \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$(hostname -I | awk '{print $1}')"
    echo "✓ SSL certificates created"
fi

# Load environment variables
set -a
source "$DEPLOY_PATH/.env"
set +a

# Start containers
echo "Starting containers..."
cd "$DEPLOY_PATH"
docker-compose -f docker-compose.prod.yml down 2>/dev/null || true
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for services to be ready
echo "Waiting for services to start..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# Build frontend
echo "Building frontend..."
cd "$DEPLOY_PATH/frontend"
npm install --silent
npm run build
docker-compose -f "$DEPLOY_PATH/docker-compose.prod.yml" exec -T nginx rm -rf /app/frontend/dist || true
docker cp frontend-build-temp:/app/dist "$DEPLOY_PATH/frontend/" 2>/dev/null || true

echo "✓ Deployment completed!"
echo ""
echo "Access your application at:"
echo "  URL: http://$(hostname -I | awk '{print $1}')"
echo "  Default credentials:"
echo "    Email: admin@servermanager.local"
echo "    Password: admin123"

REMOTE_SCRIPT_END
)

# Create remote .env.example if needed
echo -e "${YELLOW}Copying project files...${NC}"
scp -r backend "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/" 2>/dev/null || true
scp -r frontend/dist "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/frontend/" 2>/dev/null || true
scp -r docker "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/" 2>/dev/null || true
scp docker-compose.prod.yml "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/" 2>/dev/null || true
echo -e "${GREEN}✓ Files copied${NC}"
echo ""

# Execute remote deployment
echo -e "${YELLOW}Executing deployment on remote server...${NC}"
ssh "$DEPLOY_USER@$DEPLOY_HOST" bash -s "$BRANCH" "$REPO_URL" <<'SSH_SCRIPT_END'
#!/bin/bash
set -e

DEPLOY_PATH="/opt/server-manager"
DEPLOY_BRANCH="${1:-main}"
REPO_URL="${2:-.}"

echo "Starting remote deployment..."
mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

# Copy environment file if not exists
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cat > .env <<EOF
APP_NAME="Server Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://$(hostname -I | awk '{print $1}')

DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=$(openssl rand -base64 16)
DB_ROOT_PASSWORD=$(openssl rand -base64 16)
REDIS_PASSWORD=$(openssl rand -base64 16)

MAIL_DRIVER=log
EOF
fi

# Create SSL certificates
mkdir -p docker/certs
if [ ! -f docker/certs/cert.pem ]; then
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout docker/certs/key.pem \
        -out docker/certs/cert.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost" -batch
fi

# Load environment
set -a
source .env
set +a

# Start services with Docker Compose
echo "Stopping existing containers..."
docker-compose -f docker-compose.prod.yml down 2>/dev/null || true

echo "Building and starting containers..."
docker-compose -f docker-compose.prod.yml up -d --build

echo "Waiting for services..."
sleep 15

# Run migrations
echo "Running migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force --seed 2>/dev/null || true

# Copy frontend build
echo "Updating frontend..."
docker-compose -f docker-compose.prod.yml exec -T nginx rm -rf /app/frontend/dist

# Generate app key if needed
docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --force || true

echo "✓ Deployment successful!"
echo ""
echo "Application is running at:"
echo "  URL: http://$(hostname -I | awk '{print $1}')"
echo "  Default login:"
echo "    Email: admin@servermanager.local"
echo "    Password: admin123"

SSH_SCRIPT_END

echo -e "${GREEN}✓ Remote deployment completed!${NC}"
echo ""
echo -e "${BLUE}================================${NC}"
echo -e "${GREEN}✓ Deployment Complete!${NC}"
echo -e "${BLUE}================================${NC}"
echo ""
echo "Your application is now running on:"
echo -e "  ${YELLOW}http://$DEPLOY_HOST${NC}"
echo ""
echo "Default Credentials:"
echo "  Email: admin@servermanager.local"
echo "  Password: admin123"
echo ""
echo "Next steps:"
echo "  1. Change default password"
echo "  2. Configure your servers"
echo "  3. Set up monitoring alerts"
echo ""
