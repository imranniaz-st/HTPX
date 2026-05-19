#!/bin/bash

# Server Manager - Development Startup Script

echo "Starting Server Manager..."
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is available
if command -v docker &> /dev/null; then
    echo -e "${GREEN}Docker found${NC}"
    echo ""
    echo "Starting with Docker Compose..."
    docker-compose up -d
    
    echo ""
    echo "Waiting for services to be ready..."
    sleep 5
    
    echo "Running migrations..."
    docker-compose exec -T app php artisan migrate:fresh --seed
    
    echo ""
    echo -e "${GREEN}Backend running at http://localhost:8000${NC}"
    echo -e "${GREEN}Database configured and seeded${NC}"
    echo ""
    echo "Starting frontend development server..."
    cd frontend
    npm install > /dev/null 2>&1
    npm run dev &
    cd ..
    
    echo ""
    echo -e "${GREEN}Frontend running at http://localhost:5173${NC}"
    echo ""
    echo "Default Login Credentials:"
    echo "  Email: admin@servermanager.local"
    echo "  Password: admin123"
    echo ""
    echo "Press Ctrl+C to stop"
    
else
    echo -e "${YELLOW}Docker not found. Using manual setup...${NC}"
    echo ""
    
    # Backend setup
    echo "Setting up backend..."
    cd backend
    
    if ! command -v php &> /dev/null; then
        echo -e "${RED}PHP not found. Please install PHP 8.2+${NC}"
        exit 1
    fi
    
    composer install
    cp .env.example .env
    php artisan key:generate
    
    echo "Configure your database in .env, then run:"
    echo "  php artisan migrate:fresh --seed"
    echo "  php artisan serve"
    echo ""
    
    cd ../frontend
    
    if ! command -v node &> /dev/null; then
        echo -e "${RED}Node.js not found. Please install Node.js 18+${NC}"
        exit 1
    fi
    
    npm install
    npm run dev
fi
