@echo off
chcp 65001 >nul
echo ========================================
echo DIAGNOSTICO COMPLETO - MAFIT
echo ========================================
echo.

cd /d "%~dp0"

echo [1/6] Verificando PHP...
C:\xampp\php85\php.exe -v
if errorlevel 1 (
    echo ERROR: PHP no funciona
    pause
    exit /b 1
)
echo OK
echo.

echo [2/6] Verificando extensiones PHP...
C:\xampp\php85\php.exe -m | findstr /C:"pdo_mysql" >nul
if errorlevel 1 (
    echo ERROR: Extension pdo_mysql no encontrada
) else (
    echo OK - pdo_mysql
)

C:\xampp\php85\php.exe -m | findstr /C:"mbstring" >nul
if errorlevel 1 (
    echo ERROR: Extension mbstring no encontrada
) else (
    echo OK - mbstring
)

C:\xampp\php85\php.exe -m | findstr /C:"intl" >nul
if errorlevel 1 (
    echo ERROR: Extension intl no encontrada
) else (
    echo OK - intl
)
echo.

echo [3/6] Verificando conexion a base de datos...
C:\xampp\php85\php.exe artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK - Conexion a BD exitosa'; } catch (Exception $e) { echo 'ERROR: ' . $e->getMessage(); }"
echo.

echo [4/6] Verificando archivo .env...
if exist .env (
    echo OK - .env existe
    findstr /C:"DB_HOST=127.0.0.1" .env >nul
    if errorlevel 1 (
        echo ADVERTENCIA: DB_HOST no es 127.0.0.1
    ) else (
        echo OK - DB_HOST configurado correctamente
    )
) else (
    echo ERROR: .env no existe
)
echo.

echo [5/6] Verificando permisos de carpetas...
if exist "storage\logs" (
    echo OK - storage\logs existe
) else (
    echo ERROR: storage\logs no existe
)

if exist "bootstrap\cache" (
    echo OK - bootstrap\cache existe
) else (
    echo ERROR: bootstrap\cache no existe
)
echo.

echo [6/6] Limpiando cache...
C:\xampp\php85\php.exe artisan config:clear >nul 2>&1
C:\xampp\php85\php.exe artisan cache:clear >nul 2>&1
C:\xampp\php85\php.exe artisan route:clear >nul 2>&1
C:\xampp\php85\php.exe artisan view:clear >nul 2>&1
echo OK - Cache limpiado
echo.

echo ========================================
echo DIAGNOSTICO COMPLETADO
echo ========================================
echo.
echo Si todo esta OK, prueba acceder a:
echo http://localhost/MAFIT/public/login
echo.
echo Si el error persiste:
echo 1. Verifica que Apache este corriendo en XAMPP
echo 2. Verifica que MySQL este corriendo en XAMPP
echo 3. Revisa storage\logs\laravel.log para mas detalles
echo.
pause






