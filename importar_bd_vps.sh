#!/bin/bash
# Script para importar la base de datos al VPS
# Uso: ./importar_bd_vps.sh archivo.sql

echo "========================================"
echo "Importando Base de Datos MAFIT al VPS"
echo "========================================"
echo ""

# Verificar que se proporcionó el archivo
if [ -z "$1" ]; then
    echo "ERROR: Debes proporcionar el archivo SQL a importar"
    echo "Uso: ./importar_bd_vps.sh archivo.sql"
    exit 1
fi

ARCHIVO=$1

# Verificar que el archivo existe
if [ ! -f "$ARCHIVO" ]; then
    echo "ERROR: El archivo $ARCHIVO no existe"
    exit 1
fi

# Leer configuración de .env (ajustar según tu configuración)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2 | tr -d ' ')
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | tr -d ' ')
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2 | tr -d ' ')
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2 | tr -d ' ')

echo "Base de datos: $DB_NAME"
echo "Usuario: $DB_USER"
echo "Host: $DB_HOST"
echo ""
echo "IMPORTANTE: Este proceso reemplazará TODOS los datos existentes"
read -p "¿Continuar? (s/n): " confirmar

if [ "$confirmar" != "s" ] && [ "$confirmar" != "S" ]; then
    echo "Operación cancelada"
    exit 0
fi

echo ""
echo "Importando base de datos..."
echo "Esto puede tardar varios minutos..."

# Importar base de datos
mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" < "$ARCHIVO"

if [ $? -eq 0 ]; then
    echo ""
    echo "========================================"
    echo "Importación completada exitosamente!"
    echo "========================================"
    echo ""
    echo "Siguiente paso: Ejecutar migraciones y verificar categorías"
    echo ""
    echo "Ejecuta estos comandos:"
    echo "  php artisan migrate"
    echo "  php artisan db:seed (si tienes seeders)"
    echo "  php artisan config:clear"
    echo "  php artisan cache:clear"
else
    echo ""
    echo "========================================"
    echo "ERROR en la importación"
    echo "========================================"
    echo "Verifica:"
    echo "1. Que MySQL esté corriendo"
    echo "2. Que las credenciales en .env sean correctas"
    echo "3. Que tengas permisos para importar"
    exit 1
fi







