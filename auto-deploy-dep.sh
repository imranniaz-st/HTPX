#!/usr/bin/env bash
# update
set -euo pipefail

DEPLOY_PATH="${DEPLOY_PATH:-/opt/server-manager}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-Deploye}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.prod.yml}"

if command -v docker >/dev/null 2>&1 && docker compose version >/dev/null 2>&1; then
    COMPOSE_CMD=(docker compose)
elif command -v docker-compose >/dev/null 2>&1; then
    COMPOSE_CMD=(docker-compose)
else
    echo "Docker Compose is not installed."
    exit 1
fi

if [ ! -d "$DEPLOY_PATH/.git" ]; then
    echo "Git repository not found at: $DEPLOY_PATH"
    exit 1
fi

echo "Deploy path: $DEPLOY_PATH"
echo "Branch: $DEPLOY_BRANCH"
echo "Compose file: $COMPOSE_FILE"
echo

cd "$DEPLOY_PATH"

cleanup_container() {
    local container_name="$1"
    if docker ps -a --format '{{.Names}}' | grep -qx "$container_name"; then
        echo "Removing stale container: $container_name"
        docker rm -f "$container_name" >/dev/null 2>&1 || true
    fi
}

echo "Fetching latest code from origin/$DEPLOY_BRANCH..."
git fetch origin "$DEPLOY_BRANCH"
git checkout "$DEPLOY_BRANCH"
git reset --hard "origin/$DEPLOY_BRANCH"
git clean -fd

echo
echo "Removing any stale containers from previous deploys..."
cleanup_container "server-manager-app"
cleanup_container "server-manager-mysql"
cleanup_container "server-manager-redis"
cleanup_container "server-manager-websocket"
cleanup_container "server-manager-nginx"

echo
echo "Rebuilding containers..."
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" down --remove-orphans >/dev/null 2>&1 || true
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" up -d --build --remove-orphans

echo
echo "Waiting for services to become ready..."
sleep 10

echo
echo "Running Laravel cache clear..."
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" exec -T app php artisan optimize:clear || true

echo
echo "Running database migrations..."
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" exec -T app php artisan migrate --force

echo
echo "Seeding data if needed..."
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" exec -T app php artisan db:seed --force || true

echo
echo "Deployment finished. Current status:"
"${COMPOSE_CMD[@]}" -f "$COMPOSE_FILE" ps
