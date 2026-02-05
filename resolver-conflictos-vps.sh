#!/bin/bash
# Script para resolver conflictos al actualizar desde Git en el VPS
# Ejecutar en el VPS: bash resolver-conflictos-vps.sh

echo "=========================================="
echo "  Resolver Conflictos en VPS"
echo "=========================================="
echo ""

cd /var/www/html/mafit || exit 1

# 1. Respaldar .env
echo "[1/5] Respaldando .env..."
if [ -f ".env" ]; then
    cp .env .env.backup
    echo "✓ .env respaldado"
else
    echo "⚠ No existe .env"
fi
echo ""

# 2. Ver cambios locales
echo "[2/5] Verificando cambios locales..."
echo "Archivos modificados:"
git status --short
echo ""

# 3. Guardar cambios locales en stash (opcional, por si acaso)
echo "[3/5] Guardando cambios locales en stash..."
git stash push -m "Cambios locales antes de actualizar - $(date)"
echo "✓ Cambios guardados en stash"
echo ""

# 4. Eliminar archivo conflictivo sin seguimiento
echo "[4/5] Eliminando archivo conflictivo..."
if [ -f "diagnosticar-login-vps.php" ]; then
    rm -f diagnosticar-login-vps.php
    echo "✓ Archivo eliminado"
else
    echo "✓ Archivo no existe"
fi
echo ""

# 5. Forzar actualización desde remoto
echo "[5/5] Actualizando desde remoto..."
git fetch origin main
git reset --hard origin/main
echo "✓ Actualización completada"
echo ""

# 6. Restaurar .env
if [ -f ".env.backup" ]; then
    cp .env.backup .env
    echo "✓ .env restaurado"
fi
echo ""

echo "=========================================="
echo "  ¡Actualización Completada!"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "1. Verificar archivos:"
echo "   ls -la SOLUCION_LOGIN_*.md"
echo "   ls -la diagnosticar-login-*"
echo ""
echo "2. Limpiar cachés:"
echo "   php artisan config:clear"
echo "   php artisan cache:clear"
echo ""
echo "3. Si necesitas recuperar cambios locales:"
echo "   git stash list"
echo "   git stash show -p stash@{0}"
echo ""


