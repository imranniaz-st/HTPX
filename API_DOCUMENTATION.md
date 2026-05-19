# API Documentation - Server Manager

## Base URL
```
http://localhost:8000/api
```

## Authentication
All endpoints (except `/api/auth/login` and `/api/auth/register`) require:
```
Authorization: Bearer {token}
```

## Response Format
Success (2xx):
```json
{
  "id": 1,
  "name": "Example",
  "created_at": "2024-01-15T10:30:00Z"
}
```

Error (4xx/5xx):
```json
{
  "message": "Error description",
  "errors": {
    "field": ["Validation error"]
  }
}
```

---

## Authentication Endpoints

### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@servermanager.local",
  "password": "admin123"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@servermanager.local",
    "role": "admin"
  },
  "token": "1|Xjq8K4..."
}
```

### Register
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "New User",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Logout
```
POST /api/auth/logout
Authorization: Bearer {token}
```

---

## Server Endpoints

### List All Servers
```
GET /api/servers?page=1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Web Server 1",
      "ip_address": "192.168.1.10",
      "hostname": "web-01.local",
      "os_type": "linux",
      "status": "online",
      "last_heartbeat": "2024-01-15T10:30:00Z"
    }
  ],
  "per_page": 15,
  "total": 3
}
```

### Get Server Details
```
GET /api/servers/{id}
Authorization: Bearer {token}
```

### Create Server
```
POST /api/servers
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Server",
  "ip_address": "192.168.1.40",
  "hostname": "server-04.local",
  "ssh_port": 22,
  "ssh_username": "ubuntu",
  "os_type": "linux",
  "description": "Production web server"
}
```

### Update Server
```
PUT /api/servers/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "description": "New description"
}
```

### Delete Server
```
DELETE /api/servers/{id}
Authorization: Bearer {token}
```

---

## Metrics Endpoints

### Get Latest Metrics
```
GET /api/servers/{id}/metrics
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 123,
  "server_id": 1,
  "cpu_usage": 45.2,
  "memory_usage": 8192,
  "memory_total": 16384,
  "disk_usage": 102400,
  "disk_total": 1048576,
  "disk_free": 946176,
  "network_in": 1024000,
  "network_out": 512000,
  "load_average": 1.23,
  "recorded_at": "2024-01-15T10:30:00Z"
}
```

### Get Metrics History
```
GET /api/servers/{id}/metrics/history?hours=24&limit=100
Authorization: Bearer {token}
```

Returns array of metric objects

---

## Alert Endpoints

### List Alerts
```
GET /api/alerts?is_resolved=false&severity=critical
Authorization: Bearer {token}
```

**Query Parameters:**
- `is_resolved`: true/false/null
- `severity`: critical/warning/info

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "server_id": 1,
      "type": "disk_full",
      "severity": "critical",
      "title": "Disk Full",
      "message": "Disk usage exceeded 90%",
      "metric_type": "disk_usage",
      "metric_value": 95.5,
      "threshold": 90,
      "is_resolved": false,
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Resolve Alert
```
PUT /api/alerts/{id}/resolve
Authorization: Bearer {token}
```

---

## Firewall Endpoints

### List Firewall Rules
```
GET /api/servers/{id}/firewall-rules?page=1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "server_id": 1,
      "name": "Allow SSH",
      "direction": "inbound",
      "action": "allow",
      "protocol": "tcp",
      "port": 22,
      "source_ip": null,
      "is_enabled": true,
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Create Firewall Rule
```
POST /api/servers/{id}/firewall-rules
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Allow HTTP",
  "direction": "inbound",
  "action": "allow",
  "protocol": "tcp",
  "port": 80,
  "description": "Allow web traffic"
}
```

### Update Firewall Rule
```
PUT /api/servers/{id}/firewall-rules/{rule_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "is_enabled": false
}
```

### Delete Firewall Rule
```
DELETE /api/servers/{id}/firewall-rules/{rule_id}
Authorization: Bearer {token}
```

---

## Alert Rule Endpoints

### List Alert Rules
```
GET /api/alert-rules?page=1
Authorization: Bearer {token}
```

### Create Alert Rule
```
POST /api/alert-rules
Authorization: Bearer {token}
Content-Type: application/json

{
  "server_id": 1,
  "name": "High CPU Alert",
  "metric_type": "cpu_usage",
  "operator": ">",
  "threshold": 80,
  "duration_minutes": 5,
  "severity": "critical",
  "notify_email": "admin@example.com",
  "notify_webhook_url": "https://example.com/webhook"
}
```

### Update Alert Rule
```
PUT /api/alert-rules/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "threshold": 85,
  "is_enabled": true
}
```

### Delete Alert Rule
```
DELETE /api/alert-rules/{id}
Authorization: Bearer {token}
```

---

## User Endpoints

### Get Server Users
```
GET /api/servers/{id}/users
Authorization: Bearer {token}
```

**Response:**
```json
{
  "users": ["root", "ubuntu", "www-data"],
  "count": 3
}
```

### Change User Password
```
POST /api/servers/{id}/users/{username}/change-password
Authorization: Bearer {token}
Content-Type: application/json

{
  "password": "newpassword123"
}
```

**Response:**
```json
{
  "message": "Password changed successfully",
  "server": "Web Server 1",
  "username": "ubuntu"
}
```

---

## Dashboard Endpoints

### Get Dashboard Stats
```
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
  "total_servers": 3,
  "online_servers": 2,
  "offline_servers": 1,
  "active_alerts": 5,
  "critical_alerts": 2,
  "avg_cpu_usage": 35.2,
  "avg_memory_usage": 48.5
}
```

### Get Alerts Summary
```
GET /api/dashboard/alerts-summary
Authorization: Bearer {token}
```

**Response:**
```json
[
  {
    "severity": "critical",
    "count": 2
  },
  {
    "severity": "warning",
    "count": 3
  }
]
```

---

## Profile Endpoints

### Get Profile
```
GET /api/profile
Authorization: Bearer {token}
```

### Update Profile
```
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Name",
  "email": "newemail@example.com"
}
```

### Change Password
```
PUT /api/profile/password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

## Agent Endpoints

### Heartbeat
```
POST /api/agent/heartbeat
Content-Type: application/json

{
  "server_id": 1,
  "api_key": "server-api-key"
}
```

### Submit Metrics
```
POST /api/agent/metrics
Content-Type: application/json

{
  "server_id": 1,
  "api_key": "server-api-key",
  "cpu_usage": 45.2,
  "memory_usage": 8192,
  "memory_total": 16384,
  "disk_usage": 102400,
  "disk_total": 1048576,
  "load_average": 1.23
}
```

### Get Tasks
```
GET /api/agent/tasks?server_id=1&api_key=key
```

### Submit Task Result
```
POST /api/agent/task-result
Content-Type: application/json

{
  "server_id": 1,
  "task_id": "abc123",
  "status": "success",
  "result": {}
}
```

---

## cURL Examples

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@servermanager.local",
    "password": "admin123"
  }'
```

### List Servers
```bash
curl -X GET http://localhost:8000/api/servers \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Server
```bash
curl -X POST http://localhost:8000/api/servers \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Server",
    "ip_address": "192.168.1.40",
    "hostname": "server-04.local",
    "os_type": "linux",
    "ssh_username": "ubuntu"
  }'
```

### Get Metrics
```bash
curl -X GET http://localhost:8000/api/servers/1/metrics \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Alert Rule
```bash
curl -X POST http://localhost:8000/api/alert-rules \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "server_id": 1,
    "name": "High CPU",
    "metric_type": "cpu_usage",
    "operator": ">",
    "threshold": 80,
    "severity": "critical"
  }'
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 204 | No Content - Delete successful |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Missing/invalid token |
| 403 | Forbidden - No permission |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable - Validation failed |
| 429 | Too Many Requests - Rate limited |
| 500 | Server Error - Backend error |

---

## Rate Limiting

- Auth endpoints: 5 req/min
- API endpoints: 60 req/min  
- Agent endpoints: 100 req/min

Returns `429 Too Many Requests` when exceeded.

---

## Error Handling

All errors return consistent format:
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"],
    "password": ["The password must be at least 8 characters"]
  }
}
```
