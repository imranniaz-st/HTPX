# Feature Documentation

## Server Management

### Add Server
1. Click "Servers" menu
2. Click "+ Add Server" button
3. Fill in server details:
   - **Name**: Unique identifier
   - **IP Address**: Server IP
   - **Hostname**: FQDN or hostname
   - **SSH Port**: Usually 22
   - **SSH Username**: Login username (e.g., ubuntu, root)
   - **OS Type**: Linux, Windows, or macOS
4. Click "Create Server"

### Monitor Servers
- View real-time metrics on dashboard
- See individual server details
- Track CPU, Memory, and Disk usage
- View network statistics

## Firewall Management

### Add Firewall Rule
1. Select a server from the Servers page
2. Click "Firewall Rules" tab
3. Click "Add Rule"
4. Configure:
   - **Name**: Rule identifier
   - **Direction**: Inbound or Outbound
   - **Action**: Allow or Deny
   - **Protocol**: TCP, UDP, ICMP, or All
   - **Port**: Port number (optional)
   - **Source/Destination IP**: IP address (optional)
5. Click "Add Rule"

### Manage Rules
- Enable/disable rules without deletion
- Edit existing rules
- Delete rules
- View rule status in real-time

## Storage Alerts

### Create Storage Alert
1. Go to "Settings" → "Alert Rules"
2. Click "New Alert Rule"
3. Select:
   - **Server**: Target server
   - **Metric Type**: disk_usage
   - **Operator**: > (greater than)
   - **Threshold**: 90 (%)
   - **Severity**: Critical
4. Configure notifications:
   - Email alerts
   - Webhook URLs
5. Click "Create Rule"

### Alert Thresholds
- **Critical**: Default 90%
- **Warning**: Default 70%
- Custom thresholds supported

## User Password Management

### Change Linux User Password
1. Go to server details
2. Click "Users" tab
3. Select user from list
4. Click "Change Password"
5. Enter new password (min 8 characters)
6. Click "Update"

**Note**: Requires SSH access with proper permissions

## Real-time Dashboards

### Dashboard Features
- **Server Overview**: Online/Offline count
- **Active Alerts**: Critical alerts summary
- **System Metrics**: Average CPU and Memory
- **Recent Alerts**: Last 5 alerts
- **Server Grid**: Quick server status view

### Refresh Rate
- Metrics: Every 5 minutes (configurable)
- Alerts: Real-time via WebSocket
- Dashboard: Auto-refresh every 30 seconds

## Notifications

### Email Alerts
Configure SMTP in backend `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=alerts@servermanager.local
```

### Webhook Alerts
Set webhook URL in alert rule settings. Receives JSON payload:
```json
{
  "alert_id": 123,
  "server_id": 1,
  "title": "High CPU Usage",
  "message": "CPU usage exceeded 90%",
  "severity": "critical",
  "metric_value": 95.5,
  "threshold": 90,
  "created_at": "2024-01-15T10:30:00Z"
}
```

## Server Configuration (Advanced)

### SSH Key Setup
Add public key to authorized servers:
```bash
cat storage/ssh/server_manager_key.pub | ssh user@server \
  "cat >> ~/.ssh/authorized_keys"
```

### Agent Installation
For continuous monitoring, install lightweight agent on servers:
1. Download agent from `/agents/install.sh`
2. Run: `bash install.sh`
3. Configure: `/etc/server-manager/agent.conf`
4. Start: `systemctl start server-manager-agent`

## User Roles

### Admin
- Full system access
- Manage all servers
- Create users and roles
- Configure system settings
- View all alerts

### Manager
- Manage assigned servers
- View all servers (read-only)
- Create and manage firewall rules
- Change user passwords
- Acknowledge alerts

### Viewer
- Read-only access
- View server metrics
- View alerts
- Cannot modify anything

## Troubleshooting

### Server Offline
1. Verify server is running
2. Check SSH connectivity: `ssh -i storage/ssh/server_manager_key user@ip`
3. Verify firewall allows SSH (port 22)
4. Check SSH credentials in server settings

### Alerts Not Working
1. Verify alert rule is enabled
2. Check metric data is being collected
3. Verify email/webhook configuration
4. Check system logs: `storage/logs/laravel.log`

### High CPU Usage
1. Check running processes on server
2. Review recent jobs in queue
3. Optimize database queries
4. Scale horizontally with load balancing

## Performance Optimization

### Database
- Indexes on frequently queried columns
- Archival of old metrics
- Query optimization

### Caching
- Redis for caching
- API response caching
- Session caching

### Monitoring
- Optimize metric collection frequency
- Batch metric updates
- Compress historical data

## Backup & Recovery

### Database Backup
```bash
mysqldump -u laravel -p server_manager > backup.sql
```

### Restore
```bash
mysql -u laravel -p server_manager < backup.sql
```

### SSH Keys Backup
```bash
cp -r storage/ssh backup/
chmod 700 backup/ssh
```

## Security Best Practices

1. **Change default passwords** immediately
2. **Use strong SSH keys** (4096-bit RSA minimum)
3. **Enable HTTPS** in production
4. **Rotate API tokens** regularly
5. **Restrict user permissions** by role
6. **Monitor access logs** regularly
7. **Keep dependencies updated**
8. **Use environment variables** for secrets

## Scaling

### Horizontal Scaling
- Load balance frontend with nginx
- Multi-instance backend deployment
- Database replication
- Redis clustering

### Performance Tuning
- Enable query caching
- Optimize metric collection
- Archive old data
- Use CDN for static assets

## API Rate Limiting

Configured endpoints have rate limits:
- Auth endpoints: 5 requests/minute
- API endpoints: 60 requests/minute
- Agent endpoints: 100 requests/minute

Exceeded limits return `429 Too Many Requests`

## Support & Updates

- Check GitHub releases for updates
- Review changelog before upgrading
- Backup before major upgrades
- Test in staging environment first
