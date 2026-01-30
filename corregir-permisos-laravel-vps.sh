#!/bin/bash
# Script para corregir permisos de Laravel en el VPS
# Ejecutar en el VPS: bash corregir-permisos-laravel-vps.sh

echo "=========================================="
echo "  Corregir Permisos de Laravel"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"
WEB_USER="www-data"

# Verificar que el directorio existe
if [ ! -d "$APP_DIR" ]; then
    echo "[ERROR] El directorio $APP_DIR no existe"
    exit 1
fi

cd "$APP_DIR" || exit 1

echo "Directorio del proyecto: $APP_DIR"
echo "Usuario web: $WEB_USER"
echo ""

echo "[1/5] Creando directorios necesarios..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/storage

echo "✓ Directorios creados"

echo ""
echo "[2/5] Corrigiendo permisos de archivos..."
find . -type f -exec chmod 644 {} \;
echo "✓ Archivos: 644"

echo ""
echo "[3/5] Corrigiendo permisos de directorios..."
find . -type d -exec chmod 755 {} \;
echo "✓ Directorios: 755"

echo ""
echo "[4/5] Permisos especiales para storage y cache..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
echo "✓ Storage y cache: 775"

echo ""
echo "[5/5] Estableciendo propietario..."
chown -R $WEB_USER:$WEB_USER .
echo "✓ Propietario: $WEB_USER:$WEB_USER"

echo ""
echo "Verificando permisos de storage/framework/views..."
ls -ld storage/framework/views/
ls -la storage/framework/views/ | head -5

echo ""
echo "=========================================="
echo "  Verificación Adicional"
echo "=========================================="
echo ""

# Verificar que el usuario web puede escribir
echo "Probando escritura en storage/framework/views..."
sudo -u $WEB_USER touch storage/framework/views/test_write.txt 2>/dev/null
if [ -f "storage/framework/views/test_write.txt" ]; then
    rm -f storage/framework/views/test_write.txt
    echo "✓ El usuario $WEB_USER puede escribir en storage/framework/views"
else
    echo "✗ El usuario $WEB_USER NO puede escribir"
    echo "  Ajustando permisos adicionales..."
    chmod -R 777 storage/framework/views
    chown -R $WEB_USER:$WEB_USER storage/framework/views
fi

echo ""
echo "Verificando configuración de Apache..."
# Verificar DocumentRoot
APACHE_ROOT=$(grep -r "DocumentRoot" /etc/apache2/sites-enabled/ 2>/dev/null | grep -v "#" | head -1 | awk '{print $2}' | tr -d '"')
echo "DocumentRoot de Apache: $APACHE_ROOT"

if [ "$APACHE_ROOT" != "$APP_DIR/public" ] && [ "$APACHE_ROOT" != "${APP_DIR}/public" ]; then
    echo "⚠ ADVERTENCIA: DocumentRoot no apunta a $APP_DIR/public"
    echo "  DocumentRoot actual: $APACHE_ROOT"
    echo "  Debería ser: $APP_DIR/public"
fi

echo ""
echo "=========================================="
echo "  ¡Permisos Corregidos!"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "  1. Reiniciar Apache: systemctl restart apache2"
echo "  2. Limpiar cachés: php artisan cache:clear"
echo "  3. Verificar que el sitio funciona"
echo ""

