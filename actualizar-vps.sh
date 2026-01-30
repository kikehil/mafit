#!/usr/bin/env bash
set -e

APP_DIR="/var/www/html/mafit"
WEB_USER="www-data"

cd "$APP_DIR"

echo "==> Validando .env"
if [ ! -f ".env" ]; then
  echo "No existe .env. Copiando .env.example..."
  cp .env.example .env
  echo "IMPORTANTE: edita .env con tus credenciales (DB, APP_URL, etc.)"
fi

echo "==> Composer install"
composer install --no-dev --optimize-autoloader

echo "==> App key (si falta)"
php artisan key:generate --force || true

echo "==> Migraciones"
php artisan migrate --force || true

echo "==> NPM build (si existe package.json)"
if [ -f "package.json" ]; then
  npm ci
  npm run build
fi

echo "==> Storage link"
php artisan storage:link || true

echo "==> Cache optimize"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Permisos"
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
chown -R $WEB_USER:$WEB_USER storage bootstrap/cache || true
chmod -R ug+rw storage bootstrap/cache || true

echo "✅ Listo"




