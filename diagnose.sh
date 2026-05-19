#!/bin/bash

echo "========================================"
echo "Server Manager Diagnostic Report"
echo "========================================"
echo ""

cd /opt/server-manager 2>/dev/null || { echo "/opt/server-manager not found"; exit 1; }

echo "Directory: $(pwd)"
echo ""

echo "Docker Containers Status:"
docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" || echo "Docker command failed"
echo ""

echo "Listening Ports:"
netstat -tulpn 2>/dev/null | grep LISTEN || ss -tulpn 2>/dev/null | grep LISTEN || echo "Port check failed"
echo ""

echo "docker-compose.prod.yml exists:"
[ -f docker-compose.prod.yml ] && echo "Yes" || echo "No"
echo ""

echo "Recent Logs (app container):"
docker logs server-manager-app 2>/dev/null | tail -20 || echo "No logs or container not running"
echo ""

echo "Recent Logs (nginx container):"
docker logs server-manager-nginx 2>/dev/null | tail -20 || echo "No logs or container not running"
echo ""

echo "Database Status:"
docker logs server-manager-mysql 2>/dev/null | tail -10 || echo "MySQL not running"
echo ""

echo "Diagnostic complete"
