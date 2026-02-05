# Cómo Actualizar el VPS desde Git

## Opción 1: Usar el Script Automático (Recomendado)

### Paso 1: Conectarse al VPS
```bash
ssh root@tu-servidor-vps
# o
ssh usuario@tu-servidor-vps
```

### Paso 2: Ir al directorio del proyecto
```bash
cd /var/www/html/mafit
```

### Paso 3: Ejecutar el script de actualización
```bash
bash actualizar-desde-git.sh
```

Este script automáticamente:
- ✅ Hace pull desde Git
- ✅ Instala dependencias de Composer
- ✅ Compila assets (si hay NPM)
- ✅ Ejecuta migraciones
- ✅ Limpia y optimiza cachés
- ✅ Configura permisos

## Opción 2: Actualización Manual

Si prefieres hacerlo manualmente:

### Paso 1: Conectarse al VPS
```bash
ssh root@tu-servidor-vps
cd /var/www/html/mafit
```

### Paso 2: Hacer pull desde Git
```bash
git pull origin main
```

### Paso 3: Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
```

### Paso 4: Limpiar y regenerar cachés
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 5: Configurar permisos
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Paso 6: Reiniciar servicios (si es necesario)
```bash
# Si usas PHP-FPM
sudo systemctl restart php8.3-fpm

# Si usas Apache
sudo systemctl restart apache2

# Si usas Nginx
sudo systemctl reload nginx
```

## Opción 3: Si hay Conflictos

Si el pull falla por conflictos:

### Opción A: Descartar cambios locales y usar remoto
```bash
cd /var/www/html/mafit

# Respaldar .env primero
cp .env .env.backup

# Descartar cambios locales
git fetch origin main
git reset --hard origin/main

# Restaurar .env
cp .env.backup .env
```

### Opción B: Usar el script de sincronización
```bash
cd /var/www/html/mafit
bash sincronizar-vps-desde-git.sh
```

## Verificar que Funcionó

### 1. Verificar que los archivos nuevos estén presentes:
```bash
ls -la SOLUCION_LOGIN_*.md
ls -la diagnosticar-login-*
```

### 2. Verificar la configuración de sesiones:
```bash
php artisan config:show session
```

### 3. Probar el login:
- Accede a la URL del sitio
- Intenta hacer login
- Verifica que funcione correctamente

## Si el VPS no está conectado a Git

Si el directorio no es un repositorio Git:

### Opción 1: Clonar desde cero
```bash
cd /var/www/html
rm -rf mafit  # ¡CUIDADO! Respaldar primero
git clone https://github.com/kikehil/mafit.git
cd mafit
cp .env.example .env
nano .env  # Configurar .env
```

### Opción 2: Inicializar Git en el directorio existente
```bash
cd /var/www/html/mafit
git init
git remote add origin https://github.com/kikehil/mafit.git
git fetch origin
git checkout -b main origin/main
```

## Después de Actualizar

### 1. Ejecutar migraciones (si hay nuevas)
```bash
php artisan migrate --force
```

### 2. Verificar logs
```bash
tail -f storage/logs/laravel.log
```

### 3. Probar funcionalidades
- Login
- Navegación
- Funciones principales

## Solución de Problemas

### Error: "Permission denied"
```bash
sudo chown -R www-data:www-data /var/www/html/mafit
sudo chmod -R 755 /var/www/html/mafit
```

### Error: "Composer not found"
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Error: "PHP version too old"
```bash
# Ver versión actual
php -v

# Si es menor a 8.3, actualizar:
bash actualizar-php-vps-debian.sh
```

### Error: "Git pull failed"
```bash
# Verificar conexión
git remote -v

# Forzar actualización
git fetch origin main
git reset --hard origin/main
```

## Comandos Rápidos

```bash
# Actualización rápida (solo código)
cd /var/www/html/mafit && git pull origin main && php artisan config:clear && php artisan cache:clear

# Actualización completa
cd /var/www/html/mafit && bash actualizar-desde-git.sh

# Ver cambios pendientes
cd /var/www/html/mafit && git status

# Ver último commit
cd /var/www/html/mafit && git log -1
```

## Notas Importantes

1. **Respaldar .env**: Siempre respalda tu `.env` antes de actualizar
2. **Verificar PHP**: Asegúrate de tener PHP 8.3+ instalado
3. **Permisos**: Verifica que los permisos de `storage` sean correctos
4. **Base de datos**: Si hay nuevas migraciones, ejecútalas después del pull
5. **Cachés**: Siempre limpia los cachés después de actualizar


