# Instalación con XAMPP - Guía Paso a Paso

## Requisitos Previos

- ✅ XAMPP instalado
- ✅ Composer instalado (https://getcomposer.org/download/)
- ✅ Node.js instalado (https://nodejs.org/)

## Paso 1: Verificar XAMPP

1. Abre **XAMPP Control Panel**
2. Inicia **Apache** y **MySQL**
3. Verifica que ambos estén corriendo (deben aparecer en verde)

## Paso 2: Configurar el Proyecto

### Opción A: Mover proyecto a htdocs (Recomendado)

```powershell
# Copiar proyecto a htdocs de XAMPP
Copy-Item -Path "C:\WEB\MAFIT" -Destination "C:\xampp\htdocs\MAFIT" -Recurse
cd C:\xampp\htdocs\MAFIT
```

### Opción B: Usar proyecto en ubicación actual

Si prefieres mantener el proyecto en `C:\WEB\MAFIT`, necesitarás configurar un Virtual Host en Apache (ver más abajo).

## Paso 3: Crear Base de Datos

1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Crea una nueva base de datos:
   - Nombre: `mafit`
   - Intercalación: `utf8mb4_unicode_ci`
   - Haz clic en "Crear"

O ejecuta este SQL en phpMyAdmin:
```sql
CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Paso 4: Configurar .env

Edita el archivo `.env` en la raíz del proyecto:

```env
APP_NAME=MAFIT
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/MAFIT/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

**Nota:** Por defecto, XAMPP no tiene contraseña para el usuario `root`.

## Paso 5: Verificar PHP

1. Abre: http://localhost/dashboard/phpinfo.php
2. Verifica que estas extensiones estén habilitadas:
   - `mysqli`
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `curl`
   - `fileinfo`
   - `intl` ⚠️ **IMPORTANTE**
   - `zip`
   - `gd`

### Habilitar extensión intl (si no está habilitada)

**Nota:** Si tienes PHP 8.3 en `C:\xampp\php85`, usa esa ruta.

1. Abre: `C:\xampp\php85\php.ini` (o `C:\xampp\php\php.ini` si usas la versión estándar)
2. Busca la línea: `;extension=intl`
3. Quita el punto y coma: `extension=intl`
4. Guarda el archivo
5. Reinicia Apache en XAMPP Control Panel

## Paso 6: Instalar Dependencias

Abre PowerShell o CMD en la carpeta del proyecto:

```powershell
# Si moviste a htdocs
cd C:\xampp\htdocs\MAFIT

# O si está en la ubicación original
cd C:\WEB\MAFIT

# Instalar dependencias PHP
# Si PHP 8.3 está en php85, usa:
C:\xampp\php85\php.exe C:\xampp\php85\composer.phar install
# O si composer está en PATH:
composer install

# Generar clave de aplicación
C:\xampp\php85\php.exe artisan key:generate

# Ejecutar migraciones y seeders
C:\xampp\php85\php.exe artisan migrate --seed

# Instalar dependencias Node.js
npm install

# Compilar assets
npm run build
```

## Paso 7: Configurar Permisos (Windows)

En Windows, generalmente no hay problemas de permisos, pero asegúrate de que:
- La carpeta `storage` y `bootstrap/cache` sean escribibles
- Si hay problemas, haz clic derecho → Propiedades → Seguridad → Editar permisos

## Paso 8: Acceder a la Aplicación

### Si el proyecto está en htdocs:
- URL: http://localhost/MAFIT/public

### Si el proyecto está en otra ubicación:
Necesitas configurar un Virtual Host (ver abajo)

## Configurar Virtual Host (Opcional - Si proyecto NO está en htdocs)

Si prefieres usar `http://mafit.test` en lugar de `http://localhost/MAFIT/public`:

### 1. Editar hosts de Windows

1. Abre Notepad como Administrador
2. Abre el archivo: `C:\Windows\System32\drivers\etc\hosts`
3. Agrega esta línea al final:
   ```
   127.0.0.1    mafit.test
   ```
4. Guarda el archivo

### 2. Configurar Virtual Host en Apache

1. Abre: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Agrega al final:

```apache
<VirtualHost *:80>
    ServerName mafit.test
    DocumentRoot "C:/WEB/MAFIT/public"
    
    <Directory "C:/WEB/MAFIT/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Guarda el archivo
4. Reinicia Apache en XAMPP Control Panel

### 3. Acceder

- URL: http://mafit.test

## Paso 9: Crear Usuario Administrador

```powershell
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

Salir de Tinker: `exit`

## Solución de Problemas

### Error: "composer no se reconoce"
- Instala Composer: https://getcomposer.org/download/
- O descarga `composer.phar` y úsalo: `php composer.phar install`

### Error: "Class 'Normalizer' not found"
- Habilita extensión `intl` en `C:\xampp\php\php.ini`
- Reinicia Apache

### Error: "SQLSTATE[HY000] [1045] Access denied"
- Verifica usuario y contraseña en `.env`
- Por defecto XAMPP: usuario `root`, contraseña vacía

### Error: "The stream or file could not be opened"
- Verifica permisos de escritura en `storage` y `bootstrap/cache`
- En Windows, generalmente no es problema, pero verifica

### Error 404 al acceder
- Asegúrate de usar `/public` en la URL: http://localhost/MAFIT/public
- O configura Virtual Host

### Error: "npm no se reconoce"
- Instala Node.js: https://nodejs.org/
- Reinicia la terminal después de instalar

## Comandos Útiles

```powershell
# Ver logs de Laravel
Get-Content storage\logs\laravel.log -Tail 50

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

## Estructura de URLs

- **Aplicación:** http://localhost/MAFIT/public
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Dashboard XAMPP:** http://localhost/dashboard

---

¡Listo! Tu proyecto debería estar funcionando en XAMPP.




