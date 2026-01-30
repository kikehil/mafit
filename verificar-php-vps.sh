#!/bin/bash
# Script para verificar versión de PHP en el VPS
# Ejecutar en el VPS: bash verificar-php-vps.sh

echo "=========================================="
echo "  Verificar PHP en VPS"
echo "=========================================="
echo ""

echo "Versión de PHP:"
php -v

echo ""
echo "Versión numérica:"
php -r "echo 'PHP ' . PHP_VERSION . PHP_EOL;"

echo ""
echo "Versión requerida por el proyecto: PHP >= 8.1"
REQUIRED="8.1"
CURRENT=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")

if [ "$(php -r "echo version_compare('$CURRENT', '$REQUIRED', '>=');")" = "1" ]; then
    echo "✓ PHP $CURRENT cumple con el requisito (>= $REQUIRED)"
else
    echo "✗ PHP $CURRENT NO cumple con el requisito (>= $REQUIRED)"
    echo "  Necesitas actualizar PHP"
    echo "  Ejecuta: bash actualizar-php-vps.sh"
fi

echo ""
echo "Extensiones PHP instaladas:"
php -m | sort

echo ""
echo "Extensiones requeridas por Laravel:"
REQUIRED_EXT=("pdo" "pdo_mysql" "mbstring" "xml" "curl" "zip" "gd" "bcmath" "intl" "opcache")
for ext in "${REQUIRED_EXT[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "  ✓ $ext"
    else
        echo "  ✗ $ext (FALTA)"
    fi
done

echo ""
echo "Ruta de PHP:"
which php

echo ""
echo "Ruta de php.ini:"
php --ini | grep "Loaded Configuration File"

echo ""

