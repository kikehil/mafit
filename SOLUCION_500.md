# Solución Error HTTP 500

## Problema
Error 500 al acceder a http://localhost/MAFIT/public

## Posibles Causas y Soluciones

### 1. MySQL no está corriendo

**Verificar:**
- Abre XAMPP Control Panel
- Verifica que MySQL esté en verde (corriendo)
- Si no está corriendo, haz clic en "Start"

### 2. Base de datos no existe

**Crear base de datos:**
1. Abre: http://localhost/phpmyadmin
2. Crea base de datos: `mafit`
3. Intercalación: `utf8mb4_unicode_ci`

O ejecuta en MySQL:
```sql
CREATE DATABASE mafit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configuración .env incorrecta

**Verificar .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=root
DB_PASSWORD=
```

**Limpiar cache:**
```cmd
C:\xampp\php85\php.exe artisan config:clear
C:\xampp\php85\php.exe artisan cache:clear
```

### 4. Permisos de carpetas

**Verificar permisos:**
- `storage` debe ser escribible
- `bootstrap/cache` debe ser escribible

En Windows generalmente no hay problemas, pero verifica.

### 5. Ver logs de error

**Ver el error específico:**
```cmd
Get-Content storage\logs\laravel.log -Tail 50
```

### 6. Verificar Apache

**Asegúrate de que:**
- Apache esté corriendo en XAMPP
- El DocumentRoot apunte correctamente
- Los módulos necesarios estén habilitados

## Comandos de Diagnóstico

```cmd
# Verificar conexión a BD
C:\xampp\php85\php.exe artisan tinker
# Luego: DB::connection()->getPdo();

# Limpiar todo el cache
C:\xampp\php85\php.exe artisan config:clear
C:\xampp\php85\php.exe artisan cache:clear
C:\xampp\php85\php.exe artisan route:clear
C:\xampp\php85\php.exe artisan view:clear

# Ver rutas
C:\xampp\php85\php.exe artisan route:list
```

## Si nada funciona

1. Verifica los logs: `storage\logs\laravel.log`
2. Verifica que Apache y MySQL estén corriendo
3. Verifica que la base de datos `mafit` exista
4. Verifica el archivo `.env`






