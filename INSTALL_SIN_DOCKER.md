# Instalación sin Docker - Windows

Esta guía te ayudará a instalar el proyecto MAFIT sin usar Docker.

## Opción 1: Usar Laragon (Recomendado - Más Fácil)

Laragon es un entorno de desarrollo todo-en-uno para Windows que incluye PHP, MySQL, Nginx/Apache.

### 1. Instalar Laragon

1. Descarga Laragon desde: https://laragon.org/download/
2. Instala Laragon (versión Full recomendada)
3. Inicia Laragon

### 2. Configurar Laragon

1. **Crear el proyecto:**
   - En Laragon, haz clic en "Menu" → "Quick add" → "Laravel"
   - O copia la carpeta `C:\WEB\MAFIT` a `C:\laragon\www\MAFIT`

2. **Configurar PHP:**
   - Laragon incluye PHP 8.3, verifica en "Menu" → "PHP" → "Version" que esté seleccionado PHP 8.3

3. **Iniciar servicios:**
   - Haz clic en "Start All" en Laragon
   - Verifica que MySQL y Nginx/Apache estén corriendo

### 3. Configurar Base de Datos

1. **Crear base de datos:**
   - Abre phpMyAdmin desde Laragon (Menu → Database → phpMyAdmin)
   - O ejecuta en MySQL:
   ```sql
   CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'mafit'@'localhost' IDENTIFIED BY 'root';
   GRANT ALL PRIVILEGES ON mafit.* TO 'mafit'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Configurar .env:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mafit
   DB_USERNAME=mafit
   DB_PASSWORD=root
   ```

### 4. Instalar Dependencias

Abre terminal en la carpeta del proyecto:

```powershell
# Instalar dependencias PHP
composer install

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --seed

# Instalar dependencias Node.js
npm install

# Compilar assets
npm run build
```

### 5. Acceder a la Aplicación

- Abre: http://mafit.test (si usas Laragon)
- O: http://localhost/MAFIT/public

---

## Opción 2: Instalación Manual (PHP, MySQL, Nginx)

### Requisitos Previos

1. **PHP 8.3:**
   - Descarga desde: https://windows.php.net/download/
   - Extrae a `C:\php`
   - Agrega `C:\php` al PATH del sistema

2. **Composer:**
   - Descarga desde: https://getcomposer.org/download/
   - Instala Composer globalmente

3. **MySQL 8.0:**
   - Descarga desde: https://dev.mysql.com/downloads/installer/
   - Instala MySQL Server

4. **Nginx:**
   - Descarga desde: https://nginx.org/en/download.html
   - Extrae a `C:\nginx`

5. **Node.js:**
   - Descarga desde: https://nodejs.org/
   - Instala Node.js y npm

### Configuración

#### 1. Configurar PHP

Edita `C:\php\php.ini` y habilita:
```ini
extension=mysqli
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo
extension=intl
extension=zip
extension=gd
```

#### 2. Configurar Nginx

Crea `C:\nginx\conf\mafit.conf`:

```nginx
server {
    listen 8080;
    server_name localhost;
    root C:/WEB/MAFIT/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Edita `C:\nginx\conf\nginx.conf` y agrega al final:
```nginx
include mafit.conf;
```

#### 3. Iniciar Servicios

**PHP-FPM:**
```powershell
cd C:\php
php-cgi.exe -b 127.0.0.1:9000
```

**Nginx:**
```powershell
cd C:\nginx
start nginx.exe
```

**MySQL:**
- Debe iniciarse automáticamente como servicio de Windows

#### 4. Crear Base de Datos

```sql
CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mafit'@'localhost' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON mafit.* TO 'mafit'@'localhost';
FLUSH PRIVILEGES;
```

#### 5. Configurar .env

```env
APP_NAME=MAFIT
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=mafit
DB_PASSWORD=root

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

#### 6. Instalar Proyecto

```powershell
cd C:\WEB\MAFIT

# Instalar dependencias
composer install

# Generar clave
php artisan key:generate

# Migrar base de datos
php artisan migrate --seed

# Instalar Node.js
npm install
npm run build
```

#### 7. Acceder

Abre: http://localhost:8080

---

## Opción 3: Usar XAMPP

### 1. Instalar XAMPP

1. Descarga desde: https://www.apachefriends.org/
2. Instala XAMPP (incluye PHP, MySQL, Apache)

### 2. Configurar

1. **Copiar proyecto:**
   - Copia `C:\WEB\MAFIT` a `C:\xampp\htdocs\MAFIT`

2. **Iniciar servicios:**
   - Abre XAMPP Control Panel
   - Inicia Apache y MySQL

3. **Crear base de datos:**
   - Abre phpMyAdmin: http://localhost/phpmyadmin
   - Crea base de datos `mafit`
   - Usuario: `root`, Contraseña: (vacía por defecto)

4. **Configurar .env:**
   ```env
   DB_HOST=127.0.0.1
   DB_DATABASE=mafit
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Instalar dependencias:**
   ```powershell
   cd C:\xampp\htdocs\MAFIT
   composer install
   php artisan key:generate
   php artisan migrate --seed
   npm install
   npm run build
   ```

6. **Acceder:**
   - http://localhost/MAFIT/public

---

## Crear Usuario Administrador

Después de cualquier instalación, crea un usuario admin:

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

---

## Solución de Problemas

### Error: "composer no se reconoce"
- Instala Composer: https://getcomposer.org/download/
- O usa: `php composer.phar install`

### Error: "php artisan no funciona"
- Verifica que PHP esté en el PATH
- O usa: `php C:\ruta\completa\artisan`

### Error: "Class 'Normalizer' not found"
- Habilita extensión `intl` en php.ini
- En XAMPP: edita `C:\xampp\php\php.ini` y descomenta `extension=intl`

### Error de conexión a MySQL
- Verifica que MySQL esté corriendo
- Verifica usuario y contraseña en `.env`
- Verifica que la base de datos exista

### Error: "npm no se reconoce"
- Instala Node.js: https://nodejs.org/
- Reinicia la terminal después de instalar

---

## Recomendación

**Para Windows, Laragon es la opción más fácil y rápida.** Incluye todo lo necesario y es muy simple de usar.









