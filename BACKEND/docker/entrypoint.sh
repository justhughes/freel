#!/bin/sh
set -e

# ─── Step 1: Pastikan .env ada ───────────────────────────────
echo "==> Setting up .env file..."
if [ ! -f /var/www/html/.env ]; then
    echo "    .env tidak ada, membuat dari .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Tulis environment variables dari Docker ke .env
# Sehingga artisan command bisa membacanya
{
    echo "APP_NAME=Contify"
    echo "APP_ENV=${APP_ENV:-production}"
    echo "APP_KEY=${APP_KEY:-}"
    echo "APP_DEBUG=${APP_DEBUG:-false}"
    echo "APP_URL=${APP_URL:-http://localhost:8000}"
    echo "APP_TIMEZONE=Asia/Jakarta"
    echo ""
    echo "DB_CONNECTION=mysql"
    echo "DB_HOST=${DB_HOST:-db}"
    echo "DB_PORT=${DB_PORT:-3306}"
    echo "DB_DATABASE=${DB_DATABASE:-contify}"
    echo "DB_USERNAME=${DB_USERNAME:-contify_user}"
    echo "DB_PASSWORD=${DB_PASSWORD:-contify_pass_2024}"
    echo ""
    echo "SESSION_DRIVER=file"
    echo "CACHE_STORE=file"
    echo "QUEUE_CONNECTION=sync"
    echo "FILESYSTEM_DISK=public"
    echo "LOG_CHANNEL=stderr"
    echo "LOG_LEVEL=error"
    echo ""
    echo "BROADCAST_CONNECTION=log"
    echo "MAIL_MAILER=log"
} > /var/www/html/.env

# ─── Step 2: Tunggu MySQL siap ───────────────────────────────
echo "==> Waiting for MySQL..."
until mysqladmin ping -h "${DB_HOST:-db}" -u "${DB_USERNAME:-contify_user}" -p"${DB_PASSWORD:-contify_pass_2024}" --silent 2>/dev/null; do
    echo "    MySQL not ready yet, waiting 3 seconds..."
    sleep 3
done
echo "MySQL is ready!"

# ─── Step 3: Generate APP_KEY jika belum ada ─────────────────
echo "==> Generating app key if not set..."
if [ -z "${APP_KEY}" ] || [ "${APP_KEY}" = "" ]; then
    php artisan key:generate --force
else
    # Key sudah diberikan via env, tulis ke .env
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
fi

# ─── Step 4: Migrasi ─────────────────────────────────────────
echo "==> Running migrations..."
php artisan migrate --force

# ─── Step 5: Seed jika tabel users kosong ────────────────────
echo "==> Seeding database if empty..."
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "    Tabel kosong, menjalankan seeder..."
    php artisan db:seed --force
else
    echo "    Database sudah ada data ($USER_COUNT users), skip seeding."
fi

# ─── Step 6: Storage & cache ─────────────────────────────────
echo "==> Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Clearing & caching config..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
