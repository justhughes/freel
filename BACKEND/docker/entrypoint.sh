#!/bin/sh
set -e

echo "==> Waiting for MySQL..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "MySQL not ready yet, waiting 3 seconds..."
    sleep 3
done
echo "MySQL is ready!"

echo "==> Generating app key if not set..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Running seeders if fresh install..."
php artisan db:seed --force 2>/dev/null || true

echo "==> Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Clearing & caching config..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
