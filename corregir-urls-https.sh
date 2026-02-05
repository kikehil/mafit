#!/bin/bash

# Script para corregir el problema de URLs con puerto :443
# Este script debe ejecutarse en el servidor VPS

echo "=========================================="
echo "CORREGIR URLs HTTPS (SIN PUERTO :443)"
echo "=========================================="
echo ""

PROJECT_DIR="/opt/mafit"

if [ ! -d "$PROJECT_DIR" ]; then
    echo "ERROR: No se encontró el directorio del proyecto: $PROJECT_DIR"
    exit 1
fi

cd "$PROJECT_DIR"

echo "[1/5] Verificando archivos modificados..."
if [ ! -f "app/Providers/AppServiceProvider.php" ]; then
    echo "ERROR: No se encontró AppServiceProvider.php"
    exit 1
fi
if [ ! -f "app/Http/Middleware/ForceHttps.php" ]; then
    echo "ERROR: No se encontró ForceHttps.php"
    exit 1
fi
if [ ! -f "resources/views/layouts/app.blade.php" ]; then
    echo "ERROR: No se encontró app.blade.php"
    exit 1
fi
echo "✓ Archivos encontrados"
echo ""

echo "[2/5] Verificando configuración de .env..."
if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    echo "   APP_URL actual: $APP_URL"
    
    # Remover puerto 443 si está presente
    if echo "$APP_URL" | grep -q ":443"; then
        echo "   ⚠️  APP_URL contiene puerto :443, corrigiendo..."
        sed -i 's|APP_URL=.*|APP_URL=https://mafit.regiontamaulipas.com.mx|g' .env
        echo "   ✓ APP_URL corregido"
    else
        echo "   ✓ APP_URL está correcto"
    fi
    
    # Asegurar que use HTTPS
    if echo "$APP_URL" | grep -q "^http://"; then
        echo "   ⚠️  APP_URL usa HTTP, cambiando a HTTPS..."
        sed -i 's|APP_URL=http://|APP_URL=https://|g' .env
        echo "   ✓ APP_URL cambiado a HTTPS"
    fi
else
    echo "   ⚠️  No se encontró archivo .env"
fi
echo ""

echo "[3/5] Limpiando caché de Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo "✓ Caché limpiado"
echo ""

echo "[4/5] Regenerando caché de configuración..."
php artisan config:cache
echo "✓ Caché de configuración regenerado"
echo ""

echo "[5/5] Verificando configuración final..."
APP_URL_FINAL=$(php artisan tinker --execute="echo config('app.url');" 2>/dev/null | tail -n 1)
echo "   APP_URL en Laravel: $APP_URL_FINAL"

if echo "$APP_URL_FINAL" | grep -q ":443"; then
    echo "   ⚠️  ADVERTENCIA: APP_URL aún contiene :443"
    echo "   Esto puede requerir reiniciar PHP-FPM"
else
    echo "   ✓ APP_URL está correcto (sin puerto :443)"
fi
echo ""

echo "=========================================="
echo "PROCESO COMPLETADO"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "1. Reiniciar PHP-FPM: sudo systemctl restart php8.3-fpm"
echo "2. Verificar que nginx esté configurado correctamente"
echo "3. Probar la aplicación en el navegador"
echo ""
echo "Para verificar la configuración de nginx:"
echo "   sudo nginx -t"
echo "   sudo systemctl status nginx"
echo ""

