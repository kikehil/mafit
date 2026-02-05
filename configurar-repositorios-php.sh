#!/bin/bash
# Script para configurar correctamente los repositorios de PHP
# Ejecutar en el VPS: bash configurar-repositorios-php.sh

echo "=========================================="
echo "  Configurar Repositorios de PHP"
echo "=========================================="
echo ""

# Detectar distribución
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    VERSION=$VERSION_ID
    CODENAME=$(lsb_release -sc 2>/dev/null || echo "bullseye")
    echo "Sistema: $OS $VERSION ($CODENAME)"
else
    echo "No se pudo detectar el sistema operativo"
    exit 1
fi

echo ""
echo "[1/4] Instalando dependencias necesarias..."
apt-get update
apt-get install -y software-properties-common apt-transport-https lsb-release ca-certificates curl wget gnupg2

echo ""
echo "[2/4] Limpiando repositorios anteriores..."
# Eliminar repositorios PHP anteriores si existen
rm -f /etc/apt/sources.list.d/sury-php.list
rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php*.list
rm -f /etc/apt/trusted.gpg.d/php.gpg
rm -f /etc/apt/trusted.gpg.d/sury-php.gpg

echo ""
echo "[3/4] Agregando repositorio de PHP..."

if [ "$OS" = "debian" ]; then
    echo "Configurando para Debian..."
    
    # Método 1: Repositorio de Sury
    wget -qO - https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-php.gpg
    echo "deb https://packages.sury.org/php/ $CODENAME main" > /etc/apt/sources.list.d/sury-php.list
    
elif [ "$OS" = "ubuntu" ]; then
    echo "Configurando para Ubuntu..."
    
    # Método 1: PPA de Ondrej
    add-apt-repository ppa:ondrej/php -y
    
    # Método 2 alternativo: Repositorio directo
    if [ $? -ne 0 ]; then
        echo "PPA falló, intentando método alternativo..."
        wget -qO - https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-php.gpg
        echo "deb https://packages.sury.org/php/ $CODENAME main" > /etc/apt/sources.list.d/sury-php.list
    fi
else
    echo "Sistema no soportado: $OS"
    exit 1
fi

echo ""
echo "[4/4] Actualizando lista de paquetes..."
apt-get update

echo ""
echo "=========================================="
echo "  Verificación"
echo "=========================================="
echo ""
echo "Repositorios configurados:"
ls -la /etc/apt/sources.list.d/*php* 2>/dev/null || echo "No se encontraron archivos de repositorio PHP"

echo ""
echo "Buscando paquetes PHP disponibles..."
if apt-cache search php8.3 2>/dev/null | grep -q "^php8.3 "; then
    echo "✓ PHP 8.3 está disponible"
elif apt-cache search php8.2 2>/dev/null | grep -q "^php8.2 "; then
    echo "✓ PHP 8.2 está disponible"
elif apt-cache search php8.1 2>/dev/null | grep -q "^php8.1 "; then
    echo "✓ PHP 8.1 está disponible"
else
    echo "✗ No se encontró PHP 8.1, 8.2 ni 8.3"
    echo ""
    echo "Listando primeros 10 paquetes PHP disponibles:"
    apt-cache search php | grep "^php[0-9]" | head -10
    echo ""
    echo "Verifica manualmente:"
    echo "  cat /etc/apt/sources.list.d/sury-php.list"
    echo "  apt-cache search php | grep php8"
fi

echo ""





