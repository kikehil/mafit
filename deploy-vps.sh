#!/bin/bash

# Script de Despliegue Automatizado para VPS - MAFIT
# Este script automatiza la instalación y configuración de MAFIT en un VPS Ubuntu

set -e  # Salir si hay algún error

echo "=========================================="
echo "  Script de Despliegue MAFIT en VPS"
echo "=========================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variables configurables
PROJECT_DIR="/var/www/mafit"
DOMAIN=""
DB_NAME="mafit"
DB_USER="mafit_user"
DB_PASSWORD=""
PHP_VERSION="8.3"

# Función para imprimir mensajes
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}→${NC} $1"
}

# Verificar que se ejecuta como root o con sudo
if [ "$EUID" -ne 0 ]; then 
    print_error "Por favor ejecuta este script con sudo"
    exit 1
fi

# Solicitar información necesaria
echo "Por favor, proporciona la siguiente información:"
read -p "Dominio o IP del servidor (ej: mafit.tudominio.com): " DOMAIN
read -p "Contraseña para el usuario de MySQL (mafit_user): " DB_PASSWORD
read -p "¿Deseas instalar Node.js y npm para compilar assets? (s/n): " INSTALL_NODE

if [ -z "$DOMAIN" ] || [ -z "$DB_PASSWORD" ]; then
    print_error "Dominio y contraseña de BD son requeridos"
    exit 1
fi

print_info "Iniciando instalación..."

# Paso 1: Actualizar sistema
print_info "Actualizando sistema..."
apt update && apt upgrade -y
print_success "Sistema actualizado"

# Paso 2: Instalar dependencias básicas
print_info "Instalando dependencias básicas..."
apt install -y software-properties-common curl wget git unzip
print_success "Dependencias básicas instaladas"

# Paso 3: Instalar PHP y extensiones
print_info "Instalando PHP $PHP_VERSION y extensiones..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl
print_success "PHP $PHP_VERSION instalado"

# Paso 4: Instalar Composer
print_info "Instalando Composer..."
if [ ! -f /usr/local/bin/composer ]; then
    cd /tmp
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    print_success "Composer instalado"
else
    print_info "Composer ya está instalado"
fi

# Paso 5: Instalar MySQL
print_info "Instalando MySQL..."
if ! command -v mysql &> /dev/null; then
    apt install -y mysql-server
    systemctl start mysql
    systemctl enable mysql
    print_success "MySQL instalado"
else
    print_info "MySQL ya está instalado"
fi

# Paso 6: Crear base de datos y usuario
print_info "Configurando base de datos..."
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF
print_success "Base de datos configurada"

# Paso 7: Instalar Nginx
print_info "Instalando Nginx..."
if ! command -v nginx &> /dev/null; then
    apt install -y nginx
    systemctl start nginx
    systemctl enable nginx
    print_success "Nginx instalado"
else
    print_info "Nginx ya está instalado"
fi

# Paso 8: Instalar Node.js (opcional)
if [ "$INSTALL_NODE" = "s" ] || [ "$INSTALL_NODE" = "S" ]; then
    print_info "Instalando Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
    print_success "Node.js instalado"
fi

# Paso 9: Verificar que el proyecto existe
if [ ! -d "$PROJECT_DIR" ]; then
    print_error "El directorio $PROJECT_DIR no existe"
    print_info "Por favor, sube el proyecto a $PROJECT_DIR primero"
    print_info "Puedes usar: scp -r /ruta/local/MAFIT usuario@servidor:/var/www/mafit"
    exit 1
fi

# Paso 10: Configurar permisos
print_info "Configurando permisos..."
chown -R www-data:www-data $PROJECT_DIR
chmod -R 755 $PROJECT_DIR
print_success "Permisos configurados"

# Paso 11: Instalar dependencias de Composer
print_info "Instalando dependencias de Composer..."
cd $PROJECT_DIR
sudo -u www-data composer install --optimize-autoloader --no-dev --no-interaction
print_success "Dependencias de Composer instaladas"

# Paso 12: Configurar .env
print_info "Configurando archivo .env..."
if [ ! -f "$PROJECT_DIR/.env" ]; then
    if [ -f "$PROJECT_DIR/.env.example" ]; then
        cp $PROJECT_DIR/.env.example $PROJECT_DIR/.env
    else
        print_error "No se encontró .env.example"
        exit 1
    fi
fi

# Actualizar variables de entorno
sed -i "s|APP_ENV=.*|APP_ENV=production|g" $PROJECT_DIR/.env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" $PROJECT_DIR/.env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|g" $PROJECT_DIR/.env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|g" $PROJECT_DIR/.env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|g" $PROJECT_DIR/.env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|g" $PROJECT_DIR/.env
sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|g" $PROJECT_DIR/.env

print_success "Archivo .env configurado"

# Paso 13: Generar clave de aplicación
print_info "Generando clave de aplicación..."
cd $PROJECT_DIR
sudo -u www-data php artisan key:generate --force
print_success "Clave de aplicación generada"

# Paso 14: Ejecutar migraciones
print_info "Ejecutando migraciones..."
sudo -u www-data php artisan migrate --force
print_success "Migraciones ejecutadas"

# Paso 15: Compilar assets (si Node.js está instalado)
if command -v npm &> /dev/null; then
    print_info "Compilando assets..."
    cd $PROJECT_DIR
    npm install
    npm run build
    print_success "Assets compilados"
else
    print_info "Node.js no está instalado. Omitting asset compilation."
    print_info "Puedes compilar los assets manualmente después con: npm install && npm run build"
fi

# Paso 16: Optimizar para producción
print_info "Optimizando para producción..."
cd $PROJECT_DIR
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
print_success "Aplicación optimizada"

# Paso 17: Configurar permisos de storage
print_info "Configurando permisos de storage..."
chown -R www-data:www-data $PROJECT_DIR/storage $PROJECT_DIR/bootstrap/cache
chmod -R 775 $PROJECT_DIR/storage $PROJECT_DIR/bootstrap/cache
print_success "Permisos de storage configurados"

# Paso 18: Configurar Nginx
print_info "Configurando Nginx..."
cat > /etc/nginx/sites-available/mafit <<EOF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${PROJECT_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Habilitar sitio
ln -sf /etc/nginx/sites-available/mafit /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Verificar configuración
nginx -t
systemctl reload nginx
print_success "Nginx configurado"

# Paso 19: Configurar SSL con Let's Encrypt (opcional)
read -p "¿Deseas configurar SSL con Let's Encrypt? (s/n): " CONFIGURE_SSL
if [ "$CONFIGURE_SSL" = "s" ] || [ "$CONFIGURE_SSL" = "S" ]; then
    print_info "Instalando Certbot..."
    apt install -y certbot python3-certbot-nginx
    print_info "Obteniendo certificado SSL..."
    certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN} || true
    print_success "SSL configurado (si el dominio está apuntando correctamente)"
fi

# Paso 20: Configurar cron para tareas programadas
print_info "Configurando cron..."
(crontab -u www-data -l 2>/dev/null | grep -v "artisan schedule:run"; echo "* * * * * cd ${PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -
print_success "Cron configurado"

# Resumen final
echo ""
echo "=========================================="
echo "  ¡Despliegue Completado!"
echo "=========================================="
echo ""
print_success "Aplicación instalada en: $PROJECT_DIR"
print_success "Base de datos: $DB_NAME"
print_success "Usuario BD: $DB_USER"
print_success "URL: http://${DOMAIN}"
if [ "$CONFIGURE_SSL" = "s" ] || [ "$CONFIGURE_SSL" = "S" ]; then
    print_success "HTTPS: https://${DOMAIN}"
fi
echo ""
print_info "Próximos pasos:"
echo "  1. Importa tu base de datos si tienes datos:"
echo "     mysql -u ${DB_USER} -p ${DB_NAME} < /ruta/a/mafit_backup.sql"
echo ""
echo "  2. Crea un usuario administrador:"
echo "     cd ${PROJECT_DIR}"
echo "     php artisan tinker"
echo "     # Luego ejecuta:"
echo "     \$user = App\Models\User::create(["
echo "         'name' => 'Administrador',"
echo "         'email' => 'admin@example.com',"
echo "         'password' => Hash::make('tu_password'),"
echo "         'role' => 'admin',"
echo "         'plaza' => '32YXH'"
echo "     ]);"
echo ""
echo "  3. Verifica los logs si hay problemas:"
echo "     tail -f ${PROJECT_DIR}/storage/logs/laravel.log"
echo ""
print_success "¡Listo para usar!"





