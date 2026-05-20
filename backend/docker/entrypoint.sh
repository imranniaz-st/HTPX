#!/bin/sh
set -e

cd /app

mkdir -p \
    bootstrap/cache \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/ssh

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -f artisan ]; then
    php artisan key:generate --force --ansi >/dev/null 2>&1 || true
    php artisan package:discover --ansi || true

    if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
        attempts=0
        until php artisan migrate --force --ansi; do
            attempts=$((attempts + 1))
            if [ "$attempts" -ge 10 ]; then
                echo "Database migrations failed after $attempts attempts"
                exit 1
            fi
            echo "Waiting for database before running migrations..."
            sleep 3
        done
    fi
fi

exec "$@"
