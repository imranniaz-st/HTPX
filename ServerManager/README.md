# Advanced Server Management System

A modern, scalable alternative to Zabbix built with Laravel, MySQL, and Vue.js. Monitor, manage, and secure your Linux servers with ease.

## Features

- **Real-time Server Monitoring**: CPU, RAM, disk, network metrics
- **Firewall Management**: Configure and monitor firewall rules
- **Storage Alerts**: Get notified when disk usage exceeds thresholds
- **User Management**: Change Linux server user passwords remotely
- **Server Configuration**: Web-based server setup and management
- **Alert Notifications**: Email and webhook integrations
- **Real-time Dashboards**: Live metrics with WebSocket updates
- **Agent-Based Architecture**: Lightweight agent for server communication

## Tech Stack

- **Backend**: Laravel 11 + PHP 8.2
- **Frontend**: Vue.js 3 + Vite
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Queue**: Redis Queue
- **Real-time**: Laravel WebSockets / Pusher
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome

## Project Structure

```
ServerManager/
├── backend/                 # Laravel Backend
│   ├── app/
│   │   ├── Models/         # Database models
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   │   ├── Services/       # Business logic
│   │   ├── Jobs/           # Queue jobs
│   │   └── Events/         # Broadcast events
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   ├── config/
│   └── storage/
│
├── frontend/               # Vue.js Frontend
│   ├── src/
│   │   ├── components/     # Reusable components
│   │   ├── views/          # Page components
│   │   ├── stores/         # Pinia state management
│   │   ├── services/       # API client
│   │   └── App.vue
│   ├── public/
│   └── index.html
│
├── docker-compose.yml      # Docker development environment
└── README.md
```

## Installation

### Prerequisites
- Docker & Docker Compose (recommended)
- OR: PHP 8.2+, MySQL 8.0, Node.js 18+

### Quick Start with Docker

```bash
cd backend
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan serve
cd ../frontend
npm install
npm run dev
```

### Manual Setup

**Backend:**
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

**Frontend:**
```bash
cd frontend
npm install
npm run dev
```

## API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh token

### Servers
- `GET /api/servers` - List all servers
- `POST /api/servers` - Add new server
- `GET /api/servers/{id}` - Server details
- `PUT /api/servers/{id}` - Update server
- `DELETE /api/servers/{id}` - Delete server

### Monitoring
- `GET /api/servers/{id}/metrics` - Current metrics
- `GET /api/servers/{id}/metrics/history` - Historical metrics
- `GET /api/servers/{id}/alerts` - Server alerts

### Firewall
- `GET /api/servers/{id}/firewall/rules` - List rules
- `POST /api/servers/{id}/firewall/rules` - Add rule
- `DELETE /api/servers/{id}/firewall/rules/{id}` - Remove rule

### Users
- `GET /api/servers/{id}/users` - List server users
- `POST /api/servers/{id}/users/{username}/change-password` - Change password

## Security Features

- JWT authentication
- Role-based access control (Admin, Manager, Viewer)
- SSH key-based server communication
- Encrypted sensitive data
- Rate limiting on API endpoints
- CSRF protection

## Installation & Setup

See [Backend Setup](./backend/README.md) and [Frontend Setup](./frontend/README.md) for detailed instructions.

## Troubleshooting

### Common Issues

**Backend won't start:**
- Check `.env` configuration
- Ensure MySQL is running
- Run `php artisan migrate`

**Frontend can't connect to API:**
- Check `VITE_API_BASE_URL` in frontend `.env`
- Ensure backend is running on correct port
- Check CORS settings in backend

**WebSocket connection fails:**
- Install WebSocket server: `npm install -g laravel-echo-server`
- Configure in `config/broadcasting.php`

## License

MIT License - See LICENSE file for details

## Contributing

Contributions welcome! Please see CONTRIBUTING.md

## Support

For issues and feature requests, please open an issue on GitHub.
