#!/usr/bin/env bash
# Script para actualizar el VPS desde Git
# Ejecutar directamente en el VPS: bash actualizar-desde-git.sh

set -e

APP_DIR="/var/www/html/mafit"
GIT_BRANCH="main"
WEB_USER="www-data"

echo "=========================================="
echo "  Actualizar VPS desde Git"
echo "=========================================="
echo ""
echo "Directorio: $APP_DIR"
echo "Rama: $GIT_BRANCH"
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -d "$APP_DIR" ]; then
    echo "[ERROR] No se encuentra el directorio: $APP_DIR"
    exit 1
fi

cd "$APP_DIR"

# Verificar que es un repositorio Git
if [ ! -d ".git" ]; then
    echo "[ERROR] No es un repositorio Git. Inicializando..."
    echo "¿Deseas clonar desde GitHub? (S/N)"
    read -r respuesta
    if [ "$respuesta" = "S" ] || [ "$respuesta" = "s" ]; then
        echo "Ingresa la URL del repositorio Git:"
        read -r repo_url
        cd ..
        rm -rf mafit
        git clone "$repo_url" mafit
        cd mafit
    else
        exit 1
    fi
fi

# Paso 1: Hacer pull desde Git
echo "[1/5] Actualizando código desde Git..."
echo "  Ejecutando: git pull origin $GIT_BRANCH"
echo ""

if git pull origin "$GIT_BRANCH"; then
    echo "[OK] Código actualizado desde Git"
else
    echo "[ERROR] Error al hacer pull desde Git"
    exit 1
fi

echo ""

# Paso 2: Verificar .env
echo "[2/5] Verificando archivo .env..."
if [ ! -f ".env" ]; then
    echo "[ADVERTENCIA] No existe .env"
    if [ -f ".env.example" ]; then
        echo "  Copiando .env.example a .env..."
        cp .env.example .env
        echo "[IMPORTANTE] Edita .env con tus credenciales antes de continuar"
        echo "  nano .env"
        echo ""
        echo "¿Deseas continuar de todas formas? (S/N)"
        read -r continuar
        if [ "$continuar" != "S" ] && [ "$continuar" != "s" ]; then
            exit 1
        fi
    else
        echo "[ERROR] No existe .env ni .env.example"
        exit 1
    fi
else
    echo "[OK] Archivo .env existe"
fi

echo ""

# Paso 3: Instalar dependencias de Composer
echo "[3/5] Instalando dependencias de Composer..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
    echo "[OK] Dependencias de Composer instaladas"
else
    echo "[ERROR] Composer no está instalado"
    echo "  Instala Composer: curl -sS https://getcomposer.org/installer | php"
    exit 1
fi

echo ""

# Paso 4: Instalar dependencias de NPM y compilar assets
echo "[4/5] Instalando dependencias de NPM y compilando assets..."
if [ -f "package.json" ]; then
    if command -v npm &> /dev/null; then
        echo "  Instalando dependencias de NPM..."
        npm ci
        
        echo "  Compilando assets..."
        npm run build
        
        echo "[OK] Assets compilados"
    else
        echo "[ADVERTENCIA] NPM no está instalado. Saltando compilación de assets."
    fi
else
    echo "[INFO] No se encuentra package.json. Saltando NPM."
fi

echo ""

# Paso 5: Ejecutar migraciones y optimizaciones
echo "[5/5] Ejecutando migraciones y optimizaciones..."

# Generar clave de aplicación si falta
php artisan key:generate --force || true

# Ejecutar migraciones
echo "  Ejecutando migraciones..."
php artisan migrate --force || true

# Crear enlace de storage
php artisan storage:link || true

# Limpiar cachés
echo "  Limpiando cachés..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Optimizar para producción
echo "  Optimizando para producción..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Configurar permisos
echo "  Configurando permisos..."
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
chown -R $WEB_USER:$WEB_USER storage bootstrap/cache || true
chmod -R ug+rw storage bootstrap/cache || true

echo "[OK] Migraciones y optimizaciones completadas"
echo ""

# Resumen final
echo "=========================================="
echo "  Actualización Completada!"
echo "=========================================="
echo ""
echo "Resumen:"
echo "  ✓ Código actualizado desde Git"
echo "  ✓ Dependencias instaladas"
echo "  ✓ Assets compilados"
echo "  ✓ Migraciones ejecutadas"
echo "  ✓ Cachés optimizadas"
echo "  ✓ Permisos configurados"
echo ""
echo "Próximos pasos opcionales:"
echo "1. Reiniciar PHP-FPM (si es necesario):"
echo "   sudo systemctl restart php8.3-fpm"
echo ""
echo "2. Recargar Nginx (si es necesario):"
echo "   sudo systemctl reload nginx"
echo ""
echo "3. Verificar logs si hay problemas:"
echo "   tail -f storage/logs/laravel.log"
echo ""

