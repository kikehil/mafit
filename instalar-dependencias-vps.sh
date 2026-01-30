#!/bin/bash
# Script para instalar dependencias en el VPS
# Ejecutar en el VPS: bash instalar-dependencias-vps.sh

echo "=========================================="
echo "  Instalar Dependencias en VPS"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"
cd "$APP_DIR" || exit 1

echo "[1/4] Verificando Composer..."
if command -v composer &> /dev/null; then
    composer --version
    echo "✓ Composer está instalado"
else
    echo "✗ Composer NO está instalado"
    echo "  Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    echo "✓ Composer instalado"
fi

echo ""
echo "[2/4] Verificando archivo composer.json..."
if [ -f "composer.json" ]; then
    echo "✓ composer.json existe"
else
    echo "✗ composer.json NO existe"
    exit 1
fi

echo ""
echo "[3/4] Instalando dependencias de Composer..."
echo "  Esto puede tardar varios minutos..."
composer install --no-dev --optimize-autoloader

if [ $? -eq 0 ]; then
    echo "✓ Dependencias de Composer instaladas"
else
    echo "✗ Error al instalar dependencias"
    exit 1
fi

echo ""
echo "[4/4] Verificando que vendor/autoload.php existe..."
if [ -f "vendor/autoload.php" ]; then
    echo "✓ vendor/autoload.php existe"
    echo "✓ ¡Todo listo!"
else
    echo "✗ vendor/autoload.php NO existe"
    echo "  Revisa los errores anteriores"
    exit 1
fi

echo ""
echo "=========================================="
echo "  ¡Dependencias Instaladas!"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "  1. Verificar .env está configurado"
echo "  2. Ejecutar migraciones: php artisan migrate"
echo "  3. Compilar assets: npm install && npm run build"
echo ""

