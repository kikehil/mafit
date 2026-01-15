# Guía Completa de Despliegue a VPS con Base de Datos

Esta guía te ayudará a subir la aplicación Laravel MAFIT y su base de datos a tu VPS.

## Paso 1: Preparar la Base de Datos Local

### 1.1 Exportar la base de datos desde tu máquina local

En tu máquina local (Windows), abre PowerShell o CMD y ejecuta:

```bash
# Si MySQL está en tu PATH
mysqldump -u root -p mafit > mafit_backup.sql

# O si MySQL está en XAMPP/WAMP
C:\xampp\mysql\bin\mysqldump.exe -u root -p mafit > mafit_backup.sql
```

Esto creará un archivo `mafit_backup.sql` con todos los datos de tu base de datos.

**Nota:** Te pedirá la contraseña de MySQL. Si no tienes contraseña, presiona Enter.

## Paso 2: Preparar el Código

### 2.1 Comprimir el proyecto (opcional pero recomendado)

En Windows, puedes usar PowerShell para crear un archivo ZIP:

```powershell
# Navega a la carpeta del proyecto
cd C:\WEB\MAFIT

# Crea un archivo ZIP excluyendo node_modules y vendor
Compress-Archive -Path * -DestinationPath ..\mafit.zip -Exclude node_modules,vendor,.git
```

O simplemente copia la carpeta completa excluyendo:
- `node_modules/`
- `vendor/` (lo instalaremos en el servidor)
- `.git/` (si no quieres subir el historial)

## Paso 3: Subir Archivos al VPS

### Opción A: Usando SCP (desde Windows con PowerShell)

```powershell
# Subir el código
scp -r C:\WEB\MAFIT usuario@tu-vps-ip:/var/www/mafit

# Subir la base de datos
scp C:\WEB\mafit_backup.sql usuario@tu-vps-ip:/tmp/mafit_backup.sql
```

### Opción B: Usando WinSCP (interfaz gráfica)

1. Descarga e instala WinSCP: https://winscp.net/
2. Conéctate a tu VPS
3. Arrastra la carpeta del proyecto a `/var/www/mafit`
4. Arrastra el archivo SQL a `/tmp/mafit_backup.sql`

### Opción C: Usando Git (recomendado para actualizaciones futuras)

```bash
# En el VPS
cd /var/www
git clone https://tu-repositorio.git mafit
```

## Paso 4: Configurar el Servidor VPS

### 4.1 Conectarse al VPS por SSH

```bash
ssh usuario@tu-vps-ip
```

### 4.2 Instalar dependencias del sistema

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl wget git unzip
```

### 4.3 Instalar PHP 8.3

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath
```

### 4.4 Instalar Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 4.5 Instalar MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 4.6 Instalar Nginx

```bash
sudo apt install -y nginx
```

## Paso 5: Configurar Base de Datos en el VPS

### 5.1 Crear base de datos y usuario

```bash
sudo mysql -u root -p
```

Dentro de MySQL:

```sql
CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mafit_user'@'localhost' IDENTIFIED BY 'tu_password_seguro_aqui';
GRANT ALL PRIVILEGES ON mafit.* TO 'mafit_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5.2 Importar la base de datos

```bash
# Importar desde el archivo que subiste
mysql -u mafit_user -p mafit < /tmp/mafit_backup.sql

# O si subiste a otra ubicación
mysql -u mafit_user -p mafit < /ruta/a/mafit_backup.sql
```

**Nota:** Te pedirá la contraseña que configuraste para `mafit_user`.

## Paso 6: Configurar la Aplicación Laravel

### 6.1 Navegar al directorio del proyecto

```bash
cd /var/www/mafit
```

### 6.2 Configurar permisos

```bash
sudo chown -R www-data:www-data /var/www/mafit
sudo chmod -R 755 /var/www/mafit
```

### 6.3 Instalar dependencias de Composer

```bash
composer install --optimize-autoloader --no-dev
```

### 6.4 Configurar archivo .env

```bash
cp .env.example .env
nano .env
```

Configura las siguientes variables (ajusta según tu VPS):

```env
APP_NAME=MAFIT
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=mafit_user
DB_PASSWORD=tu_password_seguro_aqui
```

### 6.5 Generar clave de aplicación

```bash
php artisan key:generate
```

### 6.6 Ejecutar migraciones (si hay nuevas)

```bash
php artisan migrate --force
```

**Nota:** Si ya importaste la base de datos completa, puedes omitir este paso o ejecutarlo para asegurar que todas las tablas estén actualizadas.

### 6.7 Optimizar para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6.8 Configurar permisos de storage

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Paso 7: Configurar Nginx

### 7.1 Crear configuración de sitio

```bash
sudo nano /etc/nginx/sites-available/mafit
```

Agregar la siguiente configuración:

```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/mafit/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7.2 Habilitar el sitio

```bash
sudo ln -s /etc/nginx/sites-available/mafit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Paso 8: Configurar SSL con Let's Encrypt (HTTPS)

### 8.1 Instalar Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 8.2 Obtener certificado SSL

```bash
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

Sigue las instrucciones en pantalla. Certbot configurará automáticamente HTTPS.

## Paso 9: Verificar el Despliegue

1. Visita `http://tu-dominio.com` o `https://tu-dominio.com`
2. Verifica que la aplicación carga correctamente
3. Prueba el login
4. Verifica que los datos se muestren correctamente

## Paso 10: Configurar Tareas Programadas (Cron)

```bash
sudo crontab -e -u www-data
```

Agregar:

```
* * * * * cd /var/www/mafit && php artisan schedule:run >> /dev/null 2>&1
```

## Comandos Útiles

### Ver logs de Laravel
```bash
tail -f /var/www/mafit/storage/logs/laravel.log
```

### Ver logs de Nginx
```bash
sudo tail -f /var/log/nginx/error.log
```

### Reiniciar servicios
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo systemctl restart mysql
```

### Actualizar la aplicación (después del despliegue inicial)

```bash
cd /var/www/mafit
git pull origin main  # Si usas Git
# O sube los archivos nuevos manualmente

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Solución de Problemas

### Error 500
- Verificar permisos: `sudo chown -R www-data:www-data /var/www/mafit`
- Verificar logs: `tail -f storage/logs/laravel.log`
- Verificar .env está configurado correctamente

### Error de conexión a base de datos
- Verificar credenciales en .env
- Verificar que MySQL está corriendo: `sudo systemctl status mysql`
- Verificar que el usuario tiene permisos: `mysql -u mafit_user -p`

### Archivos no se cargan (CSS/JS)
- Ejecutar: `php artisan storage:link`
- Verificar permisos de storage
- Limpiar cache: `php artisan cache:clear`

### Base de datos no se importa correctamente
- Verificar que el archivo SQL no esté corrupto
- Verificar que la base de datos existe
- Verificar permisos del usuario MySQL
- Verificar codificación: `SHOW VARIABLES LIKE 'character_set%';`

## Notas Importantes

1. **Seguridad**: 
   - Nunca subas el archivo `.env` a un repositorio público
   - Usa contraseñas seguras para MySQL
   - Mantén el servidor actualizado

2. **Backups**: 
   - Configura backups regulares de la base de datos
   - Puedes usar un script cron para hacer backups automáticos

3. **Firewall**: 
   - Configura un firewall (UFW) para proteger el servidor:
   ```bash
   sudo ufw allow 22/tcp
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

4. **Actualizaciones**: 
   - Mantén el servidor y las dependencias actualizadas regularmente

## Script de Backup Automático (Opcional)

Crea un script para hacer backups automáticos:

```bash
sudo nano /usr/local/bin/backup-mafit.sh
```

Contenido:

```bash
#!/bin/bash
BACKUP_DIR="/backups/mafit"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u mafit_user -p'tu_password' mafit > $BACKUP_DIR/mafit_$DATE.sql

# Comprimir
gzip $BACKUP_DIR/mafit_$DATE.sql

# Eliminar backups más antiguos de 30 días
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completado: mafit_$DATE.sql.gz"
```

Hacer ejecutable:

```bash
sudo chmod +x /usr/local/bin/backup-mafit.sh
```

Agregar a cron (diario a las 2 AM):

```bash
sudo crontab -e
# Agregar:
0 2 * * * /usr/local/bin/backup-mafit.sh >> /var/log/mafit-backup.log 2>&1
```

