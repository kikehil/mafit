# Solución para Problemas de Login en VPS

## Problema
El login no funciona en el VPS, aunque funciona correctamente en desarrollo local.

## ¿Estás usando Docker?

Si tu aplicación está corriendo en Docker, consulta primero: **[SOLUCION_LOGIN_DOCKER.md](SOLUCION_LOGIN_DOCKER.md)**

Los problemas y soluciones son diferentes cuando se usa Docker.

## ⚠️ PROBLEMA CRÍTICO: Versión de PHP

**El problema más común es que el VPS tiene una versión de PHP incompatible.**

El proyecto requiere **PHP >= 8.3.0**, pero muchos VPS tienen versiones antiguas (7.4, 8.0, 8.1, 8.2).

**Síntomas:**
- Error: "Composer detected issues in your platform: Your Composer dependencies require a PHP version >= 8.3.0"
- La aplicación no carga correctamente
- El login no funciona

**Solución: Actualizar PHP a 8.3 o superior**

Ver sección "Actualizar PHP en el VPS" más abajo.

## Causas Comunes

### 1. Configuración de Cookies de Sesión
El problema más común es que las cookies de sesión no se están configurando correctamente para el entorno del VPS.

**Solución aplicada:**
- Se actualizó `config/session.php` para detectar automáticamente HTTPS y configurar las cookies correctamente
- Si `SESSION_SECURE_COOKIE` no está definido en `.env`, se detecta automáticamente según el protocolo

### 2. Tabla de Sesiones No Existe
Si el driver de sesiones es `database`, la tabla `sessions` debe existir.

**Solución:**
```bash
php artisan migrate
```

### 3. Permisos de Storage
El directorio de sesiones debe tener permisos de escritura.

**Solución:**
```bash
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

### 4. Configuración de .env en VPS

Asegúrate de que tu archivo `.env` en el VPS tenga estas configuraciones:

```env
# Configuración de sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Si el sitio usa HTTPS, configurar:
SESSION_SECURE_COOKIE=true
# Si el sitio usa HTTP, configurar:
SESSION_SECURE_COOKIE=false

# O dejar sin definir para detección automática (recomendado)
# SESSION_SECURE_COOKIE=

# Configuración de dominio (solo si es necesario)
# SESSION_DOMAIN=.tudominio.com

# APP_URL debe coincidir con tu dominio
APP_URL=https://mafit.regiontamaulipas.com.mx
# o
APP_URL=http://mafit.regiontamaulipas.com.mx
```

### 5. Verificar Configuración de Apache/Nginx

Si usas un proxy inverso o load balancer, asegúrate de que los headers de HTTPS se pasen correctamente.

**Para Apache:**
El archivo `.htaccess` ya está configurado para detectar `X-Forwarded-Proto`.

**Para Nginx:**
```nginx
proxy_set_header X-Forwarded-Proto $scheme;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_set_header Host $host;
```

## Pasos para Diagnosticar

1. **Ejecutar el script de diagnóstico simplificado (no requiere Laravel):**
```bash
php diagnosticar-login-vps-simple.php
```

   Si tienes PHP 8.3+, también puedes usar el diagnóstico completo:
```bash
php diagnosticar-login-vps.php
```

2. **Revisar los logs de Laravel:**
```bash
tail -f storage/logs/laravel.log
```

3. **Verificar que la tabla de sesiones existe:**
```bash
php artisan tinker
>>> Schema::hasTable('sessions')
```

4. **Probar el login y revisar las cookies en el navegador:**
   - Abre las herramientas de desarrollador (F12)
   - Ve a la pestaña "Application" > "Cookies"
   - Verifica que la cookie de sesión se esté creando
   - Verifica que el dominio y la ruta sean correctos

## Soluciones Aplicadas

### 1. Configuración Automática de Cookies Seguras
Se modificó `config/session.php` para detectar automáticamente si el sitio usa HTTPS y configurar las cookies `secure` correctamente.

### 2. Mejora en el Controlador de Autenticación
Se agregó verificación adicional en `AuthenticatedSessionController` para asegurar que la sesión se guarde correctamente después del login.

## Comandos Útiles

```bash
# Limpiar caché de configuración
php artisan config:clear
php artisan cache:clear

# Limpiar sesiones antiguas
php artisan session:gc

# Verificar configuración actual
php artisan config:show session

# Verificar rutas de autenticación
php artisan route:list | grep login
```

## Verificación Final

Después de aplicar los cambios:

1. Limpia la caché:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

2. Reinicia el servidor web (si es necesario):
```bash
sudo systemctl restart apache2
# o
sudo systemctl restart nginx
```

3. Prueba el login nuevamente

## Actualizar PHP en el VPS

Si el diagnóstico muestra que tienes PHP < 8.3, debes actualizar PHP:

### Para Ubuntu/Debian:

```bash
# 1. Agregar repositorio de PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# 2. Instalar PHP 8.3 y extensiones necesarias
sudo apt install -y php8.3 \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-opcache

# 3. Configurar PHP 8.3 como versión predeterminada
sudo update-alternatives --set php /usr/bin/php8.3

# 4. Verificar versión
php -v

# 5. Reiniciar servidor web
# Para Apache:
sudo systemctl restart apache2

# Para Nginx con PHP-FPM:
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Configurar Apache para usar PHP 8.3:

```bash
# Habilitar módulo PHP 8.3
sudo a2dismod php7.4  # o la versión anterior
sudo a2enmod php8.3
sudo systemctl restart apache2
```

### Verificar configuración:

```bash
# Ver versión de PHP CLI
php -v

# Ver versión de PHP en Apache (crear archivo info.php)
echo "<?php phpinfo(); ?>" > /var/www/html/mafit/public/info.php
# Visitar: http://tu-dominio.com/info.php
# Eliminar después: rm /var/www/html/mafit/public/info.php
```

### Después de actualizar PHP:

```bash
# 1. Reinstalar dependencias de Composer
cd /var/www/html/mafit
composer install --optimize-autoloader --no-dev

# 2. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Regenerar caché de configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Si el Problema Persiste

1. **Revisa los logs:**
   - `storage/logs/laravel.log`
   - Logs del servidor web (Apache/Nginx)

2. **Verifica la base de datos:**
   - Que la tabla `sessions` exista
   - Que haya conexión a la base de datos
   - Que los usuarios existan en la tabla `users`

3. **Verifica permisos:**
   - `storage/` debe ser escribible
   - `bootstrap/cache/` debe ser escribible

4. **Verifica la configuración del servidor:**
   - Que PHP esté configurado correctamente
   - Que las extensiones necesarias estén habilitadas
   - Que el servidor web esté configurado para pasar las solicitudes a Laravel

## Contacto

Si el problema persiste después de seguir estos pasos, proporciona:
- Salida del script de diagnóstico
- Logs de Laravel
- Configuración de `.env` (sin datos sensibles)
- Configuración del servidor web

