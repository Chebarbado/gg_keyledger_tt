#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

# пишем ключевые переменные из окружения Docker в .env
set_env() {
    key="$1"
    value="$2"
    if [ -z "$value" ]; then
        return 0
    fi
    if grep -q "^${key}=" .env; then
        # разделитель | — чтобы / в APP_KEY не ломал sed
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

if [ -n "$APP_KEY" ]; then
    set_env APP_KEY "$APP_KEY"
elif ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

set_env APP_ENV "${APP_ENV:-local}"
set_env APP_DEBUG "${APP_DEBUG:-true}"
set_env APP_URL "${APP_URL:-http://localhost:8000}"
set_env ADMIN_TOKEN "${ADMIN_TOKEN:-secret}"
set_env DB_CONNECTION "${DB_CONNECTION:-sqlite}"
set_env LOG_CHANNEL "${LOG_CHANNEL:-stderr}"

mkdir -p database storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
touch database/database.sqlite
chmod -R ug+rwx storage bootstrap/cache database

php artisan config:clear

php artisan migrate --force

if [ "${RUN_SEED:-true}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "App ready: ${APP_URL:-http://localhost:8000}"
echo "Admin: ${APP_URL:-http://localhost:8000}/admin/orders?token=${ADMIN_TOKEN:-secret}"

exec "$@"
