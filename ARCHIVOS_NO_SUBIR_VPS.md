# Archivos que NO debes subir a tu VPS

## ❌ Archivos y Carpetas que DEBES EXCLUIR

### 1. Dependencias (se instalan en el servidor)
- **`/node_modules/`** - Dependencias de Node.js (se instalan con `npm install`)
- **`/vendor/`** - Dependencias de PHP/Composer (se instalan con `composer install`)
- **`/public/build/`** - Archivos compilados (se generan con `npm run build`)

### 2. Archivos de configuración local/desarrollo
- **`.env`** - Configuración local (crear uno nuevo en el servidor)
- **`.env.backup`** - Backup del archivo .env local
- **`.env.production`** - Si existe, no subirlo (crear nuevo en servidor)
- **`DB_HOST`**, **`DB_PASSWORD`**, **`DB_USERNAME`** - Archivos de configuración local

### 3. Archivos de desarrollo Windows
- **`*.bat`** - Todos los archivos .bat (solo funcionan en Windows):
  - `agregar_alias_simple.bat`
  - `aumentar_limites_php.bat`
  - `aumentar_limites_upload.bat`
  - `composer_install.bat`
  - `configurar_apache_xampp.bat`
  - `configurar_xampp.bat`
  - `corregir_alias_MAFIT.bat`
  - `corregir_env.bat`
  - `corregir_limites_php.bat`
  - `descargar_composer.bat`
  - `diagnostico_completo.bat`
  - `habilitar_extensiones.bat`
  - `habilitar_gd.bat`
  - `habilitar_todas_extensiones.bat`
  - `instalar_dependencias.bat`
  - `instalar_todo.bat`
  - `reparar_composer.bat`
  - `reparar_composer_final.bat`
  - `SOLUCION_FINAL.bat`
  - `solucionar_404.bat`
  - `test_apache.bat`
  - `verificar_env.bat`
  - `verificar_rutas.bat`

### 4. Archivos de sistema y caché
- **`.git/`** - Historial de Git (opcional, pero no necesario en producción)
- **`.phpunit.result.cache`** - Cache de pruebas PHPUnit
- **`Homestead.json`** - Configuración de Homestead (entorno local)
- **`Homestead.yaml`** - Configuración de Homestead
- **`auth.json`** - Credenciales de Composer (si existe)
- **`npm-debug.log`** - Logs de npm
- **`yarn-error.log`** - Logs de yarn
- **`/public/hot`** - Archivo de desarrollo de Vite
- **`/public/storage`** - Se crea con `php artisan storage:link`

### 5. Archivos de IDE/Editor
- **`/.fleet/`** - Configuración de Fleet IDE
- **`/.idea/`** - Configuración de IntelliJ/PhpStorm
- **`/.vscode/`** - Configuración de VS Code

### 6. Archivos de Docker (si no usas Docker en producción)
- **`/docker/`** - Configuración de Docker
- **`docker-compose.yml`** - Si no usas Docker en producción
- **`Dockerfile`** - Si no usas Docker en producción

### 7. Archivos de backup y temporales
- **`mafit_backup.sql`** - Backup de base de datos (subirlo por separado si lo necesitas)
- **`query`** - Archivo temporal de consultas
- **`tmp_check_headers.php`** - Archivo temporal de pruebas

### 8. Archivos de documentación local (opcional)
- Puedes excluir los archivos `.md` si quieres reducir el tamaño, pero no es crítico:
  - `INSTALL_XAMPP.md`
  - `INSTALL.md`
  - `INSTALL_SIN_DOCKER.md`
  - `PASOS_INSTALACION.md`
  - `comandos_xampp.md`
  - `CONFIGURACION_CORREO.md`
  - `CONFIGURAR_APACHE.md`
  - `SOLUCION_404.md`
  - `SOLUCION_500.md`
  - `SOLUCION_VIRTUALIZACION.md`
  - `NOTA_PHP85.md`
  - `pasos_siguientes.md`
  - `CHANGELOG.md`

## ✅ Archivos que SÍ debes subir

### Estructura esencial del proyecto Laravel:
- **`/app/`** - Código de la aplicación
- **`/bootstrap/`** - Archivos de arranque
- **`/config/`** - Archivos de configuración
- **`/database/`** - Migraciones y seeders
- **`/public/`** - Archivos públicos (excepto `/public/build/` y `/public/hot/`)
- **`/resources/`** - Recursos (vistas, assets sin compilar)
- **`/routes/`** - Rutas de la aplicación
- **`/storage/`** - Carpeta de almacenamiento (estructura, sin archivos generados)
- **`artisan`** - CLI de Laravel
- **`composer.json`** - Dependencias de PHP
- **`composer.lock`** - Versiones exactas de dependencias
- **`package.json`** - Dependencias de Node.js
- **`package-lock.json`** - Versiones exactas de Node.js
- **`phpunit.xml`** - Configuración de pruebas
- **`tailwind.config.js`** - Configuración de Tailwind
- **`vite.config.js`** - Configuración de Vite
- **`postcss.config.js`** - Configuración de PostCSS
- **`.gitignore`** - Archivos a ignorar
- **`.env.example`** - Ejemplo de configuración (para crear `.env` en el servidor)
- **`README.md`** - Documentación principal

## 📋 Resumen: Qué hacer en el servidor

Una vez que subas los archivos necesarios al VPS, ejecuta estos comandos:

```bash
# 1. Instalar dependencias de PHP
composer install --optimize-autoloader --no-dev

# 2. Instalar dependencias de Node.js (si necesitas compilar assets)
npm install

# 3. Compilar assets para producción
npm run build

# 4. Crear archivo .env desde .env.example
cp .env.example .env

# 5. Configurar .env con tus datos del servidor
nano .env

# 6. Generar clave de aplicación
php artisan key:generate

# 7. Crear enlace simbólico de storage
php artisan storage:link

# 8. Ejecutar migraciones
php artisan migrate --force

# 9. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Configurar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 💡 Métodos recomendados para subir archivos

### Opción 1: Usando Git (Recomendado)
```bash
# En el servidor
cd /var/www
git clone https://tu-repositorio.git mafit
cd mafit
# Luego ejecutar los comandos de instalación arriba
```

### Opción 2: Comprimir excluyendo carpetas innecesarias
En Windows PowerShell:
```powershell
# Crear ZIP excluyendo carpetas grandes
Compress-Archive -Path * -DestinationPath ..\mafit.zip -Exclude node_modules,vendor,.git,*.bat
```

### Opción 3: Usar .gitignore como referencia
Los archivos listados en `.gitignore` generalmente NO deben subirse a producción.

## ⚠️ Importante

1. **NUNCA subas el archivo `.env`** con credenciales de producción
2. **Siempre crea un `.env` nuevo** en el servidor basado en `.env.example`
3. **Las dependencias se instalan en el servidor**, no se suben desde local
4. **Los assets se compilan en producción** con `npm run build`

