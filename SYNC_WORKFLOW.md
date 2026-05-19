# Sync Workflow: Local → Server

When you make changes locally, here's how to deploy them to production:

## Files Created for Syncing

### 1. **sync-to-server.sh** (Run from your local machine)
Syncs your local code changes to the server

### 2. **server-pull-update.sh** (Run on server)
Pulls changes and rebuilds containers on the server

---

## Quick Sync Workflow

### Option 1: Sync + Auto Rebuild (Fastest)

```bash
# From your local machine, in project directory
cd C:\Users\kkk\Documents\System\ServerManager

# Sync code AND rebuild containers
bash sync-to-server.sh 167.99.13.48 rebuild
```

**What it does:**
1. Copies backend code to server
2. Copies frontend code to server  
3. Copies docker configs to server
4. Rebuilds all Docker containers
5. Runs database migrations
6. Tests application is responding

**Time:** 3-5 minutes

---

### Option 2: Sync Only (Manual Rebuild)

```bash
# From your local machine
bash sync-to-server.sh 167.99.13.48
```

Then rebuild manually on server:
```bash
# SSH to server
ssh root@167.99.13.48
cd /opt/server-manager
bash server-pull-update.sh
```

---

### Option 3: Manual rsync (For specific files)

```bash
# Sync only backend
rsync -avz --delete backend/ root@167.99.13.48:/opt/server-manager/backend/

# Sync only frontend
rsync -avz --delete frontend/ root@167.99.13.48:/opt/server-manager/frontend/

# Sync docker configs
rsync -avz docker-compose.prod.yml docker/ root@167.99.13.48:/opt/server-manager/
```

Then rebuild:
```bash
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml up -d --build"
```

---

## What Gets Synced

### Backend
- `backend/app/` - PHP models, controllers, services
- `backend/routes/` - API routes
- `backend/database/` - Migrations, seeders
- `backend/config/` - Configuration files
- **Excluded:** `vendor/`, `storage/logs/*`, `bootstrap/cache/*`

### Frontend
- `frontend/src/` - Vue components, stores, views
- `frontend/public/` - Static assets
- **Excluded:** `node_modules/`, `dist/`, `build/`

### Docker Configs
- `docker-compose.prod.yml`
- `docker/nginx/` - Nginx configs
- `docker/certs/` - SSL certificates

### NOT Synced (Preserved on Server)
- `.env` - Environment variables (production secrets)
- `storage/` - User uploads and logs
- `bootstrap/cache/` - Cache files
- `vendor/` - PHP dependencies
- `node_modules/` - NPM dependencies

---

## Complete Local Development → Production Workflow

### 1. Make Code Changes Locally
```bash
# Edit files in VS Code
# Test locally
cd backend && php artisan serve
cd frontend && npm run dev
```

### 2. Commit Changes
```bash
git add .
git commit -m "Feature: Add new server logs feature"
git push origin main
```

### 3. Deploy to Production
```bash
# Option A: Sync + Rebuild (recommended)
bash sync-to-server.sh 167.99.13.48 rebuild

# Option B: Just sync, rebuild manually
bash sync-to-server.sh 167.99.13.48
```

### 4. Verify Deployment
```bash
# Check application
curl http://167.99.13.48

# Or view logs
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml logs -f"
```

---

## Common Sync Scenarios

### Scenario 1: Changed PHP Code (Models, Controllers)
```bash
bash sync-to-server.sh 167.99.13.48 rebuild
# Rebuilds backend container and runs migrations
```

### Scenario 2: Changed Vue Components (Frontend)
```bash
bash sync-to-server.sh 167.99.13.48 rebuild
# Rebuilds frontend and regenerates dist/
```

### Scenario 3: Changed Only Configurations
```bash
rsync -avz docker-compose.prod.yml docker/ root@167.99.13.48:/opt/server-manager/
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml restart"
```

### Scenario 4: Database Migrations Only
```bash
bash sync-to-server.sh 167.99.13.48
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate"
```

### Scenario 5: Emergency Rollback
```bash
# SSH to server
ssh root@167.99.13.48

# Stop current containers
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml down

# Restore from git (if using git)
git checkout HEAD~1  # Go back 1 commit

# Rebuild
docker-compose -f docker-compose.prod.yml up -d --build
```

---

## Sync Performance

| Operation | Time | Notes |
|-----------|------|-------|
| Sync only | 30s | Just copies files |
| Rebuild only | 2-3 min | Rebuilds Docker images |
| Full sync + rebuild | 3-5 min | Includes migrations |
| Pull + update | 3-5 min | Same as above |

---

## 🆘 Troubleshooting

### Sync Fails (Permission Denied)
```bash
# Ensure SSH key is set up
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa
ssh-copy-id root@167.99.13.48
```

### Docker Build Fails
```bash
# SSH to server and check logs
ssh root@167.99.13.48
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml logs app
```

### Application Not Responding After Sync
```bash
# Wait a bit (containers take time to start)
sleep 30

# Check container status
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f

# Restart if needed
docker-compose -f docker-compose.prod.yml restart
```

### Database Migration Errors
```bash
# Check migrations on server
ssh root@167.99.13.48
cd /opt/server-manager
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate:status

# Rollback if needed
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate:rollback
```

---

## Recommended Workflow

```bash
# 1. Make changes locally
# 2. Test locally (php artisan serve, npm run dev)
# 3. Commit to git
# 4. Push to production

# Production deployment (3 commands)
cd C:\Users\kkk\Documents\System\ServerManager
bash sync-to-server.sh 167.99.13.48 rebuild

# That's it! Application updates automatically
```

---

## Security Notes

- `.env` file is **never** synced (keeps production secrets safe)
- SSH keys required for syncing (use ssh-copy-id)
- Recommended: Use git pull on server instead of rsync
- Always test locally before deploying to production

---

## Additional Commands

### Just Copy Files (No Rebuild)
```bash
bash sync-to-server.sh 167.99.13.48
```

### Rebuild Only (Files Already Synced)
```bash
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml up -d --build"
```

### View Live Logs
```bash
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml logs -f app"
```

### Force Full Rebuild
```bash
ssh root@167.99.13.48 "cd /opt/server-manager && docker-compose -f docker-compose.prod.yml build --no-cache"
```

---

**Ready to deploy?** 
```bash
bash sync-to-server.sh 167.99.13.48 rebuild
```
