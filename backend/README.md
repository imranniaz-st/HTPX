# Server Manager Backend - Setup Instructions

## System Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Redis (optional, for caching and queues)
- OpenSSH client (for SSH connections)

## Installation

### 1. Install PHP Dependencies

```bash
cd backend
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=server_manager
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 3. Database Setup

```bash
php artisan migrate:fresh --seed
```

This will:
- Create all database tables
- Seed default users and sample data
- Generate test servers

### 4. SSH Key Setup (Important!)

For SSH authentication to remote servers:

```bash
mkdir -p storage/ssh
ssh-keygen -t rsa -b 4096 -f storage/ssh/server_manager_key -N ""
chmod 600 storage/ssh/server_manager_key
```

Add your public key to authorized servers:

```bash
cat storage/ssh/server_manager_key.pub
```

### 5. Start Development Server

```bash
php artisan serve
```

The backend will be available at `http://localhost:8000`

## Database Seeding

### Default Users

| Email | Password | Role |
|-------|----------|------|
| admin@servermanager.local | admin123 | Admin |
| manager@servermanager.local | manager123 | Manager |
| viewer@servermanager.local | viewer123 | Viewer |

### Sample Servers

Three sample servers are created for testing:
- Web Server 1 (192.168.1.10)
- Database Server (192.168.1.20)
- API Server (192.168.1.30)

## API Endpoints

### Authentication

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@servermanager.local",
  "password": "admin123"
}
```

### Get All Servers

```bash
GET /api/servers
Authorization: Bearer {token}
```

### Create Server

```bash
POST /api/servers
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Server",
  "ip_address": "192.168.1.40",
  "hostname": "server-04.local",
  "os_type": "linux",
  "ssh_username": "ubuntu"
}
```

### Get Server Metrics

```bash
GET /api/servers/{id}/metrics
Authorization: Bearer {token}
```

### Get Server Alerts

```bash
GET /api/alerts?is_resolved=false
Authorization: Bearer {token}
```

## Queue Configuration

For background jobs (monitoring, alerts):

```bash
php artisan queue:work redis --queue=default
```

## Broadcasting Setup (Real-time Updates)

```bash
npm install -g laravel-echo-server
laravel-echo-server init
laravel-echo-server start
```

## Troubleshooting

### SSH Connection Issues

1. Verify SSH key permissions: `ls -la storage/ssh/`
2. Test SSH manually: `ssh -i storage/ssh/server_manager_key user@ip_address`
3. Check firewall rules on target servers

### Database Connection Error

```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate
```

### Permission Issues

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Development Commands

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create API route cache
php artisan route:cache

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Tinker (interactive console)
php artisan tinker

# Run tests
php artisan test
```

## Docker Setup

```bash
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan serve --host=0.0.0.0
```

## Security Notes

- Always use HTTPS in production
- Rotate SSH keys regularly
- Keep Laravel and dependencies updated
- Use strong database passwords
- Implement rate limiting
- Enable API token expiration
- Secure sensitive environment variables

## Support

For issues or questions, please refer to:
- [Laravel Documentation](https://laravel.com/docs)
- [phpseclib Documentation](https://phpseclib.com/)
- Project GitHub Issues
