#!/bin/bash
# Script para sincronizar el VPS con Git sin perder cambios importantes
# Ejecutar en el VPS: bash sincronizar-vps-desde-git.sh

echo "=========================================="
echo "  Sincronizar VPS con Git"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"
cd "$APP_DIR" || exit 1

echo "[1/5] Verificando estado de Git..."
git status --short | head -20

echo ""
echo "[2/5] Guardando cambios locales importantes..."
# Guardar archivos importantes antes de resetear
if [ -f ".env" ]; then
    cp .env .env.backup
    echo "✓ .env respaldado"
fi

echo ""
echo "[3/5] Descartando cambios locales (archivos eliminados)..."
# Descartar cambios de archivos eliminados
git checkout -- .

echo ""
echo "[4/5] Limpiando archivos sin seguimiento (opcional)..."
# NO eliminar .env ni archivos importantes
git clean -fd --exclude=.env --exclude=storage/logs/*.log

echo ""
echo "[5/5] Haciendo pull desde Git..."
git pull origin main

if [ $? -eq 0 ]; then
    echo "✓ Pull completado exitosamente"
    
    # Restaurar .env si existe backup
    if [ -f ".env.backup" ] && [ ! -f ".env" ]; then
        mv .env.backup .env
        echo "✓ .env restaurado"
    fi
else
    echo "✗ Error al hacer pull"
    echo "  Intenta: git fetch origin main"
    echo "  Luego: git reset --hard origin/main"
    exit 1
fi

echo ""
echo "=========================================="
echo "  ¡Sincronización Completada!"
echo "=========================================="
echo ""
echo "Verifica el estado:"
echo "  git status"
echo ""

