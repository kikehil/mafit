@echo off
echo ========================================
echo Instalacion Completa de MAFIT
echo ========================================
echo.

REM Buscar PHP (prioridad a php85 que es PHP 8.3)
if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
    set PHP_DIR=C:\xampp\php85
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
    set PHP_DIR=C:\xampp\php
) else if exist "D:\xampp\php85\php.exe" (
    set PHP=D:\xampp\php85\php.exe
    set PHP_DIR=D:\xampp\php85
) else if exist "D:\xampp\php\php.exe" (
    set PHP=D:\xampp\php\php.exe
    set PHP_DIR=D:\xampp\php
) else (
    echo ERROR: PHP no encontrado. Verifica la instalacion de XAMPP
    pause
    exit
)

echo PHP encontrado: %PHP%
echo.

REM Buscar Composer
set COMPOSER_CMD=
if exist "%USERPROFILE%\AppData\Roaming\Composer\vendor\bin\composer.bat" (
    set COMPOSER_CMD=%USERPROFILE%\AppData\Roaming\Composer\vendor\bin\composer.bat
    goto :found_composer
)

if exist "%USERPROFILE%\AppData\Local\Programs\Composer\vendor\bin\composer.bat" (
    set COMPOSER_CMD=%USERPROFILE%\AppData\Local\Programs\Composer\vendor\bin\composer.bat
    goto :found_composer
)

if exist "C:\ProgramData\ComposerSetup\bin\composer.bat" (
    set COMPOSER_CMD=C:\ProgramData\ComposerSetup\bin\composer.bat
    goto :found_composer
)

if exist "C:\xampp\php85\composer.phar" (
    set COMPOSER_CMD=%PHP% C:\xampp\php85\composer.phar
    goto :found_composer
)

if exist "C:\xampp\php\composer.phar" (
    set COMPOSER_CMD=%PHP% C:\xampp\php\composer.phar
    goto :found_composer
)

echo ERROR: Composer no encontrado
echo.
echo Busca composer.bat en tu sistema y ejecuta manualmente:
echo   [ruta]\composer.bat install
echo.
pause
exit

:found_composer
echo Composer encontrado: %COMPOSER_CMD%
echo.

REM Configurar PATH para que Composer encuentre PHP
set PATH=%PHP_DIR%;%PATH%

cd /d "%~dp0"

echo ========================================
echo Paso 1: Instalando dependencias PHP...
echo ========================================
echo.

REM Si Composer es un .bat, necesitamos asegurar que PHP esté en PATH
REM O usar composer.phar directamente si existe
if exist "C:\xampp\php85\composer.phar" (
    %PHP% C:\xampp\php85\composer.phar install
) else if exist "C:\xampp\php\composer.phar" (
    %PHP% C:\xampp\php\composer.phar install
) else (
    REM Usar composer.bat pero con PHP en PATH
    %COMPOSER_CMD% install
)

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al instalar dependencias
    pause
    exit
)

echo.
echo ========================================
echo Paso 2: Generando clave de aplicacion...
echo ========================================
echo.
%PHP% artisan key:generate

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al generar clave
    pause
    exit
)

echo.
echo ========================================
echo Paso 3: Ejecutando migraciones...
echo ========================================
echo.
%PHP% artisan migrate --seed

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al ejecutar migraciones
    echo Verifica que la base de datos 'mafit' exista y este configurada en .env
    pause
    exit
)

echo.
echo ========================================
echo Instalacion PHP completada!
echo ========================================
echo.
echo Siguiente paso manual:
echo   1. Instalar Node.js si no lo tienes: https://nodejs.org/
echo   2. Ejecutar: npm install
echo   3. Ejecutar: npm run build
echo.
echo Luego accede a: http://localhost/MAFIT/public
echo.
pause

