# Solución para Problemas de Login en Docker

## Problema
El login no funciona cuando la aplicación está corriendo en Docker.

## Causas Comunes en Docker

### 1. Headers de Proxy No Configurados
Nginx no está pasando los headers necesarios para que Laravel detecte correctamente el protocolo (HTTP/HTTPS) y configure las cookies.

**Solución aplicada:**
- Se actualizó `docker/nginx/default.conf` para pasar los headers `X-Forwarded-Proto`, `X-Forwarded-For`, etc.

### 2. Configuración de Cookies de Sesión
Las cookies de sesión no se están configurando correctamente en el entorno Docker.

**Solución aplicada:**
- `config/session.php` detecta automáticamente HTTPS basándose en los headers
- Si `SESSION_SECURE_COOKIE` no está definido en `.env`, se detecta automáticamente

### 3. Permisos de Storage
El directorio de sesiones dentro del contenedor puede no tener permisos correctos.

**Solución:**
```bash
docker-compose exec app chmod -R 775 storage/framework/sessions
docker-compose exec app chown -R www-data:www-data storage/framework/sessions
```

### 4. Tabla de Sesiones No Existe
Si el driver de sesiones es `database`, la tabla `sessions` debe existir.

**Solución:**
```bash
docker-compose exec app php artisan migrate
```

## Pasos para Solucionar

### Paso 1: Verificar Contenedores

```bash
# Verificar que los contenedores estén corriendo
docker-compose ps

# Ver logs del contenedor de la aplicación
docker-compose logs app

# Ver logs de Nginx
docker-compose logs nginx
```

### Paso 2: Reconstruir Contenedores con Nueva Configuración

```bash
# Detener contenedores
docker-compose down

# Reconstruir con la nueva configuración
docker-compose build --no-cache

# Iniciar contenedores
docker-compose up -d
```

### Paso 3: Configurar .env en Docker

Asegúrate de que tu archivo `.env` tenga estas configuraciones:

```env
# Configuración de la aplicación
APP_NAME=MAFIT
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

# Configuración de base de datos (usando el servicio mysql de Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=mafit
DB_PASSWORD=root

# Configuración de sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Dejar sin definir para detección automática (recomendado)
# SESSION_SECURE_COOKIE=

# Si usas HTTPS en producción, configurar:
# SESSION_SECURE_COOKIE=true
```

### Paso 4: Ejecutar Migraciones

```bash
# Ejecutar migraciones dentro del contenedor
docker-compose exec app php artisan migrate

# Si es la primera vez, también ejecutar seeders
docker-compose exec app php artisan db:seed
```

### Paso 5: Configurar Permisos

```bash
# Dar permisos a storage
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Paso 6: Limpiar Cachés

```bash
# Limpiar todos los cachés
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Regenerar cachés
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Paso 7: Verificar que Funciona

1. Accede a: `http://localhost:8080/login`
2. Intenta hacer login
3. Abre las herramientas de desarrollador (F12)
4. Ve a "Application" > "Cookies"
5. Verifica que la cookie de sesión se esté creando

## Diagnóstico en Docker

### Verificar Versión de PHP

```bash
docker-compose exec app php -v
```

Debe mostrar PHP 8.3.x

### Verificar Extensiones PHP

```bash
docker-compose exec app php -m
```

Debe incluir: pdo_mysql, mbstring, xml, curl, zip, gd, bcmath, intl, opcache

### Verificar Conexión a Base de Datos

```bash
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Verificar Tabla de Sesiones

```bash
docker-compose exec app php artisan tinker
>>> Schema::hasTable('sessions')
```

### Ver Logs en Tiempo Real

```bash
# Logs de la aplicación
docker-compose logs -f app

# Logs de Nginx
docker-compose logs -f nginx

# Todos los logs
docker-compose logs -f
```

## Configuración para HTTPS en Producción

Si usas HTTPS en producción con Docker:

### 1. Actualizar docker-compose.yml

```yaml
nginx:
  image: nginx:alpine
  container_name: mafit_nginx
  restart: unless-stopped
  ports:
    - "443:443"
    - "80:80"
  volumes:
    - ./:/var/www
    - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    - ./ssl:/etc/nginx/ssl  # Certificados SSL
  networks:
    - mafit-network
  depends_on:
    - app
```

### 2. Actualizar .env

```env
APP_URL=https://tu-dominio.com
SESSION_SECURE_COOKIE=true
```

### 3. Configurar Nginx para SSL

Actualiza `docker/nginx/default.conf` para incluir configuración SSL.

## Comandos Útiles

```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos artisan
docker-compose exec app php artisan [comando]

# Reiniciar contenedores
docker-compose restart

# Ver estado de contenedores
docker-compose ps

# Ver uso de recursos
docker stats

# Limpiar todo (¡CUIDADO! Elimina volúmenes)
docker-compose down -v
```

## Problemas Comunes

### Error: "Connection refused" al conectar a MySQL

**Solución:**
- Verifica que el servicio `mysql` esté corriendo: `docker-compose ps`
- En `.env`, usa `DB_HOST=mysql` (nombre del servicio, no `localhost`)

### Error: "Permission denied" en storage

**Solución:**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Las cookies no se guardan

**Solución:**
- Verifica que `APP_URL` en `.env` coincida con la URL que usas
- Verifica que los headers se estén pasando correctamente (ver `docker/nginx/default.conf`)
- Revisa la configuración de `SESSION_DOMAIN` si usas un dominio específico

### El login redirige pero no mantiene la sesión

**Solución:**
- Verifica que la tabla `sessions` exista: `docker-compose exec app php artisan migrate`
- Limpia las sesiones antiguas: `docker-compose exec app php artisan session:gc`
- Verifica los logs: `docker-compose logs app | grep -i session`

## Verificación Final

Después de aplicar todos los cambios:

1. **Reconstruir contenedores:**
```bash
docker-compose down
docker-compose build
docker-compose up -d
```

2. **Ejecutar migraciones:**
```bash
docker-compose exec app php artisan migrate
```

3. **Configurar permisos:**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

4. **Limpiar cachés:**
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

5. **Probar el login**

Si el problema persiste, revisa los logs:
```bash
docker-compose logs app | tail -50
```


