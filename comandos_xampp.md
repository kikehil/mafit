# Comandos con XAMPP - Guía Rápida

## Problema: "composer: command not found" o "php: command not found"

Esto ocurre porque PHP y Composer no están en el PATH del sistema.

## Solución 1: Usar Rutas Completas (Más Rápido)

En lugar de usar `composer` y `php` directamente, usa las rutas completas:

### En Git Bash o PowerShell:

```bash
# Instalar dependencias
C:/xampp/php/php.exe C:/xampp/php/composer.phar install

# O si tienes Composer instalado globalmente:
C:/Users/TU_USUARIO/AppData/Roaming/Composer/vendor/bin/composer install

# Generar clave
C:/xampp/php/php.exe artisan key:generate

# Migrar base de datos
C:/xampp/php/php.exe artisan migrate --seed
```

### En CMD (Símbolo del sistema):

```cmd
C:\xampp\php\php.exe C:\xampp\php\composer.phar install
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate --seed
```

## Solución 2: Agregar XAMPP al PATH Temporalmente

### En PowerShell:

```powershell
# Agregar PHP de XAMPP al PATH
$env:PATH = "C:\xampp\php;$env:PATH"

# Verificar
php -v
composer --version
```

### En Git Bash:

```bash
# Agregar PHP al PATH
export PATH="/c/xampp/php:$PATH"

# Verificar
php -v
```

### En CMD:

```cmd
set PATH=C:\xampp\php;%PATH%
php -v
```

## Solución 3: Usar el Script de Configuración

Ejecuta el archivo `configurar_xampp.bat` que crea una terminal con el PATH configurado:

```cmd
configurar_xampp.bat
```

Luego podrás usar `php` y `composer` normalmente en esa terminal.

## Solución 4: Agregar XAMPP al PATH Permanentemente

### Windows 10/11:

1. Presiona `Win + X` → "Sistema"
2. Haz clic en "Configuración avanzada del sistema"
3. Haz clic en "Variables de entorno"
4. En "Variables del sistema", selecciona "Path" → "Editar"
5. Haz clic en "Nuevo" y agrega:
   - `C:\xampp\php`
6. Si tienes Composer instalado, también agrega:
   - `C:\Users\TU_USUARIO\AppData\Roaming\Composer\vendor\bin`
7. Haz clic en "Aceptar" en todas las ventanas
8. **Cierra y vuelve a abrir** todas las terminales

## Instalar Composer en XAMPP

Si no tienes Composer instalado:

### Opción A: Descargar composer.phar

1. Descarga: https://getcomposer.org/download/
2. Descarga `composer.phar`
3. Colócalo en: `C:\xampp\php\composer.phar`
4. Úsalo así:
   ```bash
   C:/xampp/php/php.exe C:/xampp/php/composer.phar install
   ```

### Opción B: Instalar Composer para Windows

1. Descarga: https://getcomposer.org/Composer-Setup.exe
2. Instala Composer
3. Selecciona "Use existing PHP" y apunta a: `C:\xampp\php\php.exe`

## Comandos Completos para Instalación

### Si usas rutas completas:

```bash
# 1. Instalar dependencias
C:/xampp/php/php.exe C:/xampp/php/composer.phar install

# 2. Generar clave
C:/xampp/php/php.exe artisan key:generate

# 3. Migrar base de datos
C:/xampp/php/php.exe artisan migrate --seed

# 4. Instalar Node.js (si no está instalado)
# Descarga desde: https://nodejs.org/

# 5. Compilar assets
npm install
npm run build
```

### Si agregaste al PATH:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Verificar Instalación

```bash
# Verificar PHP
C:/xampp/php/php.exe -v

# Verificar Composer
C:/xampp/php/php.exe C:/xampp/php/composer.phar --version

# Verificar extensiones PHP necesarias
C:/xampp/php/php.exe -m | findstr "intl mysqli pdo_mysql mbstring"
```

## Nota Importante

Si cambias de terminal, necesitarás volver a configurar el PATH (a menos que lo agregues permanentemente).

La forma más fácil es usar las **rutas completas** o ejecutar `configurar_xampp.bat`.













