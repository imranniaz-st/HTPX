# Production Deployment Guide

## Quick Deployment to Server

### Prerequisites
- Server with Docker and Docker Compose installed
- SSH access to server
- Git installed on server
- Minimum 2GB RAM, 20GB storage

### Deployment Steps

#### 1. From Your Local Machine

Run the deployment script:

```bash
cd /path/to/ServerManager
bash deploy.sh 167.99.13.48 main
```

**Parameters:**
- `167.99.13.48` - Your server IP or hostname
- `main` - Git branch to deploy (optional, defaults to main)

#### 2. What the Script Does

The deployment script automatically:

Validates SSH connection
Builds frontend (npm install & npm run build)
Copies project files to server (`/opt/server-manager`)
Creates `.env` with secure random passwords
Generates SSL certificates
Starts Docker containers
Runs database migrations
Seeds default data
Configures Nginx reverse proxy

#### 3. Access Your Application

After deployment completes:

**URL:** `http://167.99.13.48`

**Default Credentials:**
- Email: `admin@servermanager.local`
- Password: `admin123`

**Change password immediately in production!**

---

## Manual Deployment (If Script Doesn't Work)

### Step 1: Connect to Server

```bash
ssh root@167.99.13.48
```

### Step 2: Install Docker & Docker Compose

```bash
# Update system
apt update && apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Install Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" \
  -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Verify
docker --version
docker-compose --version
```

### Step 3: Clone or Upload Project

**Option A: Clone from Git**
```bash
cd /opt
git clone https://github.com/yourrepo/server-manager.git
cd server-manager
git checkout main
```

**Option B: Upload via SCP (from your local machine)**
```bash
scp -r . root@167.99.13.48:/opt/server-manager/
```

### Step 4: Set Up Environment

```bash
cd /opt/server-manager

# Copy environment file
cp backend/.env.example .env

# Generate secure passwords
cat > .env <<EOF
APP_NAME="Server Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://167.99.13.48

DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=$(openssl rand -base64 16)
DB_ROOT_PASSWORD=$(openssl rand -base64 16)
REDIS_PASSWORD=$(openssl rand -base64 16)

MAIL_DRIVER=log
EOF

cat .env
```

### Step 5: Generate SSL Certificates

```bash
mkdir -p docker/certs

# Self-signed certificate (for testing)
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/certs/key.pem \
  -out docker/certs/cert.pem \
  -subj "/C=US/ST=State/L=City/O=Organization/CN=167.99.13.48" \
  -batch
```

### Step 6: Build Frontend

```bash
cd frontend
npm install
npm run build
cd ..
```

### Step 7: Start Services

```bash
# Load environment
set -a
source .env
set +a

# Start with Docker Compose
docker-compose -f docker-compose.prod.yml up -d --build

# Check status
docker-compose -f docker-compose.prod.yml ps
```

### Step 8: Run Migrations

```bash
# Wait for MySQL to be ready (30-60 seconds)
sleep 30

# Run migrations and seed
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate:fresh --seed

# Generate application key
docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate
```

### Step 9: Verify Installation

```bash
# Check all containers running
docker-compose -f docker-compose.prod.yml ps

# Test API
curl http://localhost:8000/api/health

# View logs
docker-compose -f docker-compose.prod.yml logs -f app
```

---

## Access Your Application

### HTTP/HTTPS Access

**Via Browser:**
```
http://167.99.13.48
```

**Default Login:**
```
Email: admin@servermanager.local
Password: admin123
```

### Nginx Status

```bash
curl http://167.99.13.48/health
```

---

## Daily Management Commands

### View Logs

```bash
cd /opt/server-manager

# View all service logs
docker-compose -f docker-compose.prod.yml logs -f

# View specific service
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f mysql
```

### Stop Services

```bash
docker-compose -f docker-compose.prod.yml stop
```

### Start Services

```bash
docker-compose -f docker-compose.prod.yml start
```

### Restart Services

```bash
docker-compose -f docker-compose.prod.yml restart
```

### Update Application

```bash
cd /opt/server-manager

# Pull latest code
git pull origin main

# Rebuild containers
docker-compose -f docker-compose.prod.yml up -d --build

# Run migrations
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate
```

### Backup Database

```bash
cd /opt/server-manager

# Export database
docker-compose -f docker-compose.prod.yml exec mysql mysqldump \
  -u laravel -p$DB_PASSWORD \
  --all-databases > backup_$(date +%Y%m%d_%H%M%S).sql

# View backups
ls -lh backup_*.sql
```

### Restore Database

```bash
# Restore from backup
docker-compose -f docker-compose.prod.yml exec mysql mysql \
  -u laravel -p$DB_PASSWORD < backup_YYYYMMDD_HHMMSS.sql
```

---

## Troubleshooting

### Services Not Starting

```bash
# Check docker status
docker ps -a

# View error logs
docker-compose -f docker-compose.prod.yml logs

# Restart all services
docker-compose -f docker-compose.prod.yml restart
```

### Can't Connect to Application

```bash
# Check if nginx is running
docker ps | grep nginx

# Check nginx logs
docker-compose -f docker-compose.prod.yml logs nginx

# Test backend connection
curl http://localhost:8000/api/health

# Verify firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### Database Connection Error

```bash
# Check MySQL is running
docker-compose -f docker-compose.prod.yml ps mysql

# Check MySQL logs
docker-compose -f docker-compose.prod.yml logs mysql

# Verify credentials in .env
cat .env | grep DB_
```

### Out of Memory

```bash
# Check resource usage
docker stats

# Free up space
docker system prune -a

# Check disk space
df -h
```

---

## Performance Optimization

### Enable caching
Already configured with Redis in docker-compose.prod.yml

### Optimize Nginx
Update `docker/nginx/conf.d/default.conf` - already optimized with:
- Gzip compression
- Static asset caching
- Rate limiting

### Database optimization
```bash
# Check indices
docker-compose -f docker-compose.prod.yml exec mysql \
  mysql -u laravel -p$DB_PASSWORD server_manager \
  -e "SHOW INDEXES FROM servers;"
```

---

## Security Checklist

- [ ] Change default admin password
- [ ] Use HTTPS in production (configure real SSL certificate)
- [ ] Configure firewall (block unused ports)
- [ ] Set up automated backups
- [ ] Enable application logging
- [ ] Update Docker images regularly
- [ ] Use strong database passwords (auto-generated in .env)
- [ ] Restrict SSH access
- [ ] Monitor server resources
- [ ] Set up monitoring alerts

---

## Need Help?

Check logs:
```bash
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml logs -f
```

Common issues are usually in:
- Database connection (`/opt/server-manager/.env`)
- Port binding (firewall blocking 80/443)
- Disk space (40GB+ used)
- Memory pressure (MySQL needs 512MB+)
