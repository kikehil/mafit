#!/bin/bash
# Script para corregir problemas con repositorios PHP
# Ejecutar en el VPS: bash corregir-repositorios-php.sh

echo "=========================================="
echo "  Corregir Repositorios PHP"
echo "=========================================="
echo ""

echo "[1/5] Verificando distribución..."
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    CODENAME=$(lsb_release -sc 2>/dev/null || echo "focal")
    echo "Sistema: $OS ($CODENAME)"
else
    echo "No se pudo detectar el sistema"
    exit 1
fi

echo ""
echo "[2/5] Limpiando repositorios duplicados y conflictivos..."
# Hacer backup
cp /etc/apt/sources.list.d/ubuntu-mirrors.list /etc/apt/sources.list.d/ubuntu-mirrors.list.backup 2>/dev/null || true

# Eliminar entradas duplicadas en ubuntu-mirrors.list
if [ -f /etc/apt/sources.list.d/ubuntu-mirrors.list ]; then
    echo "Limpiando ubuntu-mirrors.list..."
    # Eliminar líneas duplicadas
    sort -u /etc/apt/sources.list.d/ubuntu-mirrors.list > /tmp/ubuntu-mirrors.list.tmp
    mv /tmp/ubuntu-mirrors.list.tmp /etc/apt/sources.list.d/ubuntu-mirrors.list
fi

echo ""
echo "[3/5] Configurando repositorio de Ondrej correctamente..."
# Eliminar configuraciones anteriores
rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-*.list
rm -f /etc/apt/sources.list.d/sury-php.list

# Instalar dependencias
apt-get install -y software-properties-common apt-transport-https lsb-release ca-certificates curl gnupg2

# Agregar clave GPG de Ondrej
curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/ondrej-php.gpg

# Agregar repositorio
if [ "$OS" = "ubuntu" ]; then
    echo "deb https://ppa.launchpadcontent.net/ondrej/php/ubuntu $CODENAME main" > /etc/apt/sources.list.d/ondrej-php.list
    echo "# deb-src https://ppa.launchpadcontent.net/ondrej/php/ubuntu $CODENAME main" >> /etc/apt/sources.list.d/ondrej-php.list
elif [ "$OS" = "debian" ]; then
    echo "deb https://packages.sury.org/php/ $CODENAME main" > /etc/apt/sources.list.d/ondrej-php.list
fi

echo ""
echo "[4/5] Actualizando lista de paquetes..."
apt-get clean
apt-get update

echo ""
echo "[5/5] Verificando disponibilidad de paquetes PHP..."
echo ""
echo "Buscando PHP 8.1:"
if apt-cache search php8.1 2>/dev/null | grep -q "^php8.1 "; then
    echo "✓ PHP 8.1 está disponible"
    apt-cache search php8.1 | grep "^php8.1 " | head -3
else
    echo "✗ PHP 8.1 NO está disponible"
fi

echo ""
echo "Buscando PHP 8.2:"
if apt-cache search php8.2 2>/dev/null | grep -q "^php8.2 "; then
    echo "✓ PHP 8.2 está disponible"
    apt-cache search php8.2 | grep "^php8.2 " | head -3
else
    echo "✗ PHP 8.2 NO está disponible"
fi

echo ""
echo "Buscando PHP 8.3:"
if apt-cache search php8.3 2>/dev/null | grep -q "^php8.3 "; then
    echo "✓ PHP 8.3 está disponible"
    apt-cache search php8.3 | grep "^php8.3 " | head -3
else
    echo "✗ PHP 8.3 NO está disponible"
fi

echo ""
echo "=========================================="
echo "  Diagnóstico"
echo "=========================================="
echo ""
echo "Repositorios PHP configurados:"
ls -la /etc/apt/sources.list.d/*php* 2>/dev/null || echo "Ninguno"

echo ""
echo "Contenido del repositorio de Ondrej:"
cat /etc/apt/sources.list.d/ondrej-php.list 2>/dev/null || echo "No existe"

echo ""
echo "Si los paquetes aún no están disponibles, prueba:"
echo "  1. apt-get update --fix-missing"
echo "  2. apt-cache policy php8.1"
echo "  3. Verificar que la clave GPG esté correcta"
echo ""

