# Guía Rápida de Implementación en VPS

Esta guía te ayudará a implementar MAFIT en tu VPS de forma rápida y sencilla.

## 📋 Requisitos Previos

- VPS con Ubuntu 20.04 o superior
- Acceso SSH al servidor
- Dominio configurado (opcional, puedes usar la IP)
- Base de datos exportada (si tienes datos locales)

## 💾 Exportar Base de Datos (Windows)

### Opción 1: Script PowerShell (Recomendado)

```powershell
# Ejecutar en PowerShell desde la carpeta del proyecto
.\exportar-bd.ps1
```

Este script detecta automáticamente si usas XAMPP, WAMP, Laragon o MySQL del sistema.

### Opción 2: Comando Manual

Si usas **XAMPP** (sin contraseña):
```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root mafit > mafit_backup.sql
```

Si usas **XAMPP** (con contraseña):
```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root -p mafit > mafit_backup.sql
```

Si usas **WAMP**:
```powershell
# Busca la ruta de MySQL en: C:\wamp64\bin\mysql\mysql8.x.x\bin\
C:\wamp64\bin\mysql\mysql8.0.xx\bin\mysqldump.exe -u root -p mafit > mafit_backup.sql
```

## 🚀 Opción 1: Despliegue Automatizado (Recomendado)

### Paso 1: Subir el proyecto al VPS

Desde tu máquina Windows, usa PowerShell o WinSCP:

```powershell
# Opción A: Usando SCP (PowerShell)
scp -r C:\WEB\MAFIT usuario@tu-vps-ip:/var/www/mafit

# Opción B: Usando WinSCP (interfaz gráfica)
# 1. Descarga WinSCP: https://winscp.net/
# 2. Conéctate a tu VPS
# 3. Arrastra la carpeta C:\WEB\MAFIT a /var/www/mafit
```

### Paso 2: Subir el script de despliegue

```powershell
scp C:\WEB\MAFIT\deploy-vps.sh usuario@tu-vps-ip:/tmp/deploy-vps.sh
```

### Paso 3: Conectarse al VPS y ejecutar el script

```bash
# Conectarse al VPS
ssh usuario@tu-vps-ip

# Dar permisos de ejecución
chmod +x /tmp/deploy-vps.sh

# Ejecutar el script
sudo /tmp/deploy-vps.sh
```

El script te pedirá:
- Dominio o IP del servidor
- Contraseña para el usuario de MySQL
- Si deseas instalar Node.js (recomendado: sí)

### Paso 4: Importar la base de datos (si tienes datos)

```bash
# Subir el archivo SQL desde Windows
scp C:\WEB\MAFIT\mafit_backup.sql usuario@tu-vps-ip:/tmp/mafit_backup.sql

# En el VPS, importar
mysql -u mafit_user -p mafit < /tmp/mafit_backup.sql
```

### Paso 5: Crear usuario administrador

```bash
cd /var/www/mafit
php artisan tinker
```

En la consola de Tinker:

```php
$user = App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@tudominio.com',
    'password' => Hash::make('tu_password_seguro'),
    'role' => 'admin',
    'plaza' => '32YXH' // Usa un código de plaza existente
]);
```

¡Listo! Visita `http://tu-dominio.com` o `https://tu-dominio.com`

---

## 🔧 Opción 2: Despliegue Manual

Si prefieres hacerlo paso a paso, sigue la guía completa en `GUIA_DESPLIEGUE_COMPLETA.md`.

---

## 📦 Preparar Base de Datos Local (Antes de Subir)

Si tienes datos en tu base de datos local, expórtalos primero:

### En Windows (XAMPP/WAMP):

```bash
# Abrir PowerShell o CMD
cd C:\WEB\MAFIT

# Exportar base de datos
C:\xampp\mysql\bin\mysqldump.exe -u root -p mafit > mafit_backup.sql

# O si MySQL está en tu PATH
mysqldump -u root -p mafit > mafit_backup.sql
```

---

## 🔐 Configurar SSL (HTTPS)

Si tienes un dominio, el script puede configurar SSL automáticamente. Si no, puedes hacerlo manualmente:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

---

## 🔄 Actualizar la Aplicación (Después del Despliegue)

Cuando hagas cambios y quieras actualizar:

```bash
# Conectarse al VPS
ssh usuario@tu-vps-ip

# Ir al directorio del proyecto
cd /var/www/mafit

# Si usas Git:
git pull origin main

# O sube los archivos nuevos manualmente con SCP/WinSCP

# Actualizar dependencias
composer install --no-dev --optimize-autoloader

# Ejecutar migraciones (si hay nuevas)
php artisan migrate --force

# Limpiar y regenerar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Si cambiaste assets frontend
npm install
npm run build
```

---

## 🐛 Solución de Problemas

### Error 500

```bash
# Verificar permisos
sudo chown -R www-data:www-data /var/www/mafit
sudo chmod -R 755 /var/www/mafit
sudo chmod -R 775 /var/www/mafit/storage /var/www/mafit/bootstrap/cache

# Ver logs
tail -f /var/www/mafit/storage/logs/laravel.log
```

### Error de conexión a base de datos

```bash
# Verificar que MySQL está corriendo
sudo systemctl status mysql

# Verificar credenciales en .env
cat /var/www/mafit/.env | grep DB_
```

### CSS/JS no se cargan

```bash
cd /var/www/mafit
php artisan storage:link
npm run build
php artisan cache:clear
```

### Verificar servicios

```bash
# Ver estado de servicios
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql

# Reiniciar servicios
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo systemctl restart mysql
```

---

## 📝 Comandos Útiles

### Ver logs en tiempo real

```bash
# Logs de Laravel
tail -f /var/www/mafit/storage/logs/laravel.log

# Logs de Nginx
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### Limpiar cache

```bash
cd /var/www/mafit
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Verificar configuración

```bash
# Verificar configuración de Nginx
sudo nginx -t

# Ver versión de PHP
php -v

# Ver versión de Composer
composer --version
```

---

## 🔒 Seguridad

### Configurar Firewall (UFW)

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### Backup Automático

Crea un script de backup (ver `GUIA_DESPLIEGUE_COMPLETA.md` para el script completo):

```bash
sudo nano /usr/local/bin/backup-mafit.sh
```

Agregar a cron:

```bash
sudo crontab -e
# Agregar: 0 2 * * * /usr/local/bin/backup-mafit.sh
```

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa los logs: `/var/www/mafit/storage/logs/laravel.log`
2. Verifica permisos de archivos
3. Verifica configuración de `.env`
4. Verifica que todos los servicios estén corriendo

---

## ✅ Checklist Post-Despliegue

- [ ] Aplicación accesible en el navegador
- [ ] Login funciona correctamente
- [ ] Base de datos importada (si aplica)
- [ ] Usuario administrador creado
- [ ] SSL configurado (si tienes dominio)
- [ ] Cron configurado para tareas programadas
- [ ] Firewall configurado
- [ ] Backups configurados
- [ ] Logs funcionando correctamente

---

¡Listo! Tu aplicación MAFIT debería estar funcionando en tu VPS. 🎉

