#!/usr/bin/env bash
# update
set -euo pipefail

DEPLOY_PATH="${DEPLOY_PATH:-/opt/server-manager}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-dep}"
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

echo "Fetching latest code from origin/$DEPLOY_BRANCH..."
git fetch origin "$DEPLOY_BRANCH"
git checkout "$DEPLOY_BRANCH"
git reset --hard "origin/$DEPLOY_BRANCH"
git clean -fd

echo
echo "Rebuilding containers..."
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
