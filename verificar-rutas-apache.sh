#!/bin/bash
# Script para verificar y corregir rutas de Apache
# Ejecutar en el VPS: bash verificar-rutas-apache.sh

echo "=========================================="
echo "  Verificar Rutas de Apache"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"
DOMAIN="mafit.regiontamaulipas.com.mx"

echo "Buscando configuración de Apache para $DOMAIN..."
echo ""

# Buscar configuración
CONFIG_FILES=$(grep -r "$DOMAIN\|mafit" /etc/apache2/sites-enabled/ 2>/dev/null | cut -d: -f1 | sort -u)

if [ -z "$CONFIG_FILES" ]; then
    echo "No se encontró configuración específica para $DOMAIN"
    echo "Buscando todas las configuraciones..."
    CONFIG_FILES="/etc/apache2/sites-enabled/*.conf"
fi

for config in $CONFIG_FILES; do
    if [ -f "$config" ]; then
        echo "Archivo: $config"
        echo "----------------------------------------"
        grep -E "ServerName|DocumentRoot|Directory" "$config" | grep -v "^#" | head -10
        echo ""
    fi
done

echo "Verificando DocumentRoot actual..."
ACTUAL_ROOT=$(grep -r "DocumentRoot" /etc/apache2/sites-enabled/ 2>/dev/null | grep -v "#" | head -1 | awk '{print $2}' | tr -d '"')
echo "DocumentRoot: $ACTUAL_ROOT"

echo ""
echo "Verificando que existe $APP_DIR/public/index.php..."
if [ -f "$APP_DIR/public/index.php" ]; then
    echo "✓ $APP_DIR/public/index.php existe"
else
    echo "✗ $APP_DIR/public/index.php NO existe"
    echo "  Buscando index.php..."
    find "$APP_DIR" -name "index.php" -type f 2>/dev/null
fi

echo ""
echo "=========================================="
echo "  Recomendación"
echo "=========================================="
echo ""
if [ "$ACTUAL_ROOT" != "$APP_DIR/public" ] && [ "$ACTUAL_ROOT" != "${APP_DIR}/public" ]; then
    echo "⚠ DocumentRoot debe apuntar a: $APP_DIR/public"
    echo ""
    echo "Para corregir, edita la configuración de Apache:"
    echo "  nano /etc/apache2/sites-available/mafit.conf"
    echo ""
    echo "O crea una nueva configuración:"
    echo "  cat > /etc/apache2/sites-available/mafit.conf << 'EOF'"
    echo "  <VirtualHost *:80>"
    echo "      ServerName $DOMAIN"
    echo "      DocumentRoot $APP_DIR/public"
    echo "      <Directory $APP_DIR/public>"
    echo "          AllowOverride All"
    echo "          Require all granted"
    echo "      </Directory>"
    echo "  </VirtualHost>"
    echo "  EOF"
    echo ""
    echo "  a2ensite mafit.conf"
    echo "  systemctl restart apache2"
fi

echo ""





