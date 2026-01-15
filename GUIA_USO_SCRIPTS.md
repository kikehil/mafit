# Guía de Uso de Scripts de Despliegue

Esta guía te ayudará a usar los scripts automatizados para subir tu proyecto al VPS.

## 📋 Scripts Disponibles

1. **`subir_a_vps.ps1`** - Script PowerShell para Windows (preparar y subir archivos)
2. **`instalar_en_servidor.sh`** - Script Bash para el servidor (instalar dependencias y configurar)
3. **`.rsyncignore`** - Archivo de exclusión para rsync

## 🚀 Método 1: Usando el Script PowerShell (Recomendado para Windows)

### Paso 1: Preparar y crear ZIP

Abre PowerShell en la carpeta del proyecto y ejecuta:

```powershell
# Solo preparar el ZIP (sin subir)
.\subir_a_vps.ps1 -SoloPreparar

# O preparar y subir automáticamente
.\subir_a_vps.ps1 -VPS_Usuario "tu_usuario" -VPS_IP "192.168.1.100" -VPS_Ruta "/var/www/mafit"
```

### Paso 2: Subir el ZIP manualmente (si usaste -SoloPreparar)

Puedes usar **WinSCP** o **SCP** desde PowerShell:

```powershell
# Con SCP (si tienes OpenSSH instalado)
scp ..\mafit_produccion.zip usuario@tu-vps-ip:/tmp/mafit_upload.zip
```

### Paso 3: En el servidor VPS

```bash
# Conectarte al servidor
ssh usuario@tu-vps-ip

# Descomprimir y mover archivos
cd /tmp
unzip -q mafit_upload.zip -d mafit_temp
sudo rm -rf /var/www/mafit
sudo mv mafit_temp/MAFIT /var/www/mafit
sudo chown -R www-data:www-data /var/www/mafit

# Ir al directorio del proyecto
cd /var/www/mafit

# Ejecutar script de instalación
bash instalar_en_servidor.sh
```

## 🔄 Método 2: Usando Rsync (Más eficiente para actualizaciones)

### Requisitos
- WSL (Windows Subsystem for Linux) instalado, O
- Git Bash, O
- Acceso a un sistema Linux/Mac

### Desde WSL o Linux:

```bash
# Navegar al proyecto (desde WSL)
cd /mnt/c/regz/MAFIT

# Sincronizar archivos al servidor
rsync -avz --progress \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='.git' \
  --exclude='public/build' \
  --exclude='.env' \
  --exclude='*.bat' \
  ./ usuario@tu-vps-ip:/var/www/mafit/
```

O usar el archivo `.rsyncignore`:

```bash
rsync -avz --progress --exclude-from='.rsyncignore' ./ usuario@tu-vps-ip:/var/www/mafit/
```

### En el servidor:

```bash
cd /var/www/mafit
bash instalar_en_servidor.sh
```

## 📦 Método 3: Usando Git (Recomendado para producción)

### Paso 1: Preparar repositorio

Asegúrate de que `.gitignore` esté configurado correctamente y sube tu código a Git:

```bash
git add .
git commit -m "Preparar para producción"
git push origin main
```

### Paso 2: En el servidor VPS

```bash
# Clonar repositorio
cd /var/www
sudo git clone https://tu-repositorio.git mafit
sudo chown -R www-data:www-data mafit
cd mafit

# Ejecutar script de instalación
bash instalar_en_servidor.sh
```

### Paso 3: Para actualizaciones futuras

```bash
cd /var/www/mafit
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Configuración del Script de Instalación

El script `instalar_en_servidor.sh` te pedirá:

1. **APP_NAME** - Nombre de la aplicación (por defecto: MAFIT)
2. **APP_URL** - URL de tu sitio (ej: https://tudominio.com)
3. **DB_DATABASE** - Nombre de la base de datos
4. **DB_USERNAME** - Usuario de MySQL
5. **DB_PASSWORD** - Contraseña de MySQL

Luego ejecutará automáticamente:
- ✅ Instalación de dependencias de Composer
- ✅ Instalación de dependencias de Node.js (si está disponible)
- ✅ Compilación de assets
- ✅ Configuración de .env
- ✅ Generación de clave de aplicación
- ✅ Creación de enlace simbólico de storage
- ✅ Ejecución de migraciones (opcional)
- ✅ Optimización para producción
- ✅ Configuración de permisos

## 📝 Ejemplo Completo de Despliegue

### Desde Windows (PowerShell):

```powershell
# 1. Preparar archivos
.\subir_a_vps.ps1 -SoloPreparar

# 2. Subir ZIP usando WinSCP o SCP
scp ..\mafit_produccion.zip usuario@vps-ip:/tmp/mafit_upload.zip
```

### En el servidor VPS:

```bash
# 1. Conectarse
ssh usuario@vps-ip

# 2. Descomprimir
cd /tmp
unzip -q mafit_upload.zip -d mafit_temp
sudo mv mafit_temp/MAFIT /var/www/mafit
sudo chown -R www-data:www-data /var/www/mafit

# 3. Instalar
cd /var/www/mafit
bash instalar_en_servidor.sh

# 4. Configurar Nginx (si aún no está configurado)
sudo nano /etc/nginx/sites-available/mafit
# (Ver configuración en DEPLOY_VPS.md)

sudo ln -s /etc/nginx/sites-available/mafit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## ⚠️ Solución de Problemas

### Error: "SCP no está disponible"
- Instala OpenSSH Client desde Configuración de Windows
- O usa WinSCP (interfaz gráfica)

### Error: "Rsync no está disponible"
- Instala WSL: `wsl --install`
- O usa el método ZIP en su lugar

### Error: "Permiso denegado" en el servidor
- Usa `sudo` antes de los comandos que requieren permisos
- Verifica que el usuario tenga permisos: `sudo chown -R www-data:www-data /var/www/mafit`

### Error: "Composer no encontrado"
- El script intentará instalarlo automáticamente
- O instálalo manualmente: https://getcomposer.org/download/

### Error: "Node.js no encontrado"
- El script te preguntará si quieres instalarlo
- O instálalo manualmente: https://nodejs.org/

## 💡 Consejos

1. **Primera vez**: Usa el método ZIP para tener control total
2. **Actualizaciones**: Usa Git o Rsync para cambios incrementales
3. **Backups**: Siempre haz backup antes de actualizar:
   ```bash
   mysqldump -u usuario -p mafit > backup_$(date +%Y%m%d).sql
   ```
4. **Pruebas**: Prueba en un entorno de staging antes de producción
5. **Logs**: Revisa los logs si hay problemas:
   ```bash
   tail -f /var/www/mafit/storage/logs/laravel.log
   ```

## 📚 Referencias

- Ver `DEPLOY_VPS.md` para configuración completa del servidor
- Ver `GUIA_DESPLIEGUE_COMPLETA.md` para guía detallada paso a paso
- Ver `ARCHIVOS_NO_SUBIR_VPS.md` para lista completa de exclusiones

