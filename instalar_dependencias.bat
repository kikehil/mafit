@echo off
echo ========================================
echo Instalando dependencias de MAFIT
echo ========================================
echo.

cd /d "%~dp0"

REM Verificar PHP
if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
    set COMPOSER=C:\xampp\php85\composer.phar
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
    set COMPOSER=C:\xampp\php\composer.phar
) else (
    echo ERROR: PHP no encontrado
    pause
    exit
)

echo PHP: %PHP%
echo Composer: %COMPOSER%
echo.

if not exist "%COMPOSER%" (
    echo ERROR: composer.phar no encontrado en %COMPOSER%
    echo.
    echo Ejecuta primero: descargar_composer.bat
    pause
    exit
)

echo ========================================
echo Instalando dependencias...
echo ========================================
echo.
echo Esto puede tardar varios minutos...
echo.

%PHP% %COMPOSER% install

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo Dependencias instaladas correctamente!
    echo ========================================
    echo.
    echo Siguiente paso:
    echo   %PHP% artisan key:generate
    echo.
) else (
    echo.
    echo ERROR al instalar dependencias
    echo.
)

pause
