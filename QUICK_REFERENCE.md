# Quick Reference Guide

## 🚀 Quick Start Commands

### Docker Setup (Recommended)
```bash
cd ServerManager
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
# Access at http://localhost:8000
```

### Manual Setup - Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Manual Setup - Frontend
```bash
cd frontend
npm install
npm run dev
```

## 📊 Default Credentials
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@servermanager.local | admin123 |
| Manager | manager@servermanager.local | manager123 |
| Viewer | viewer@servermanager.local | viewer123 |

## 🛠️ Common Tasks

### Generate SSH Keys
```bash
bash generate-ssh-keys.sh
```

### Reset Database
```bash
cd backend
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Run Migrations Only
```bash
php artisan migrate
```

### Create Admin User
```bash
php artisan tinker
User::create([
  'name' => 'Admin',
  'email' => 'admin@example.com',
  'password' => Hash::make('password'),
  'role' => 'admin'
])
```

### View Logs
```bash
# Backend logs
tail -f backend/storage/logs/laravel.log

# Docker logs
docker-compose logs -f app
```

## 🔌 API Endpoints Reference

### Auth
```
POST /api/auth/login          # Login
POST /api/auth/logout         # Logout
POST /api/auth/register       # Register
```

### Servers
```
GET /api/servers              # List all
POST /api/servers             # Create
GET /api/servers/{id}         # Get one
PUT /api/servers/{id}         # Update
DELETE /api/servers/{id}      # Delete
```

### Metrics
```
GET /api/servers/{id}/metrics         # Latest
GET /api/servers/{id}/metrics/history # Historical
```

### Firewall
```
GET /api/servers/{id}/firewall-rules           # List
POST /api/servers/{id}/firewall-rules          # Create
PUT /api/servers/{id}/firewall-rules/{rule_id} # Update
DELETE /api/servers/{id}/firewall-rules/{rule_id} # Delete
```

### Alerts
```
GET /api/alerts                # List
PUT /api/alerts/{id}/resolve   # Resolve
GET /api/alert-rules           # List rules
POST /api/alert-rules          # Create rule
```

### Users
```
GET /api/servers/{id}/users                    # List users
POST /api/servers/{id}/users/{username}/change-password # Change password
```

## 🐛 Debugging

### Backend Issues
```bash
# Check routes
php artisan route:list

# Database test
php artisan tinker
>>> DB::connection()->getPdo();

# Check config
php artisan config:show
```

### Frontend Issues
```bash
# Clear node_modules
rm -rf node_modules package-lock.json
npm install

# Dev server on different port
npm run dev -- --port 3000
```

### Docker Issues
```bash
# View all containers
docker-compose ps

# View app logs
docker-compose logs app

# Restart services
docker-compose restart

# Rebuild images
docker-compose build --no-cache
```

## 📁 Important Files

**Backend**
- `.env` - Configuration
- `app/Models/*.php` - Database models
- `app/Http/Controllers/*.php` - API endpoints
- `database/migrations/*.php` - Schema
- `routes/api.php` - API routes

**Frontend**
- `.env.local` - Configuration
- `src/views/*.vue` - Page components
- `src/stores/*.js` - State management
- `src/services/api-client.js` - API calls
- `src/router/index.js` - Routing

## 🔐 Security Checklist

- [ ] Change default passwords
- [ ] Generate SSH keys
- [ ] Configure HTTPS
- [ ] Set APP_KEY
- [ ] Configure database password
- [ ] Set APP_DEBUG=false (production)
- [ ] Restrict file permissions
- [ ] Configure backup strategy
- [ ] Test SSH connections
- [ ] Enable logging

## 📈 Monitoring Setup

### Add Test Server
```bash
php artisan tinker
>>> Server::create([
  'name' => 'Test Server',
  'ip_address' => '192.168.1.100',
  'hostname' => 'test.local',
  'os_type' => 'linux',
  'ssh_username' => 'ubuntu'
])
```

### Create Alert Rule
```bash
>>> AlertRule::create([
  'server_id' => 1,
  'name' => 'High CPU',
  'metric_type' => 'cpu_usage',
  'operator' => '>',
  'threshold' => 80,
  'severity' => 'critical'
])
```

### Add Firewall Rule
```bash
>>> FirewallRule::create([
  'server_id' => 1,
  'name' => 'Allow SSH',
  'direction' => 'inbound',
  'action' => 'allow',
  'protocol' => 'tcp',
  'port' => 22
])
```

## 🚢 Deployment Commands

### Build Frontend
```bash
cd frontend
npm run build
```

### Prepare Backend for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Docker Production Build
```bash
docker-compose build --no-cache
docker-compose -f docker-compose.yml up -d
```

## 📝 Environment Variables

### Backend Required
```env
APP_KEY=                    # Generate with: php artisan key:generate
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### Frontend Required
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

## 🎯 Useful Artisan Commands
```bash
php artisan make:model Server -m        # Create model with migration
php artisan make:controller ServerController --resource # Create controller
php artisan make:middleware CheckRole   # Create middleware
php artisan tinker                       # Interactive shell
php artisan queue:work                   # Process queued jobs
php artisan migrate:refresh              # Reset and run migrations
php artisan db:seed --class=DatabaseSeeder # Run seeder
php artisan cache:clear                  # Clear application cache
```

## 💾 Backup Commands

### Database
```bash
# Backup
mysqldump -u laravel -p server_manager > backup.sql

# Restore
mysql -u laravel -p server_manager < backup.sql
```

### SSH Keys
```bash
# Backup
cp -r backend/storage/ssh backup/ssh_backup

# Verify
ls -la backup/ssh_backup/
```

## 🔄 Troubleshooting Quick Fixes

**Port Already In Use**
```bash
# Find what's using port 8000
lsof -i :8000
# Kill process or use different port
php artisan serve --port=8001
```

**Database Connection Error**
```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate
```

**SSH Not Working**
```bash
# Test connection
ssh -i backend/storage/ssh/server_manager_key user@ip_address
# Verify key permissions
chmod 600 backend/storage/ssh/server_manager_key
```

**Frontend Not Connecting**
```bash
# Check backend running
curl http://localhost:8000/health
# Verify VITE_API_BASE_URL in .env.local
```

## 📞 Getting Help

1. Check logs: `backend/storage/logs/laravel.log`
2. Review docs: `SETUP_GUIDE.md`, `FEATURES.md`
3. Check API: `PROJECT_OVERVIEW.md`
4. Run: `php artisan migrate:refresh --seed`
5. Restart services: `docker-compose restart`

---

**Need more info?** See the full documentation in README.md or SETUP_GUIDE.md
