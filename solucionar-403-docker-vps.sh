#!/usr/bin/env bash
# Script para solucionar error 403 en VPS con Docker
# Ejecutar en el VPS: bash solucionar-403-docker-vps.sh

set -e

APP_DIR="/var/www/html/mafit"
WEB_USER="www-data"

echo "=========================================="
echo "  Solucionar Error 403 - Docker VPS"
echo "=========================================="
echo ""
echo "Directorio: $APP_DIR"
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -d "$APP_DIR" ]; then
    echo "[ERROR] El directorio $APP_DIR no existe"
    exit 1
fi

cd "$APP_DIR"

# Verificar Docker
echo "[1/5] Verificando Docker..."
if ! command -v docker &> /dev/null; then
    echo "[ERROR] Docker no esta instalado"
    exit 1
fi
echo "[OK] Docker esta instalado"
echo ""

# Verificar docker-compose.yml
echo "[2/5] Verificando docker-compose.yml..."
if [ ! -f "docker-compose.yml" ]; then
    echo "[ERROR] docker-compose.yml no existe"
    exit 1
fi
echo "[OK] docker-compose.yml existe"
echo ""

# Corregir permisos
echo "[3/5] Corrigiendo permisos..."
echo "  Estableciendo permisos de archivos (644)..."
find . -type f -exec chmod 644 {} \;

echo "  Estableciendo permisos de directorios (755)..."
find . -type d -exec chmod 755 {} \;

echo "  Permisos especiales para storage y cache (775)..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "  Estableciendo propietario..."
chown -R $WEB_USER:$WEB_USER . 2>/dev/null || chown -R 33:33 . 2>/dev/null || true

echo "[OK] Permisos corregidos"
echo ""

# Verificar configuracion de Nginx en Docker
echo "[4/5] Verificando configuracion de Nginx..."
if [ -f "docker/nginx/default.conf" ]; then
    echo "  Verificando root en configuracion de Nginx..."
    ROOT_LINE=$(grep -i "root" docker/nginx/default.conf | head -n 1)
    echo "  $ROOT_LINE"
    
    if echo "$ROOT_LINE" | grep -q "/var/www/public"; then
        echo "[OK] Root de Nginx esta correcto"
    else
        echo "[ADVERTENCIA] Root de Nginx puede no estar correcto"
        echo "  Deberia ser: root /var/www/public;"
    fi
else
    echo "[ADVERTENCIA] No se encuentra docker/nginx/default.conf"
fi
echo ""

# Reiniciar contenedores
echo "[5/5] Reiniciando contenedores Docker..."
echo "  Deteniendo contenedores..."
docker compose down

echo "  Iniciando contenedores..."
docker compose up -d

echo ""
echo "Estado de contenedores:"
docker compose ps

echo ""
echo "=========================================="
echo "  Resumen"
echo "=========================================="
echo ""
echo "Acciones completadas:"
echo "  Permisos corregidos"
echo "  Contenedores reiniciados"
echo ""
echo "Próximos pasos:"
echo "1. Verificar logs:"
echo "   docker compose logs nginx"
echo "   docker compose logs app"
echo ""
echo "2. Verificar que los contenedores esten corriendo:"
echo "   docker compose ps"
echo ""
echo "3. Verificar puertos expuestos:"
echo "   docker compose ps | grep -E 'PORTS|mafit'"
echo ""






