# Server Manager - Project Overview

## What is Server Manager?

**Server Manager** is an advanced, modern alternative to Zabbix built with Laravel and Vue.js. It's a comprehensive server monitoring and management system that allows you to:

- Monitor multiple Linux servers in real-time
- Manage firewall rules remotely
- Get alerts when disk space is low
- Change Linux user passwords remotely
- Configure servers through a web interface
- Track CPU, Memory, Disk, and Network metrics

## Core Capabilities

### 1. Server Monitoring
- **Real-time Metrics**: CPU, RAM, Disk, Network usage
- **Historical Data**: View trends over time
- **Server Status**: Online/Offline detection
- **Multi-OS Support**: Linux, Windows, macOS

### 2. Firewall Management
- **Add/Remove Rules**: Easily configure firewall
- **Inbound/Outbound**: Control traffic direction
- **Multiple Protocols**: TCP, UDP, ICMP
- **Enable/Disable**: Toggle rules without deletion

### 3. Storage Alerts
- **Disk Space Monitoring**: Track disk usage
- **Custom Thresholds**: Set your alert levels
- **Multiple Severities**: Critical, Warning, Info
- **Notifications**: Email and Webhook support

### 4. User Management
- **Change Passwords**: Update user passwords remotely
- **Permission Control**: Manage user access levels
- **Multi-role Support**: Admin, Manager, Viewer

### 5. Real-time Dashboard
- **System Overview**: Quick status view
- **Active Alerts**: See critical issues instantly
- **Metrics Visualization**: Charts and graphs
- **Server Grid**: All servers at a glance

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   Frontend (Vue.js 3)                   │
│              http://localhost:5173                      │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  • Dashboard View          • Server Management          │
│  • Alert Management        • Firewall Rules             │
│  • User Management         • Real-time Updates          │
│                                                          │
└────────────────┬────────────────────────────────────────┘
                 │
            HTTP/REST API
        Axios + Interceptors
                 │
┌────────────────▼────────────────────────────────────────┐
│                 Backend (Laravel 11)                    │
│              http://localhost:8000                      │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  • Authentication (Sanctum)                            │
│  • Server Management APIs                              │
│  • SSH Service (phpseclib)                             │
│  • Alert Processing                                     │
│  • Real-time Events (Pusher)                           │
│                                                          │
└────────────────┬────────────────────────────────────────┘
                 │
        ┌────────┼────────┐
        │        │        │
┌───────▼──┐ ┌──▼──────┐ ┌▼──────────┐
│ MySQL    │ │ Redis   │ │ SSH Keys  │
│ Database │ │ Cache   │ │ Servers   │
│          │ │ & Queue │ │ (Keys)    │
└──────────┘ └─────────┘ └───────────┘
```

## Tech Stack

**Backend**
- Language: PHP 8.2+
- Framework: Laravel 11
- Database: MySQL 8.0
- Cache: Redis
- Authentication: Laravel Sanctum
- SSH: phpseclib

**Frontend**
- Framework: Vue 3
- Build Tool: Vite
- State Management: Pinia
- HTTP Client: Axios
- Styling: Tailwind CSS
- Package Manager: npm

**Infrastructure**
- Containerization: Docker & Docker Compose
- Web Server: PHP-FPM
- Database: MySQL
- Cache: Redis
- Real-time: Pusher/Laravel Echo

## Database Schema

### Core Tables

**users** - System users and authentication
- id, name, email, password, role, is_active

**servers** - Managed servers
- id, name, ip_address, hostname, os_type, status, last_heartbeat

**server_metrics** - Performance metrics
- id, server_id, cpu_usage, memory_usage, disk_usage, recorded_at

**alerts** - System alerts
- id, server_id, type, severity, title, message, is_resolved

**firewall_rules** - Firewall configurations
- id, server_id, direction, action, protocol, port, source_ip

**alert_rules** - Alert configurations
- id, server_id, metric_type, operator, threshold, severity

## Security Architecture

```
Internet → HTTPS → Nginx → Laravel
                      ↓
                 Sanctum (JWT)
                      ↓
                 Role-Based Access
                      ↓
            SSH Keys (Server Access)
                      ↓
                Remote Servers
```

### Authentication Flow
1. User logs in with email/password
2. System generates JWT token
3. Token stored in localStorage
4. Token included in all API requests
5. Backend validates token and user role

### Server Access
1. System stores SSH private key securely
2. Uses phpseclib for SSH connections
3. Executes commands over SSH
4. Results sent back to frontend
5. No passwords stored (key-based auth)

## Data Flow

### Monitoring Data Collection
```
Remote Server
    ↓
Linux Agent (or API heartbeat)
    ↓
POST /api/agent/metrics
    ↓
Store in server_metrics table
    ↓
Trigger alert rules
    ↓
Create alerts if thresholds exceeded
    ↓
Broadcast real-time events
    ↓
Dashboard displays metrics
```

### Firewall Rule Application
```
Admin configures rule in UI
    ↓
POST /api/servers/{id}/firewall-rules
    ↓
Store in database
    ↓
SSHService.applyFirewallRule()
    ↓
Connect via SSH
    ↓
Execute: sudo ufw allow/deny
    ↓
Return status to frontend
```

## Key Workflows

### Adding a Server
1. Admin fills server form (IP, hostname, SSH credentials)
2. POST /api/servers
3. Create record in database
4. Test SSH connection
5. Return success/error
6. Add to server list view

### Monitoring Server
1. Agent sends metrics to /api/agent/metrics
2. ServerMetric record created
3. Check alert rules
4. Trigger critical alerts
5. Send notifications (email/webhook)
6. Broadcast event to real-time dashboard

### Changing User Password
1. Manager selects user and server
2. Enter new password
3. POST /api/servers/{id}/users/{username}/change-password
4. SSH connect as privileged user
5. Execute: echo 'user:password' | chpasswd
6. Return success status

## UI Components

### Main Views
- **Login**: Authentication page
- **Dashboard**: System overview and metrics
- **Servers**: Server list and details
- **Alerts**: Alert management and history
- **Firewall**: Firewall rule management
- **Settings**: System configuration

### Reusable Components
- StatCard: Display metrics
- ServerCard: Server status
- AlertTable: Alert listing
- MetricChart: Data visualization
- FirewallRuleForm: Rule configuration

## Alert System

### Alert Types
- **disk_full**: Disk space exceeded threshold
- **high_cpu**: CPU usage exceeded threshold
- **high_memory**: Memory usage exceeded threshold
- **server_offline**: Server not responding
- **custom**: User-defined alerts

### Alert Workflow
1. Alert rule created with condition
2. Metrics monitored against condition
3. When triggered, Alert record created
4. Notifications sent (email + webhook)
5. Alert displayed in UI
6. Admin can resolve alert manually

### Notification Channels
- **Email**: SMTP-based alerts
- **Webhook**: HTTP POST to custom URL
- **Real-time**: WebSocket to dashboard

## API Structure

### Request Format
```
POST /api/servers
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Web Server 1",
  "ip_address": "192.168.1.10",
  "hostname": "web-01.local",
  "os_type": "linux"
}
```

### Response Format
```json
{
  "id": 1,
  "name": "Web Server 1",
  "ip_address": "192.168.1.10",
  "status": "online",
  "created_at": "2024-01-15T10:30:00Z"
}
```

### Error Handling
```json
{
  "message": "Error description",
  "errors": {
    "field": ["Error message"]
  }
}
```

## Deployment

### Development
```bash
docker-compose up -d
npm run dev
```

### Production
- Set APP_ENV=production
- Use HTTPS
- Configure proper database
- Set up backups
- Configure monitoring
- Use SSH keys exclusively
- Enable rate limiting

## Use Cases

### System Administration
- Monitor all servers from one dashboard
- Get alerts for critical issues
- Manage firewall rules centrally
- Change passwords easily

### DevOps
- Track infrastructure metrics
- Respond to alerts quickly
- Configure servers via UI
- Automate common tasks

### Security
- Centralized access control
- Audit trail of changes
- SSH key-based authentication
- Role-based permissions

## Getting Started

### Quick Start (Docker)
```bash
cd ServerManager
docker-compose up -d
docker-compose exec app php artisan migrate:fresh --seed
```

Visit: http://localhost:8000

Login: admin@servermanager.local / admin123

### Manual Setup
See SETUP_GUIDE.md for detailed instructions

## Documentation Files

- **README.md** - Project overview
- **SETUP_GUIDE.md** - Installation guide
- **FEATURES.md** - Feature documentation
- **backend/README.md** - Backend setup
- **frontend/README.md** - Frontend setup
- **API_DOCUMENTATION.md** - API reference (optional)

## Contributing

1. Follow Laravel + Vue.js best practices
2. Write tests for new features
3. Document API changes
4. Keep components reusable
5. Follow security guidelines

## Troubleshooting

### Issue: SSH Connection Fails
- Verify key permissions (600)
- Test manually: `ssh -i key user@host`
- Check firewall allows port 22

### Issue: Alerts Not Triggering
- Verify alert rule is enabled
- Check metrics are being collected
- Review backend logs

### Issue: Frontend Can't Connect
- Verify backend is running
- Check VITE_API_BASE_URL
- Review browser console errors

## Learning Path

1. **Start**: Review this overview
2. **Setup**: Follow SETUP_GUIDE.md
3. **Explore**: Check FEATURES.md
4. **Develop**: Read code in app/Models and src/views
5. **Deploy**: Use production checklist

## License

MIT License - Free for personal and commercial use

---

**Ready to get started?** Follow the SETUP_GUIDE.md for installation instructions!
