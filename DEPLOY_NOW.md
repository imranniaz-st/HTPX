# Quick Deployment to 167.99.13.48 - HTTP/HTTPS

## Step 1: Copy Files to Server (from your machine)

```powershell
# Open PowerShell in your project directory
cd C:\Users\kkk\Documents\System\ServerManager

# Copy entire project to server
scp -r . root@167.99.13.48:/opt/server-manager/
```

If you get key issues:

```bash
# Verify SSH works first
ssh root@167.99.13.48 "echo 'SSH OK'"
```

## Step 2: Run Deployment on Server

```bash
# SSH into your server
ssh root@167.99.13.48

# Run the quick deployment script
bash /opt/server-manager/quick-deploy.sh
```

That's it! The script will:
- ✅ Check/install Docker & Docker Compose
- ✅ Create .env with secure passwords
- ✅ Generate SSL certificates  
- ✅ Start all services (app, mysql, redis, nginx)
- ✅ Run database migrations
- ✅ Display access URL

## Step 3: Access Your Application

**After deployment completes, you'll see:**

```
✓ Deployment Complete!

Access your application:
  URL: http://167.99.13.48
  Default Login:
    Email: admin@servermanager.local
    Password: admin123
```

Open browser:
```
http://167.99.13.48
```

---

## Alternative: One-Line Copy & Deploy

If SCP doesn't work, do this on the server:

```bash
ssh root@167.99.13.48

# Create directory
mkdir -p /opt/server-manager
cd /opt/server-manager

# Copy your files manually or use rsync
# Then just run:
bash /opt/server-manager/quick-deploy.sh
```

---

## What Gets Deployed

| Component | Status |
|-----------|--------|
| **Nginx** (HTTP/HTTPS) | Port 80, 443 |
| **Laravel API** | Port 8000 (internal) |
| **MySQL Database** | Auto-created & seeded |
| **Redis Cache** | Auto-configured |
| **WebSocket Server** | Auto-running on 6001 |
| **SSL Certificates** | Auto-generated (self-signed) |

---

## Troubleshooting

### Can't connect to server?
```bash
ssh -v root@167.99.13.48
# Check if SSH port is open
```

### Files won't copy?
```bash
# Try from server's end - create directory first
ssh root@167.99.13.48 "mkdir -p /opt/server-manager"

# Then use rsync
rsync -avz . root@167.99.13.48:/opt/server-manager/
```

### Docker not installing?
```bash
# SSH to server and try manually
ssh root@167.99.13.48
apt update
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
```

### Services won't start?
```bash
ssh root@167.99.13.48
cd /opt/server-manager

# Check status
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f
```

---

## Verify Deployment

```bash
# SSH to server
ssh root@167.99.13.48

# Check all services running
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml ps

# Should show 5 containers: app, mysql, redis, websocket, nginx
```

---

## Next Steps

1. ✅ Open http://167.99.13.48 in browser
2. ✅ Login with admin/admin123
3. ✅ Change password immediately
4. ✅ Add your servers using the guide in ADD_NEW_SERVER_GUIDE.md
5. ✅ Configure monitoring alerts
6. ✅ Set up backup strategy

---

## Need to Update?

When you make code changes:

```bash
# From your machine
cd C:\Users\kkk\Documents\System\ServerManager

# Copy updated files
scp -r . root@167.99.13.48:/opt/server-manager/

# SSH and rebuild
ssh root@167.99.13.48
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml up -d --build
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate
```

---

Ready to deploy? Run the command in Step 1 and 2! 🚀
