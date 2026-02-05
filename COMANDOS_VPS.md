# Comandos Rápidos para el VPS

## Verificar si usa Docker

```bash
# Verificar si Docker está instalado
docker --version

# Ver contenedores corriendo
docker ps

# Ver todos los contenedores (incluyendo detenidos)
docker ps -a

# Verificar si hay contenedores de mafit
docker ps -a | grep mafit

# Verificar si existe docker-compose.yml
cd /var/www/html/mafit
ls -la docker-compose.yml
```

## Si usa Docker - Solucionar Error 403

```bash
cd /var/www/html/mafit

# 1. Corregir permisos
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .

# 2. Verificar docker-compose.yml existe
cat docker-compose.yml

# 3. Reiniciar contenedores
docker compose down
docker compose up -d

# 4. Ver estado de contenedores
docker compose ps

# 5. Ver logs de Nginx
docker compose logs nginx

# 6. Ver logs de la aplicación
docker compose logs app
```

## Si NO usa Docker (Apache directo) - Solucionar Error 403

```bash
cd /var/www/html/mafit

# 1. Corregir permisos
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .

# 2. Verificar configuración de Apache
cat /etc/apache2/sites-available/*.conf | grep -A 5 "mafit\|DocumentRoot"

# 3. Verificar que DocumentRoot apunte a /var/www/html/mafit/public
# Si no, editar configuración:
nano /etc/apache2/sites-available/mafit.conf

# 4. Reiniciar Apache
systemctl restart apache2

# 5. Ver logs de Apache
tail -f /var/log/apache2/error.log
```

## Verificar qué servidor web está corriendo

```bash
# Verificar Apache
systemctl status apache2

# Verificar Nginx
systemctl status nginx

# Verificar puertos en uso
netstat -tulpn | grep -E "80|443|8080"

# Verificar procesos
ps aux | grep -E "apache|nginx|httpd"
```

## Comandos de diagnóstico rápido

```bash
# Ver estructura del proyecto
cd /var/www/html/mafit
ls -la

# Verificar que existe public/index.php
ls -la public/index.php

# Ver permisos del directorio public
ls -ld public/

# Ver configuración de Nginx en Docker (si usa Docker)
cat docker/nginx/default.conf

# Ver puertos expuestos en Docker
docker compose ps
```






