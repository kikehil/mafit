#!/bin/bash
# Script para corregir permisos de Git en el VPS
# Ejecutar en el VPS: bash fix-git-permissions-vps.sh

echo "=========================================="
echo "  Corregir Permisos de Git en VPS"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"

cd "$APP_DIR" || exit 1

echo "[1/3] Agregando directorio como seguro en Git..."
git config --global --add safe.directory "$APP_DIR"
echo "✓ Directorio agregado como seguro"

echo ""
echo "[2/3] Verificando configuración de Git..."
git config --global --get-regexp safe.directory | grep "$APP_DIR"
if [ $? -eq 0 ]; then
    echo "✓ Configuración verificada"
else
    echo "⚠ No se pudo verificar la configuración"
fi

echo ""
echo "[3/3] Corrigiendo permisos del directorio .git..."
chown -R root:root .git 2>/dev/null || chown -R $(whoami):$(whoami) .git
chmod -R 755 .git
echo "✓ Permisos de .git corregidos"

echo ""
echo "=========================================="
echo "  ¡Listo!"
echo "=========================================="
echo ""
echo "Ahora puedes usar git pull:"
echo "  git pull origin main"
echo ""





