#!/bin/bash
# Script de instalación automática para el servidor VPS
# Uso: bash instalar_en_servidor.sh

set -e  # Salir si hay algún error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_info() {
    echo -e "${CYAN}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Banner
echo ""
echo "========================================"
echo "  Instalación Automática MAFIT en VPS"
echo "========================================"
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "No se encontró el archivo 'artisan'. Asegúrate de estar en el directorio raíz del proyecto Laravel."
    exit 1
fi

print_info "Directorio actual: $(pwd)"
echo ""

# Verificar PHP
print_info "Verificando PHP..."
if ! command -v php &> /dev/null; then
    print_error "PHP no está instalado. Por favor instálalo primero."
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
print_success "PHP $PHP_VERSION encontrado"

# Verificar Composer
print_info "Verificando Composer..."
if ! command -v composer &> /dev/null; then
    print_warning "Composer no está instalado. Instalando..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    print_success "Composer instalado"
else
    print_success "Composer encontrado"
fi

# Verificar Node.js y npm (opcional, solo si necesitas compilar assets)
print_info "Verificando Node.js..."
if ! command -v node &> /dev/null; then
    print_warning "Node.js no está instalado. ¿Deseas instalarlo? (s/n)"
    read -r respuesta
    if [[ "$respuesta" =~ ^[Ss]$ ]]; then
        curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
        sudo apt-get install -y nodejs
        print_success "Node.js instalado"
    else
        print_warning "Node.js no instalado. Los assets no se compilarán."
    fi
else
    NODE_VERSION=$(node -v)
    print_success "Node.js $NODE_VERSION encontrado"
fi

# Paso 1: Instalar dependencias de Composer
echo ""
print_info "Instalando dependencias de PHP (Composer)..."
print_warning "Esto puede tardar varios minutos..."
composer install --optimize-autoloader --no-dev --no-interaction
print_success "Dependencias de PHP instaladas"

# Paso 2: Instalar dependencias de Node.js (si Node está disponible)
if command -v npm &> /dev/null; then
    echo ""
    print_info "Instalando dependencias de Node.js..."
    npm install --production
    print_success "Dependencias de Node.js instaladas"
    
    # Compilar assets
    echo ""
    print_info "Compilando assets para producción..."
    npm run build
    print_success "Assets compilados"
else
    print_warning "npm no disponible. Los assets no se compilarán."
fi

# Paso 3: Configurar archivo .env
echo ""
print_info "Configurando archivo .env..."

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_success "Archivo .env creado desde .env.example"
    else
        print_error "No se encontró .env.example. Debes crear .env manualmente."
        exit 1
    fi
else
    print_warning "El archivo .env ya existe. ¿Deseas sobrescribirlo? (s/n)"
    read -r respuesta
    if [[ "$respuesta" =~ ^[Ss]$ ]]; then
        cp .env.example .env
        print_success "Archivo .env sobrescrito"
    else
        print_info "Manteniendo .env existente"
    fi
fi

# Solicitar información para .env
echo ""
print_info "Configuración del archivo .env:"
print_warning "Por favor, completa la información solicitada:"
echo ""

read -p "APP_NAME [MAFIT]: " APP_NAME
APP_NAME=${APP_NAME:-MAFIT}

read -p "APP_URL [http://localhost]: " APP_URL
APP_URL=${APP_URL:-http://localhost}

read -p "DB_DATABASE [mafit]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-mafit}

read -p "DB_USERNAME [mafit_user]: " DB_USERNAME
DB_USERNAME=${DB_USERNAME:-mafit_user}

read -sp "DB_PASSWORD: " DB_PASSWORD
echo ""

# Actualizar .env
sed -i "s/APP_NAME=.*/APP_NAME=$APP_NAME/" .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env

print_success "Archivo .env configurado"

# Paso 4: Generar clave de aplicación
echo ""
print_info "Generando clave de aplicación..."
php artisan key:generate --force
print_success "Clave de aplicación generada"

# Paso 5: Crear enlace simbólico de storage
echo ""
print_info "Creando enlace simbólico de storage..."
php artisan storage:link
print_success "Enlace simbólico creado"

# Paso 6: Ejecutar migraciones
echo ""
print_warning "¿Deseas ejecutar las migraciones de base de datos? (s/n)"
read -r respuesta
if [[ "$respuesta" =~ ^[Ss]$ ]]; then
    print_info "Ejecutando migraciones..."
    php artisan migrate --force
    print_success "Migraciones ejecutadas"
else
    print_warning "Migraciones omitidas"
fi

# Paso 7: Optimizar para producción
echo ""
print_info "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Optimización completada"

# Paso 8: Configurar permisos
echo ""
print_info "Configurando permisos..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
print_success "Permisos configurados"

# Resumen final
echo ""
echo "========================================"
echo "  Instalación Completada"
echo "========================================"
echo ""
print_success "La aplicación está lista para producción"
echo ""
print_info "Próximos pasos:"
echo "  1. Verifica la configuración de Nginx/Apache"
echo "  2. Asegúrate de que el servidor web apunta a: $(pwd)/public"
echo "  3. Configura SSL con Let's Encrypt si es necesario"
echo "  4. Configura el cron job para tareas programadas:"
echo "     * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo ""
print_info "Para ver los logs:"
echo "  tail -f storage/logs/laravel.log"
echo ""
print_info "Para limpiar caché si es necesario:"
echo "  php artisan cache:clear"
echo "  php artisan config:clear"
echo "  php artisan route:clear"
echo "  php artisan view:clear"
echo ""

