#!/bin/bash
# Script completo para corregir repositorios PHP y hacer que funcionen
# Ejecutar en el VPS: bash fix-php-repos-completo.sh

echo "=========================================="
echo "  Corrección Completa de Repositorios PHP"
echo "=========================================="
echo ""

CODENAME=$(lsb_release -sc)
echo "Distribución: $CODENAME"
echo ""

echo "[1/6] Limpiando TODOS los archivos relacionados con PHP..."
# Eliminar todos los archivos relacionados
rm -f /etc/apt/sources.list.d/ondrej-php.list
rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-*.list
rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-*.list.save
rm -f /etc/apt/sources.list.d/sury-php.list
rm -f /etc/apt/trusted.gpg.d/ondrej-php.gpg
rm -f /etc/apt/trusted.gpg.d/sury-php.gpg

# Limpiar archivo backup problemático
rm -f /etc/apt/sources.list.d/ubuntu-mirrors.list.backup

echo "✓ Archivos limpiados"

echo ""
echo "[2/6] Instalando dependencias..."
apt-get update
apt-get install -y software-properties-common apt-transport-https lsb-release ca-certificates curl gnupg2

echo ""
echo "[3/6] Agregando clave GPG de Ondrej (método directo)..."
# Método 1: Descargar clave directamente
curl -fsSL https://keyserver.ubuntu.com/pks/lookup?op=get\&search=0x14AA40EC0831756756D7F66C4F4EA0AAE5267A6C | gpg --dearmor -o /etc/apt/trusted.gpg.d/ondrej-php.gpg

# Método 2 alternativo si falla
if [ ! -f /etc/apt/trusted.gpg.d/ondrej-php.gpg ]; then
    echo "Método alternativo: descargando desde packages.sury.org..."
    curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/ondrej-php.gpg
fi

echo "✓ Clave GPG agregada"

echo ""
echo "[4/6] Agregando repositorio de Ondrej..."
# Usar el método más confiable
echo "deb https://ppa.launchpadcontent.net/ondrej/php/ubuntu $CODENAME main" > /etc/apt/sources.list.d/ondrej-php.list
echo "# deb-src https://ppa.launchpadcontent.net/ondrej/php/ubuntu $CODENAME main" >> /etc/apt/sources.list.d/ondrej-php.list

echo "Contenido del archivo:"
cat /etc/apt/sources.list.d/ondrej-php.list

echo ""
echo "[5/6] Actualizando lista de paquetes (forzando refresh)..."
apt-get clean
rm -rf /var/lib/apt/lists/*
apt-get update

echo ""
echo "[6/6] Verificando con apt-cache policy (más confiable)..."
echo ""
echo "Política para php8.1:"
apt-cache policy php8.1 2>&1 | head -10

echo ""
echo "Buscando paquetes PHP 8.x disponibles:"
apt-cache search php8.1 2>/dev/null | grep "^php8.1 " | head -5

if [ -z "$(apt-cache search php8.1 2>/dev/null | grep '^php8.1 ')" ]; then
    echo ""
    echo "⚠ Aún no se encuentran los paquetes"
    echo ""
    echo "Intentando método alternativo con add-apt-repository..."
    add-apt-repository --remove ppa:ondrej/php 2>/dev/null || true
    add-apt-repository ppa:ondrej/php -y
    apt-get update
    
    echo ""
    echo "Verificando nuevamente:"
    apt-cache policy php8.1 2>&1 | head -10
fi

echo ""
echo "=========================================="
echo "  Resumen"
echo "=========================================="
echo ""
if apt-cache policy php8.1 2>&1 | grep -q "Candidate:"; then
    CANDIDATE=$(apt-cache policy php8.1 2>&1 | grep "Candidate:" | awk '{print $2}')
    if [ "$CANDIDATE" != "(none)" ]; then
        echo "✓ PHP 8.1 está disponible: $CANDIDATE"
        echo ""
        echo "Puedes instalar con:"
        echo "  apt-get install -y php8.1 php8.1-cli php8.1-fpm php8.1-mysql \\"
        echo "      php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip \\"
        echo "      php8.1-gd php8.1-bcmath php8.1-intl php8.1-opcache"
    else
        echo "✗ PHP 8.1 aún no está disponible"
        echo ""
        echo "Diagnóstico adicional:"
        echo "  - Verifica: cat /etc/apt/sources.list.d/ondrej-php.list"
        echo "  - Verifica: apt-key list | grep ondrej"
        echo "  - Verifica: apt-get update 2>&1 | grep -i error"
    fi
else
    echo "✗ No se pudo verificar la disponibilidad"
fi

echo ""

