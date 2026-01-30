#!/bin/bash
# Script simple para solucionar error 403 en Apache
# Ejecutar en el VPS: bash fix-403.sh

echo "=========================================="
echo "  Solucionar Error 403 Forbidden"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"

cd "$APP_DIR" || exit 1

echo "[1/4] Corrigiendo permisos de archivos..."
find . -type f -exec chmod 644 {} \;
echo "✓ Archivos: 644"

echo "[2/4] Corrigiendo permisos de directorios..."
find . -type d -exec chmod 755 {} \;
echo "✓ Directorios: 755"

echo "[3/4] Permisos especiales para storage y cache..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
echo "✓ Storage/Cache: 775"

echo "[4/4] Estableciendo propietario..."
chown -R www-data:www-data .
echo "✓ Propietario: www-data"

echo ""
echo "Verificando directorio public..."
if [ -d "public" ] && [ -f "public/index.php" ]; then
    echo "✓ public/index.php existe"
    ls -ld public/
else
    echo "✗ ERROR: public/index.php no existe"
    exit 1
fi

echo ""
echo "Verificando configuración de Apache..."
APACHE_CONFIG=$(grep -r "DocumentRoot.*mafit" /etc/apache2/sites-enabled/ 2>/dev/null | head -n 1)
if [ -n "$APACHE_CONFIG" ]; then
    echo "Configuración encontrada:"
    echo "$APACHE_CONFIG"
else
    echo "⚠ ADVERTENCIA: No se encontró configuración específica para mafit"
    echo "  Verificando configuración por defecto..."
    grep -i "DocumentRoot" /etc/apache2/sites-enabled/*.conf 2>/dev/null | head -n 1
fi

echo ""
echo "=========================================="
echo "  Reiniciando Apache..."
echo "=========================================="
systemctl restart apache2

if [ $? -eq 0 ]; then
    echo "✓ Apache reiniciado correctamente"
else
    echo "✗ Error al reiniciar Apache"
    exit 1
fi

echo ""
echo "=========================================="
echo "  ¡Listo!"
echo "=========================================="
echo ""
echo "Prueba acceder a: http://mafit.regiontamaulipas.com.mx"
echo ""
echo "Si persiste el error, verifica:"
echo "  1. tail -f /var/log/apache2/error.log"
echo "  2. Verificar que DocumentRoot apunte a: $APP_DIR/public"
echo ""


