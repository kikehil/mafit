#!/bin/bash
# Script de diagnóstico para problemas de login en Docker
# Ejecutar desde la raíz del proyecto: bash diagnosticar-login-docker.sh

echo "=== DIAGNÓSTICO DE LOGIN EN DOCKER ==="
echo ""

# Verificar que docker-compose esté disponible
if ! command -v docker-compose &> /dev/null; then
    echo "⚠️  ERROR: docker-compose no está instalado"
    exit 1
fi

# Verificar que los contenedores estén corriendo
echo "1. ESTADO DE CONTENEDORES:"
if docker-compose ps | grep -q "Up"; then
    echo "   ✓ Contenedores están corriendo"
    docker-compose ps
else
    echo "   ⚠️  ERROR: Los contenedores no están corriendo"
    echo "      Ejecuta: docker-compose up -d"
    exit 1
fi
echo ""

# Verificar versión de PHP
echo "2. VERSIÓN DE PHP:"
PHP_VERSION=$(docker-compose exec -T app php -v | head -n 1)
echo "   $PHP_VERSION"
if docker-compose exec -T app php -r "echo version_compare(PHP_VERSION, '8.3.0', '>=') ? 'OK' : 'ERROR';" 2>/dev/null | grep -q "OK"; then
    echo "   ✓ Versión de PHP compatible (>= 8.3.0)"
else
    echo "   ⚠️  ERROR: Se requiere PHP >= 8.3.0"
fi
echo ""

# Verificar extensiones PHP
echo "3. EXTENSIONES PHP:"
REQUIRED_EXT=("pdo_mysql" "mbstring" "xml" "curl" "zip" "gd" "bcmath" "intl" "opcache")
for ext in "${REQUIRED_EXT[@]}"; do
    if docker-compose exec -T app php -m | grep -q "^$ext$"; then
        echo "   ✓ $ext"
    else
        echo "   ✗ $ext (FALTA)"
    fi
done
echo ""

# Verificar conexión a base de datos
echo "4. CONEXIÓN A BASE DE DATOS:"
if docker-compose exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; then
    echo "   ✓ Conexión a base de datos: OK"
    
    # Verificar tabla de sesiones
    if docker-compose exec -T app php artisan tinker --execute="echo Schema::hasTable('sessions') ? 'SÍ' : 'NO';" 2>/dev/null | grep -q "SÍ"; then
        echo "   ✓ Tabla 'sessions' existe"
        
        # Contar sesiones
        SESSION_COUNT=$(docker-compose exec -T app php artisan tinker --execute="echo DB::table('sessions')->count();" 2>/dev/null | tail -1)
        echo "   - Sesiones activas: $SESSION_COUNT"
    else
        echo "   ⚠️  ERROR: La tabla 'sessions' NO existe"
        echo "      Ejecuta: docker-compose exec app php artisan migrate"
    fi
    
    # Verificar tabla de usuarios
    if docker-compose exec -T app php artisan tinker --execute="echo Schema::hasTable('users') ? 'SÍ' : 'NO';" 2>/dev/null | grep -q "SÍ"; then
        echo "   ✓ Tabla 'users' existe"
        
        # Contar usuarios
        USER_COUNT=$(docker-compose exec -T app php artisan tinker --execute="echo DB::table('users')->count();" 2>/dev/null | tail -1)
        echo "   - Total de usuarios: $USER_COUNT"
    else
        echo "   ⚠️  ERROR: La tabla 'users' NO existe"
        echo "      Ejecuta: docker-compose exec app php artisan migrate"
    fi
else
    echo "   ⚠️  ERROR: No se pudo conectar a la base de datos"
    echo "      Verifica la configuración en .env"
fi
echo ""

# Verificar permisos de storage
echo "5. PERMISOS DE STORAGE:"
if docker-compose exec -T app test -w storage/framework/sessions 2>/dev/null; then
    echo "   ✓ Permisos de escritura en storage/framework/sessions: OK"
else
    echo "   ⚠️  ERROR: No hay permisos de escritura en storage/framework/sessions"
    echo "      Ejecuta: docker-compose exec app chmod -R 775 storage/framework/sessions"
fi

if docker-compose exec -T app test -w bootstrap/cache 2>/dev/null; then
    echo "   ✓ Permisos de escritura en bootstrap/cache: OK"
else
    echo "   ⚠️  ERROR: No hay permisos de escritura en bootstrap/cache"
    echo "      Ejecuta: docker-compose exec app chmod -R 775 bootstrap/cache"
fi
echo ""

# Verificar configuración de .env
echo "6. CONFIGURACIÓN DE .env:"
if [ -f .env ]; then
    echo "   ✓ Archivo .env existe"
    
    # Verificar configuraciones importantes
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
        echo "   - APP_URL: $APP_URL"
    else
        echo "   ⚠️  APP_URL no está definido"
    fi
    
    if grep -q "^SESSION_DRIVER=" .env; then
        SESSION_DRIVER=$(grep "^SESSION_DRIVER=" .env | cut -d '=' -f2)
        echo "   - SESSION_DRIVER: $SESSION_DRIVER"
    else
        echo "   - SESSION_DRIVER: database (por defecto)"
    fi
    
    if grep -q "^DB_HOST=" .env; then
        DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2)
        echo "   - DB_HOST: $DB_HOST"
        if [ "$DB_HOST" != "mysql" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
            echo "   ⚠️  ADVERTENCIA: En Docker, DB_HOST debería ser 'mysql' (nombre del servicio)"
        fi
    fi
else
    echo "   ⚠️  ERROR: El archivo .env no existe"
    echo "      Copia .env.example a .env y configúralo"
fi
echo ""

# Verificar configuración de Nginx
echo "7. CONFIGURACIÓN DE NGINX:"
if [ -f docker/nginx/default.conf ]; then
    echo "   ✓ Archivo de configuración de Nginx existe"
    
    if grep -q "X-Forwarded-Proto" docker/nginx/default.conf; then
        echo "   ✓ Headers de proxy configurados"
    else
        echo "   ⚠️  ADVERTENCIA: Headers de proxy no configurados"
        echo "      Esto puede causar problemas con las cookies de sesión"
    fi
else
    echo "   ⚠️  ERROR: Archivo de configuración de Nginx no existe"
fi
echo ""

# Verificar logs recientes
echo "8. LOGS RECIENTES (últimas 10 líneas):"
echo "   Aplicación:"
docker-compose logs --tail=10 app 2>/dev/null | sed 's/^/   /'
echo ""
echo "   Nginx:"
docker-compose logs --tail=10 nginx 2>/dev/null | sed 's/^/   /'
echo ""

# Recomendaciones
echo "=== RECOMENDACIONES ==="
echo ""
echo "Si hay errores, ejecuta estos comandos en orden:"
echo ""
echo "1. Reconstruir contenedores:"
echo "   docker-compose down"
echo "   docker-compose build --no-cache"
echo "   docker-compose up -d"
echo ""
echo "2. Ejecutar migraciones:"
echo "   docker-compose exec app php artisan migrate"
echo ""
echo "3. Configurar permisos:"
echo "   docker-compose exec app chmod -R 775 storage bootstrap/cache"
echo "   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache"
echo ""
echo "4. Limpiar cachés:"
echo "   docker-compose exec app php artisan config:clear"
echo "   docker-compose exec app php artisan cache:clear"
echo "   docker-compose exec app php artisan route:clear"
echo "   docker-compose exec app php artisan view:clear"
echo ""
echo "5. Regenerar cachés:"
echo "   docker-compose exec app php artisan config:cache"
echo "   docker-compose exec app php artisan route:cache"
echo ""
echo "=== FIN DEL DIAGNÓSTICO ==="


