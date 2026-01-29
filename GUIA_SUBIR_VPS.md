# 🚀 Guía para Subir/Actualizar el Proyecto en el VPS

## 📋 Métodos Disponibles

### Método 1: Usando PowerShell Script (Más Fácil) ⭐

#### Paso 1: Configurar el script
Edita el archivo `subir-a-vps.ps1` y configura tus datos:

```powershell
$VPS_USER = "root"                    # Tu usuario SSH
$VPS_IP = "147.93.118.121"           # IP de tu VPS
$PROJECT_DIR = "C:\WEB\MAFIT"        # Ruta de tu proyecto
```

#### Paso 2: Ejecutar el script
Abre PowerShell en la carpeta del proyecto y ejecuta:

```powershell
.\subir-a-vps.ps1
```

Este script:
- ✅ Sube todo el proyecto al VPS
- ✅ Sube el script de despliegue
- ✅ Sube el backup de la base de datos (si existe)

#### Paso 3: En el VPS, ejecutar actualización
Conéctate al VPS y ejecuta:

```bash
ssh root@147.93.118.121  # Usa tus credenciales

# Ir al directorio del proyecto
cd /var/www/mafit

# Ejecutar script de actualización
bash actualizar-vps.sh
```

---

### Método 2: Usando WinSCP (Interfaz Gráfica) 🖱️

#### Paso 1: Descargar WinSCP
Descarga desde: https://winscp.net/

#### Paso 2: Conectarse al VPS
1. Abre WinSCP
2. Configura la conexión:
   - **Host name**: `147.93.118.121` (tu IP)
   - **User name**: `root` (tu usuario)
   - **Password**: (tu contraseña)
   - **Protocol**: SFTP
3. Conecta

#### Paso 3: Subir archivos
1. En el panel izquierdo: navega a `C:\WEB\MAFIT`
2. En el panel derecho: navega a `/var/www/mafit`
3. Selecciona todos los archivos y carpetas
4. Arrastra y suelta (o botón derecho → Upload)

**⚠️ IMPORTANTE**: Excluye estas carpetas al subir:
- `node_modules/`
- `vendor/` (si ya está instalado en el VPS)
- `.git/` (opcional)
- `storage/logs/*` (opcional, para no perder logs)

#### Paso 4: En el VPS, ejecutar actualización
Abre una terminal en WinSCP (botón "Terminal" en la barra superior) y ejecuta:

```bash
cd /var/www/mafit
bash actualizar-vps.sh
```

---

### Método 3: Usando SCP desde PowerShell 💻

#### Subir proyecto completo:
```powershell
scp -r C:\WEB\MAFIT root@147.93.118.121:/var/www/mafit
```

#### Subir solo archivos modificados (más rápido):
```powershell
# Subir solo el script de actualización primero
scp C:\WEB\MAFIT\actualizar-vps.sh root@147.93.118.121:/var/www/mafit/

# Luego subir archivos específicos que cambiaste
scp C:\WEB\MAFIT\app\Http\Middleware\ForceHttps.php root@147.93.118.121:/var/www/mafit/app/Http/Middleware/
scp C:\WEB\MAFIT\resources\views\layouts\app.blade.php root@147.93.118.121:/var/www/mafit/resources/views/layouts/
```

#### En el VPS:
```bash
cd /var/www/mafit
bash actualizar-vps.sh
```

---

### Método 4: Usando Git (Recomendado para actualizaciones frecuentes) 🔄

#### En tu máquina local:
```powershell
# Si usas Git, hacer commit y push
git add .
git commit -m "Actualización: Fix HTTPS mixed content"
git push origin main
```

#### En el VPS:
```bash
cd /var/www/mafit
git pull origin main
bash actualizar-vps.sh
```

---

## 🔄 Proceso de Actualización Completo

### Después de subir archivos, SIEMPRE ejecuta:

```bash
# 1. Conectarse al VPS
ssh root@147.93.118.121

# 2. Ir al directorio del proyecto
cd /var/www/mafit

# 3. Instalar dependencias de Composer
composer install --optimize-autoloader --no-dev

# 4. Compilar assets (IMPORTANTE para cambios de frontend/HTTPS)
npm install
npm run build

# 5. Ejecutar migraciones (si hay nuevas)
php artisan migrate --force

# 6. Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Configurar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 9. Reiniciar servicios (opcional pero recomendado)
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

**O simplemente ejecuta el script automatizado:**
```bash
bash actualizar-vps.sh
```

---

## 📝 Checklist de Actualización

- [ ] Archivos subidos al VPS
- [ ] Dependencias de Composer instaladas
- [ ] Assets compilados (`npm run build`)
- [ ] Migraciones ejecutadas (si hay nuevas)
- [ ] Cachés limpiadas y regeneradas
- [ ] Permisos configurados
- [ ] Servicios reiniciados
- [ ] Verificar que la aplicación funciona correctamente

---

## ⚡ Actualización Rápida (Solo Archivos Modificados)

Si solo cambiaste algunos archivos específicos:

```bash
# 1. Subir solo los archivos modificados con SCP
scp C:\WEB\MAFIT\ruta\al\archivo.php root@147.93.118.121:/var/www/mafit/ruta/al/

# 2. En el VPS, ejecutar solo lo necesario
cd /var/www/mafit

# Si cambiaste código PHP:
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Si cambiaste assets (CSS/JS):
npm run build

# Si cambiaste configuración:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🐛 Solución de Problemas

### Error: "Permission denied"
```bash
sudo chown -R www-data:www-data /var/www/mafit
sudo chmod -R 755 /var/www/mafit
sudo chmod -R 775 /var/www/mafit/storage /var/www/mafit/bootstrap/cache
```

### Error: "Composer not found"
```bash
# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Error: "npm not found"
```bash
# Instalar Node.js y npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### La aplicación no se actualiza
```bash
# Limpiar TODAS las cachés
cd /var/www/mafit
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Regenerar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
```

---

## 💡 Consejos

1. **Primera vez**: Usa el método completo (subir todo el proyecto)
2. **Actualizaciones pequeñas**: Usa SCP para subir solo archivos modificados
3. **Actualizaciones frecuentes**: Configura Git en el VPS
4. **Siempre haz backup** antes de actualizar:
   ```bash
   mysqldump -u mafit_user -p mafit > backup_$(date +%Y%m%d).sql
   ```

---

## 📞 Comandos Útiles

### Ver logs en tiempo real
```bash
tail -f /var/www/mafit/storage/logs/laravel.log
```

### Verificar estado de servicios
```bash
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
```

### Verificar configuración
```bash
php artisan config:show
php artisan route:list
```

---

¡Listo! Con estos métodos puedes subir y actualizar tu proyecto fácilmente. 🎉




