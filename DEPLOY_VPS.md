# Guía de Despliegue en VPS

Esta guía te ayudará a subir e implementar la aplicación Laravel MAFIT en tu VPS.

## Requisitos Previos

- VPS con Ubuntu 20.04 o superior (recomendado)
- Acceso SSH al servidor
- Dominio configurado (opcional pero recomendado)

## Paso 1: Preparar el Servidor

### 1.1 Actualizar el sistema
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalar dependencias básicas
```bash
sudo apt install -y software-properties-common curl wget git unzip
```

### 1.3 Instalar PHP 8.3 y extensiones necesarias
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath
```

### 1.4 Instalar Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 1.5 Instalar MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 1.6 Instalar Nginx
```bash
sudo apt install -y nginx
```

## Paso 2: Configurar Base de Datos

### 2.1 Crear base de datos y usuario
```bash
sudo mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mafit_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON mafit.* TO 'mafit_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Paso 3: Subir el Código

### 3.1 Opción A: Usando Git (Recomendado)
```bash
cd /var/www
sudo git clone https://tu-repositorio.git mafit
sudo chown -R www-data:www-data mafit
cd mafit
```

### 3.2 Opción B: Usando SCP/SFTP
Desde tu máquina local:
```bash
scp -r C:\WEB\MAFIT usuario@tu-vps-ip:/var/www/mafit
```

Luego en el servidor:
```bash
cd /var/www/mafit
sudo chown -R www-data:www-data /var/www/mafit
```

## Paso 4: Configurar la Aplicación

### 4.1 Instalar dependencias
```bash
cd /var/www/mafit
composer install --optimize-autoloader --no-dev
```

### 4.2 Configurar archivo .env
```bash
cp .env.example .env
nano .env
```

Configurar las siguientes variables:
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
DB_PASSWORD=tu_password_seguro
```

### 4.3 Generar clave de aplicación
```bash
php artisan key:generate
```

### 4.4 Ejecutar migraciones
```bash
php artisan migrate --force
```

### 4.5 Optimizar para producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Paso 5: Configurar Nginx

### 5.1 Crear configuración de sitio
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

### 5.2 Habilitar el sitio
```bash
sudo ln -s /etc/nginx/sites-available/mafit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Paso 6: Configurar SSL con Let's Encrypt (HTTPS)

### 6.1 Instalar Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 6.2 Obtener certificado SSL
```bash
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

Sigue las instrucciones en pantalla. Certbot configurará automáticamente HTTPS.

## Paso 7: Configurar Permisos

```bash
cd /var/www/mafit
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Paso 8: Configurar Queue Worker (Opcional)

Si usas colas, crear un servicio systemd:
```bash
sudo nano /etc/systemd/system/mafit-queue.service
```

Contenido:
```ini
[Unit]
Description=MAFIT Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/mafit/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Activar:
```bash
sudo systemctl enable mafit-queue
sudo systemctl start mafit-queue
```

## Paso 9: Configurar Tareas Programadas (Cron)

```bash
sudo crontab -e -u www-data
```

Agregar:
```
* * * * * cd /var/www/mafit && php artisan schedule:run >> /dev/null 2>&1
```

## Paso 10: Verificar el Despliegue

1. Visita `http://tu-dominio.com` o `https://tu-dominio.com`
2. Verifica que la aplicación carga correctamente
3. Prueba el login
4. Verifica que las migraciones se ejecutaron correctamente

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
```

### Actualizar la aplicación
```bash
cd /var/www/mafit
git pull origin main
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
- Verificar que el usuario tiene permisos

### Archivos no se cargan (CSS/JS)
- Ejecutar: `php artisan storage:link`
- Verificar permisos de storage
- Limpiar cache: `php artisan cache:clear`

## Notas Importantes

1. **Seguridad**: Nunca subas el archivo `.env` a un repositorio público
2. **Backups**: Configura backups regulares de la base de datos
3. **Firewall**: Configura un firewall (UFW) para proteger el servidor
4. **Actualizaciones**: Mantén el servidor y las dependencias actualizadas







