#!/bin/bash

echo "=========================================="
echo "Server Manager - Status Check"
echo "=========================================="
cd /opt/server-manager/ServerManager

echo ""
echo "📁 Working Directory:"
pwd

echo ""
echo "📂 Directory Contents:"
ls -la | head -20

echo ""
echo "🐳 Docker Containers:"
docker-compose -f docker-compose.prod.yml ps

echo ""
echo "🔌 Listening Ports:"
netstat -tulpn 2>/dev/null | grep LISTEN | grep -E "8001|8000|3306|6379" || ss -tulpn | grep LISTEN | grep -E "8001|8000|3306|6379"

echo ""
echo "📝 Application Logs (last 30 lines):"
docker-compose -f docker-compose.prod.yml logs app 2>/dev/null | tail -30

echo ""
echo "✅ Status check complete"
