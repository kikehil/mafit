#!/bin/bash
# Script mejorado para actualizar PHP en VPS Debian/Ubuntu
# Ejecutar en el VPS: bash actualizar-php-vps-debian.sh

echo "=========================================="
echo "  Actualizar PHP en VPS (Debian/Ubuntu)"
echo "=========================================="
echo ""

# Detectar distribución
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    VERSION=$VERSION_ID
    echo "Sistema detectado: $OS $VERSION"
else
    echo "No se pudo detectar el sistema operativo"
    exit 1
fi

# Verificar versión actual
CURRENT_PHP=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;" 2>/dev/null || echo "0.0")
echo "PHP actual: $CURRENT_PHP"

if [ "$(php -r "echo version_compare('$CURRENT_PHP', '8.1', '>=');" 2>/dev/null || echo "0")" = "1" ]; then
    echo "✓ PHP ya está en versión 8.1 o superior"
    exit 0
fi

echo ""
echo "[1/5] Limpiando configuración anterior..."
apt-get clean
apt-get update

echo ""
echo "[2/5] Agregando repositorio de Ondrej PHP..."
# Instalar dependencias necesarias
apt-get install -y software-properties-common apt-transport-https lsb-release ca-certificates

# Agregar repositorio según la distribución
if [ "$OS" = "debian" ]; then
    echo "Configurando para Debian..."
    apt-get install -y curl wget gnupg2
    wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury-php.list
elif [ "$OS" = "ubuntu" ]; then
    echo "Configurando para Ubuntu..."
    add-apt-repository ppa:ondrej/php -y
else
    echo "Sistema no soportado: $OS"
    exit 1
fi

echo ""
echo "[3/5] Actualizando lista de paquetes..."
apt-get update

echo ""
echo "[4/5] Verificando disponibilidad de PHP 8.3..."
if apt-cache search php8.3 | grep -q "^php8.3 "; then
    echo "✓ PHP 8.3 está disponible"
else
    echo "✗ PHP 8.3 no está disponible en los repositorios"
    echo "Intentando con PHP 8.2..."
    
    if apt-cache search php8.2 | grep -q "^php8.2 "; then
        PHP_VERSION="8.2"
        echo "✓ PHP 8.2 está disponible, usando esta versión"
    else
        echo "✗ PHP 8.2 tampoco está disponible"
        echo "Intentando con PHP 8.1..."
        
        if apt-cache search php8.1 | grep -q "^php8.1 "; then
            PHP_VERSION="8.1"
            echo "✓ PHP 8.1 está disponible, usando esta versión"
        else
            echo "✗ No se encontró PHP 8.1, 8.2 ni 8.3"
            echo "Verifica la configuración de repositorios"
            exit 1
        fi
    fi
else
    PHP_VERSION="8.3"
fi

echo ""
echo "[5/5] Instalando PHP $PHP_VERSION y extensiones..."
apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl php${PHP_VERSION}-opcache

if [ $? -ne 0 ]; then
    echo "✗ Error al instalar PHP $PHP_VERSION"
    echo "Intentando instalar extensiones una por una..."
    apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-fpm
    apt-get install -y php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring
    apt-get install -y php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-gd
    apt-get install -y php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl php${PHP_VERSION}-opcache
fi

echo ""
echo "Configurando PHP $PHP_VERSION como versión por defecto..."
update-alternatives --set php /usr/bin/php${PHP_VERSION} 2>/dev/null || true

echo ""
echo "Configurando Apache..."
# Deshabilitar versiones antiguas de PHP
a2dismod php7.4 2>/dev/null || true
a2dismod php8.0 2>/dev/null || true

# Habilitar nueva versión
a2enmod php${PHP_VERSION} 2>/dev/null || {
    echo "⚠ No se pudo habilitar módulo PHP en Apache"
    echo "  Puede que Apache use PHP-FPM en lugar de mod_php"
}

# Reiniciar Apache
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
    echo "✓ PHP actualizado correctamente a $NEW_PHP"
    echo ""
    echo "Extensiones instaladas:"
    php -m | grep -E "mysql|xml|mbstring|curl|zip|gd|bcmath|intl|opcache" | sort
else
    echo "✗ Error: PHP sigue en versión $NEW_PHP"
    exit 1
fi

echo ""
echo "=========================================="
echo "  ¡PHP Actualizado!"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "  1. cd /var/www/html/mafit"
echo "  2. composer install --no-dev --optimize-autoloader"
echo "  3. bash actualizar-vps.sh"
echo ""

