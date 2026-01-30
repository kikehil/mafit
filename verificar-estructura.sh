#!/bin/bash
# Script para verificar estructura del proyecto

echo "=========================================="
echo "  Verificar Estructura del Proyecto"
echo "=========================================="
echo ""

APP_DIR="/var/www/html/mafit"
cd "$APP_DIR" || exit 1

echo "Estructura actual:"
echo ""
ls -la | head -20

echo ""
echo "¿Existe directorio public?"
if [ -d "public" ]; then
    echo "✓ Directorio public existe"
    echo ""
    echo "Contenido de public:"
    ls -la public/ | head -10
else
    echo "✗ Directorio public NO existe"
fi

echo ""
echo "¿Existe index.php en algún lugar?"
find . -name "index.php" -type f 2>/dev/null | head -5

echo ""
echo "¿Existe artisan?"
if [ -f "artisan" ]; then
    echo "✓ artisan existe (es Laravel)"
else
    echo "✗ artisan NO existe"
fi

echo ""
echo "Verificando estructura Laravel típica:"
for dir in app bootstrap config database public resources routes storage; do
    if [ -d "$dir" ]; then
        echo "✓ $dir existe"
    else
        echo "✗ $dir NO existe"
    fi
done

echo ""


