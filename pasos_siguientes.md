# Pasos Siguientes - Instalación MAFIT

## ✅ Completado
- ✓ Composer.phar descargado en: `C:\xampp\php85\composer.phar`

## 📋 Pasos Restantes

### 1. Instalar Dependencias PHP

Ejecuta en Git Bash o PowerShell:

```bash
C:/xampp/php85/php.exe C:/xampp/php85/composer.phar install
```

**Nota:** Esto puede tardar varios minutos la primera vez.

### 2. Generar Clave de Aplicación

```bash
C:/xampp/php85/php.exe artisan key:generate
```

### 3. Verificar Base de Datos

Asegúrate de que:
- MySQL esté corriendo en XAMPP
- La base de datos `mafit` exista (crearla desde phpMyAdmin si no existe)
- El archivo `.env` tenga la configuración correcta:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar Migraciones

```bash
C:/xampp/php85/php.exe artisan migrate --seed
```

Esto creará las tablas y cargará los datos iniciales (plazas).

### 5. Instalar Dependencias Node.js

```bash
npm install
```

### 6. Compilar Assets

```bash
npm run build
```

### 7. Crear Usuario Administrador

```bash
C:/xampp/php85/php.exe artisan tinker
```

Luego en Tinker:

```php
App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

Salir: `exit`

### 8. Acceder a la Aplicación

- **URL:** http://localhost/MAFIT/public
- **Login:** admin@example.com / password

---

## 🚀 Comandos Rápidos (Todo en uno)

Si prefieres ejecutar todo de una vez:

```bash
# 1. Instalar dependencias PHP
C:/xampp/php85/php.exe C:/xampp/php85/composer.phar install

# 2. Generar clave
C:/xampp/php85/php.exe artisan key:generate

# 3. Migrar base de datos
C:/xampp/php85/php.exe artisan migrate --seed

# 4. Instalar Node.js
npm install

# 5. Compilar assets
npm run build
```

---

## ⚠️ Solución de Problemas

### Error: "Class 'Normalizer' not found"
- Habilita extensión `intl` en `C:\xampp\php85\php.ini`
- Busca `;extension=intl` y cambia a `extension=intl`
- Reinicia Apache

### Error: "SQLSTATE[HY000] [1045] Access denied"
- Verifica usuario y contraseña en `.env`
- Por defecto XAMPP: usuario `root`, contraseña vacía

### Error: "Base de datos no existe"
- Abre phpMyAdmin: http://localhost/phpmyadmin
- Crea base de datos: `mafit`
- Intercalación: `utf8mb4_unicode_ci`

---

¡Casi terminamos! 🎉






