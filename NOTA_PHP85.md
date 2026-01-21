# Nota: PHP 8.3 en php85

## Configuración Actual

Tu instalación de XAMPP tiene PHP 8.3 en:
- **PHP:** `C:\xampp\php85\php.exe`
- **Composer:** Configurado para usar PHP 8.3

## Comandos Actualizados

Todos los scripts y guías han sido actualizados para buscar primero en `php85` y luego en `php`.

### Comandos Manuales

Si ejecutas comandos manualmente, usa estas rutas:

```bash
# Instalar dependencias
C:/xampp/php85/php.exe C:/xampp/php85/composer.phar install

# O si Composer está en PATH:
composer install

# Generar clave
C:/xampp/php85/php.exe artisan key:generate

# Migrar base de datos
C:/xampp/php85/php.exe artisan migrate --seed

# Otros comandos artisan
C:/xampp/php85/php.exe artisan [comando]
```

### En PowerShell

```powershell
# Agregar PHP 8.3 al PATH temporalmente
$env:PATH = "C:\xampp\php85;$env:PATH"

# Ahora puedes usar:
php artisan key:generate
php artisan migrate --seed
```

### En Git Bash

```bash
# Agregar PHP 8.3 al PATH temporalmente
export PATH="/c/xampp/php85:$PATH"

# Ahora puedes usar:
php artisan key:generate
php artisan migrate --seed
```

## Scripts Automáticos

Los siguientes scripts ya están actualizados para buscar en `php85` primero:

- ✅ `instalar_todo.bat` - Instalación completa automática
- ✅ `composer_install.bat` - Solo instalación de dependencias
- ✅ `configurar_xampp.bat` - Configuración de PATH

## Verificar PHP

```bash
C:/xampp/php85/php.exe -v
```

Debería mostrar: `PHP 8.3.x`

## Verificar Extensiones

```bash
C:/xampp/php85/php.exe -m
```

Asegúrate de que estas extensiones estén listadas:
- intl ⚠️ **IMPORTANTE**
- mysqli
- pdo_mysql
- mbstring
- openssl
- curl
- fileinfo
- zip
- gd

## Habilitar extensión intl

Si falta `intl`:

1. Abre: `C:\xampp\php85\php.ini`
2. Busca: `;extension=intl`
3. Cambia a: `extension=intl`
4. Guarda y reinicia Apache

---

**Todo está listo para usar PHP 8.3 desde `php85`!** 🚀






