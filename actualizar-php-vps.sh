#!/bin/bash
# Script para actualizar PHP en el VPS a PHP 8.3
# Ejecutar en el VPS: bash actualizar-php-vps.sh

echo "=========================================="
echo "  Actualizar PHP en VPS"
echo "=========================================="
echo ""

# Verificar versión actual de PHP
echo "[1/6] Verificando versión actual de PHP..."
PHP_VERSION=$(php -v | head -n 1)
echo "Versión actual: $PHP_VERSION"

CURRENT_PHP=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "PHP $CURRENT_PHP detectado"

if [ "$(php -r "echo version_compare('$CURRENT_PHP', '8.1', '>=');")" = "1" ]; then
    echo "✓ PHP ya está en versión 8.1 o superior"
    exit 0
fi

echo ""
echo "[2/6] Agregando repositorio de PHP..."
# Agregar repositorio de Ondrej para PHP 8.3
apt update
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

echo ""
echo "[3/6] Instalando PHP 8.3 y extensiones necesarias..."
apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip \
    php8.3-gd php8.3-bcmath php8.3-intl php8.3-opcache

echo ""
echo "[4/6] Configurando PHP 8.3 como versión por defecto..."
# Actualizar alternativas
update-alternatives --set php /usr/bin/php8.3
update-alternatives --set phar /usr/bin/phar8.3
update-alternatives --set phar.phar /usr/bin/phar.phar8.3

echo ""
echo "[5/6] Verificando instalación..."
NEW_VERSION=$(php -v | head -n 1)
echo "Nueva versión: $NEW_VERSION"

if [ "$(php -r "echo version_compare(PHP_VERSION, '8.1', '>=');")" = "1" ]; then
    echo "✓ PHP actualizado correctamente"
else
    echo "✗ Error al actualizar PHP"
    exit 1
fi

echo ""
echo "[6/6] Configurando Apache para usar PHP 8.3..."
# Habilitar módulo PHP 8.3 en Apache
a2enmod php8.3

# Deshabilitar versión antigua de PHP si existe
a2dismod php7.4 2>/dev/null || true

# Reiniciar Apache
systemctl restart apache2

echo ""
echo "=========================================="
echo "  ¡PHP Actualizado!"
echo "=========================================="
echo ""
echo "Versión instalada:"
php -v
echo ""
echo "Extensiones instaladas:"
php -m | grep -E "mysql|xml|mbstring|curl|zip|gd|bcmath|intl|opcache"
echo ""
echo "Próximos pasos:"
echo "  1. Instalar dependencias: composer install"
echo "  2. Verificar que todo funciona"
echo ""

