# Pasos de Instalación - Orden Correcto

## ⚠️ IMPORTANTE: Ejecuta los comandos en este orden

### Paso 1: Instalar Composer (si no lo tienes)

**Opción A: Descargar composer.phar (Más rápido)**

En PowerShell:
```powershell
# Descargar composer.phar
Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile "C:\xampp\php\composer.phar"
```

O descarga manualmente desde: https://getcomposer.org/download/
- Descarga `composer.phar`
- Colócalo en: `C:\xampp\php\composer.phar`

**Opción B: Instalar Composer para Windows**
- Descarga: https://getcomposer.org/Composer-Setup.exe
- Instala y selecciona PHP de XAMPP: `C:\xampp\php\php.exe`

### Paso 2: Instalar Dependencias PHP

**Si tienes composer.phar en XAMPP:**
```bash
# En Git Bash
C:/xampp/php/php.exe C:/xampp/php/composer.phar install
```

**Si tienes Composer instalado globalmente:**
```bash
composer install
```

**O ejecuta el script:**
```cmd
composer_install.bat
```

### Paso 3: Generar Clave de Aplicación

```bash
C:/xampp/php/php.exe artisan key:generate
```

### Paso 4: Configurar Base de Datos

1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Crea base de datos: `mafit`
3. Verifica `.env`:
   ```env
   DB_HOST=127.0.0.1
   DB_DATABASE=mafit
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Paso 5: Ejecutar Migraciones

```bash
C:/xampp/php/php.exe artisan migrate --seed
```

### Paso 6: Instalar Dependencias Node.js

```bash
npm install
```

### Paso 7: Compilar Assets

```bash
npm run build
```

### Paso 8: Crear Usuario Administrador

```bash
C:/xampp/php/php.exe artisan tinker
```

Luego en Tinker:
```php
App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
exit
```

### Paso 9: Acceder a la Aplicación

- URL: http://localhost/MAFIT/public
- Login: admin@example.com / password

---

## Comandos Rápidos (Todo en uno)

Si ya tienes composer.phar:

```bash
# 1. Instalar dependencias
C:/xampp/php/php.exe C:/xampp/php/composer.phar install

# 2. Generar clave
C:/xampp/php/php.exe artisan key:generate

# 3. Migrar base de datos
C:/xampp/php/php.exe artisan migrate --seed

# 4. Instalar Node.js
npm install

# 5. Compilar
npm run build
```

---

## Solución de Problemas

### "composer: command not found"
- Usa la ruta completa: `C:/xampp/php/php.exe C:/xampp/php/composer.phar`
- O ejecuta: `composer_install.bat`

### "vendor/autoload.php: No such file or directory"
- Ejecuta primero: `composer install`
- Verifica que la carpeta `vendor` se haya creado

### "Class 'Normalizer' not found"
- Habilita extensión `intl` en `C:\xampp\php\php.ini`
- Busca `;extension=intl` y quita el `;`
- Reinicia Apache

### Error de conexión a MySQL
- Verifica que MySQL esté corriendo en XAMPP
- Verifica usuario/contraseña en `.env`
- Verifica que la base de datos `mafit` exista

