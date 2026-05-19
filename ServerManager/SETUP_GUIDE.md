# Advanced Server Management System - Complete Setup Guide

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose (Recommended)
- OR: PHP 8.2+, MySQL 8.0, Node.js 18+, Redis

### Docker Setup (Recommended)

```bash
cd ServerManager

# Start all services
docker-compose up -d

# Wait a few seconds, then run migrations
docker-compose exec app php artisan migrate:fresh --seed

# Backend will be at http://localhost:8000
# Visit http://localhost:8000/health to verify
```

### Manual Setup

#### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate:fresh --seed

# Generate SSH keys for server management
mkdir -p storage/ssh
ssh-keygen -t rsa -b 4096 -f storage/ssh/server_manager_key -N ""

# Start server
php artisan serve
```

#### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

## 📋 Default Login Credentials

| Email | Password | Role |
|-------|----------|------|
| admin@servermanager.local | admin123 | Admin |
| manager@servermanager.local | manager123 | Manager |
| viewer@servermanager.local | viewer123 | Viewer |

## 🔧 Configuration

### Backend (.env)

```env
APP_NAME="Server Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=server_manager
DB_USERNAME=laravel
DB_PASSWORD=laravel

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_PORT=6001

ALERT_CRITICAL_THRESHOLD=90
ALERT_WARNING_THRESHOLD=70
```

### Frontend (.env.local)

```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_PUSHER_APP_KEY=app-key
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_PUSHER_CLUSTER=mt1
```

## 🗂️ Project Structure

```
ServerManager/
├── backend/
│   ├── app/
│   │   ├── Models/              # Database models
│   │   ├── Http/Controllers/    # API endpoints
│   │   ├── Services/            # Business logic (SSH, Alerts)
│   │   ├── Jobs/                # Background jobs
│   │   └── Events/              # Broadcast events
│   ├── database/
│   │   ├── migrations/          # Database schema
│   │   └── seeders/             # Test data
│   ├── routes/
│   │   └── api.php              # API routes
│   ├── .env.example
│   ├── composer.json
│   └── Dockerfile
│
├── frontend/
│   ├── src/
│   │   ├── components/          # Vue components
│   │   ├── views/               # Page components
│   │   ├── stores/              # Pinia stores
│   │   ├── services/            # API client
│   │   ├── router/              # Routes
│   │   └── main.js
│   ├── package.json
│   ├── vite.config.js
│   └── tailwind.config.js
│
├── docker-compose.yml
└── README.md
```

## 📊 Features

### Server Management
- ✅ Add/remove servers
- ✅ Real-time status monitoring
- ✅ SSH-based management
- ✅ Multiple authentication types

### Monitoring
- ✅ CPU, Memory, Disk metrics
- ✅ Network traffic monitoring
- ✅ Process count tracking
- ✅ Load average monitoring

### Firewall Management
- ✅ Add/remove firewall rules
- ✅ Inbound/outbound rules
- ✅ Enable/disable rules
- ✅ Protocol management (TCP, UDP, ICMP)

### User Management
- ✅ Change Linux user passwords
- ✅ User list per server
- ✅ Permission management

### Alerting System
- ✅ Configurable alert rules
- ✅ Multiple severity levels (Critical, Warning, Info)
- ✅ Email notifications
- ✅ Webhook integrations
- ✅ Real-time alerts

### Dashboard
- ✅ System overview statistics
- ✅ Recent alerts
- ✅ Metric trends
- ✅ Server status at a glance

## 🔌 API Endpoints

### Authentication
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/register
```

### Servers
```
GET    /api/servers
POST   /api/servers
GET    /api/servers/{id}
PUT    /api/servers/{id}
DELETE /api/servers/{id}
```

### Metrics
```
GET    /api/servers/{id}/metrics
GET    /api/servers/{id}/metrics/history
```

### Firewall
```
GET    /api/servers/{id}/firewall-rules
POST   /api/servers/{id}/firewall-rules
PUT    /api/servers/{id}/firewall-rules/{rule_id}
DELETE /api/servers/{id}/firewall-rules/{rule_id}
```

### Alerts
```
GET    /api/alerts
PUT    /api/alerts/{id}/resolve
GET    /api/alert-rules
POST   /api/alert-rules
PUT    /api/alert-rules/{id}
DELETE /api/alert-rules/{id}
```

### Users
```
GET    /api/servers/{id}/users
POST   /api/servers/{id}/users/{username}/change-password
```

## 🔐 Security Features

- JWT Authentication (Sanctum)
- Role-based access control (Admin, Manager, Viewer)
- SSH key-based server authentication
- Encrypted sensitive data
- CORS protection
- Rate limiting
- API token expiration

## 🛠️ Development

### Running Tests

```bash
cd backend
php artisan test
```

### Database Migrations

```bash
# Create new migration
php artisan make:migration migration_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh database with seeding
php artisan migrate:fresh --seed
```

### Queue Processing

```bash
# Process queued jobs
php artisan queue:work redis --queue=default

# For testing (synchronous)
php artisan queue:work --queue=default
```

## 📦 Dependencies

### Backend (Laravel)
- laravel/framework: 11.0
- laravel/sanctum: 4.0
- phpseclib/phpseclib: 3.0 (SSH support)
- pusher/pusher-php-server: 7.2 (Real-time)

### Frontend (Vue 3)
- vue: 3.3.4
- vue-router: 4.2.4
- pinia: 2.1.4
- axios: 1.5.0
- chart.js: 4.4.0
- tailwindcss: 3.3.0

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new APP_KEY
- [ ] Configure proper database
- [ ] Set up Redis cache
- [ ] Configure email service
- [ ] Set up HTTPS/SSL
- [ ] Configure backup strategy
- [ ] Set up monitoring
- [ ] Configure firewall rules
- [ ] Generate SSH keys for all servers

### Docker Production Build

```bash
docker-compose -f docker-compose.yml build
docker-compose -f docker-compose.yml up -d
```

## 🐛 Troubleshooting

### Backend Issues

**Port already in use:**
```bash
# Change port in .env or use different port
php artisan serve --port=8001
```

**Database connection error:**
```bash
# Check credentials in .env
php artisan config:clear
php artisan migrate
```

**SSH connection failures:**
- Verify SSH key permissions (600)
- Test manually: `ssh -i storage/ssh/server_manager_key user@ip`
- Check firewall allows SSH

### Frontend Issues

**API connection fails:**
- Verify backend is running
- Check VITE_API_BASE_URL in .env.local
- Check browser console for CORS errors

**Hot reload not working:**
- Ensure Vite server is running on port 5173
- Check for port conflicts

## 📝 Logging

Logs are stored in `backend/storage/logs/`

```bash
# Watch logs
tail -f backend/storage/logs/laravel.log
```

## 📞 Support

For issues:
1. Check logs in `storage/logs/`
2. Review error messages in browser console
3. Verify environment configuration
4. Check database connection
5. Review Laravel documentation

## 📄 License

MIT License - See LICENSE file

## 🤝 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

---

**Questions?** Check the README files in backend/ and frontend/ directories for detailed setup instructions.
