#!/bin/bash
# Script simple para instalar PHP 8.1 (más compatible)
# Ejecutar en el VPS: bash instalar-php-simple.sh

echo "=========================================="
echo "  Instalar PHP 8.1 (Método Simple)"
echo "=========================================="
echo ""

# Verificar versión actual
CURRENT_PHP=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;" 2>/dev/null || echo "0.0")
echo "PHP actual: $CURRENT_PHP"

if [ "$(php -r "echo version_compare('$CURRENT_PHP', '8.1', '>=');" 2>/dev/null || echo "0")" = "1" ]; then
    echo "✓ PHP ya está en versión 8.1 o superior"
    php -v
    exit 0
fi

echo ""
echo "[1/3] Configurando repositorios..."
bash configurar-repositorios-php.sh

if [ $? -ne 0 ]; then
    echo "✗ Error al configurar repositorios"
    exit 1
fi

echo ""
echo "[2/3] Instalando PHP 8.1 y extensiones..."
# Intentar instalar PHP 8.1 primero (más estable)
if apt-cache search php8.1 2>/dev/null | grep -q "^php8.1 "; then
    PHP_VERSION="8.1"
    echo "Instalando PHP 8.1..."
elif apt-cache search php8.2 2>/dev/null | grep -q "^php8.2 "; then
    PHP_VERSION="8.2"
    echo "Instalando PHP 8.2..."
elif apt-cache search php8.3 2>/dev/null | grep -q "^php8.3 "; then
    PHP_VERSION="8.3"
    echo "Instalando PHP 8.3..."
else
    echo "✗ No se encontró PHP 8.1, 8.2 ni 8.3"
    echo ""
    echo "Paquetes PHP disponibles:"
    apt-cache search php | grep "^php[0-9]" | head -10
    exit 1
fi

apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl php${PHP_VERSION}-opcache

if [ $? -ne 0 ]; then
    echo "✗ Error al instalar PHP $PHP_VERSION"
    echo "Intentando instalar paquetes individualmente..."
    apt-get install -y php${PHP_VERSION}
    apt-get install -y php${PHP_VERSION}-cli php${PHP_VERSION}-fpm
    apt-get install -y php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring
    apt-get install -y php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-gd
    apt-get install -y php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl php${PHP_VERSION}-opcache
fi

echo ""
echo "[3/3] Configurando PHP $PHP_VERSION..."
update-alternatives --set php /usr/bin/php${PHP_VERSION} 2>/dev/null || true

# Configurar Apache
a2dismod php7.4 2>/dev/null || true
a2enmod php${PHP_VERSION} 2>/dev/null || echo "⚠ No se pudo habilitar módulo PHP en Apache"
systemctl restart apache2 2>/dev/null || service apache2 restart 2>/dev/null || true

echo ""
echo "=========================================="
echo "  Verificación"
echo "=========================================="
echo ""
NEW_VERSION=$(php -v | head -n 1)
echo "Versión instalada: $NEW_VERSION"

NEW_PHP=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [ "$(php -r "echo version_compare('$NEW_PHP', '8.1', '>=');")" = "1" ]; then
    echo "✓ PHP $NEW_PHP instalado correctamente"
    echo ""
    echo "Extensiones instaladas:"
    php -m | grep -E "mysql|xml|mbstring|curl|zip|gd|bcmath|intl|opcache" | sort
else
    echo "✗ Error: PHP sigue en versión $NEW_PHP"
    exit 1
fi

echo ""
echo "=========================================="
echo "  ¡PHP Instalado!"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "  cd /var/www/html/mafit"
echo "  composer install --no-dev --optimize-autoloader"
echo ""





