@echo off
echo ========================================
echo Configuracion de XAMPP para MAFIT
echo ========================================
echo.

REM Verificar si XAMPP existe
if exist "C:\xampp\php\php.exe" (
    set XAMPP_PATH=C:\xampp
    goto :found
)

if exist "D:\xampp\php\php.exe" (
    set XAMPP_PATH=D:\xampp
    goto :found
)

echo ERROR: XAMPP no encontrado en las ubicaciones comunes
echo Por favor, edita este archivo y cambia XAMPP_PATH a tu ruta de instalacion
pause
exit

:found
echo XAMPP encontrado en: %XAMPP_PATH%
echo.

REM Agregar XAMPP al PATH temporalmente
set PATH=%XAMPP_PATH%\php;%PATH%

REM Verificar Composer
if exist "%LOCALAPPDATA%\Composer\vendor\bin\composer.bat" (
    set PATH=%LOCALAPPDATA%\Composer\vendor\bin;%PATH%
    echo Composer encontrado
) else if exist "%APPDATA%\Composer\vendor\bin\composer.bat" (
    set PATH=%APPDATA%\Composer\vendor\bin;%PATH%
    echo Composer encontrado
) else (
    echo ADVERTENCIA: Composer no encontrado
    echo Descarga composer.phar y colocalo en esta carpeta
)

echo.
echo ========================================
echo Comandos disponibles:
echo ========================================
echo.
echo Para instalar dependencias:
echo   composer install
echo.
echo Para generar clave:
echo   php artisan key:generate
echo.
echo Para migrar base de datos:
echo   php artisan migrate --seed
echo.
echo ========================================
echo.

REM Abrir nueva terminal con PATH configurado
cmd /k













