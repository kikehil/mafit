@echo off
echo ========================================
echo Instalacion de dependencias con Composer
echo ========================================
echo.

REM Verificar PHP (prioridad a php85 que es PHP 8.3)
if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
) else if exist "D:\xampp\php85\php.exe" (
    set PHP=D:\xampp\php85\php.exe
) else if exist "D:\xampp\php\php.exe" (
    set PHP=D:\xampp\php\php.exe
) else (
    echo ERROR: PHP no encontrado. Verifica la instalacion de XAMPP
    pause
    exit
)

echo PHP encontrado: %PHP%
echo.

REM Verificar composer.phar
if exist "C:\xampp\php85\composer.phar" (
    set COMPOSER=%PHP% C:\xampp\php85\composer.phar
    echo Composer encontrado en XAMPP php85
    goto :install
)

if exist "C:\xampp\php\composer.phar" (
    set COMPOSER=%PHP% C:\xampp\php\composer.phar
    echo Composer encontrado en XAMPP
    goto :install
)

if exist "D:\xampp\php85\composer.phar" (
    set COMPOSER=%PHP% D:\xampp\php85\composer.phar
    echo Composer encontrado en XAMPP php85
    goto :install
)

if exist "D:\xampp\php\composer.phar" (
    set COMPOSER=%PHP% D:\xampp\php\composer.phar
    echo Composer encontrado en XAMPP
    goto :install
)

echo.
echo Composer no encontrado. Descargando...
echo.

REM Descargar composer.phar
if exist "C:\xampp\php85\php.exe" (
    %PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    %PHP% composer-setup.php --install-dir=C:\xampp\php85 --filename=composer.phar
    del composer-setup.php
    
    if exist "C:\xampp\php85\composer.phar" (
        set COMPOSER=%PHP% C:\xampp\php85\composer.phar
        echo Composer instalado correctamente
        goto :install
    )
) else if exist "C:\xampp\php\php.exe" (
    %PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    %PHP% composer-setup.php --install-dir=C:\xampp\php --filename=composer.phar
    del composer-setup.php
    
    if exist "C:\xampp\php\composer.phar" (
        set COMPOSER=%PHP% C:\xampp\php\composer.phar
        echo Composer instalado correctamente
        goto :install
    )
)

echo.
echo ERROR: No se pudo descargar Composer
echo.
echo Descarga manualmente:
echo 1. Ve a: https://getcomposer.org/download/
echo 2. Descarga composer.phar
echo 3. Colocalo en: C:\xampp\php\composer.phar
echo.
pause
exit

:install
echo.
echo ========================================
echo Instalando dependencias...
echo ========================================
echo.

cd /d "%~dp0"
%COMPOSER% install

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo Dependencias instaladas correctamente!
    echo ========================================
    echo.
    echo Siguiente paso:
    echo   %PHP% artisan key:generate
    echo   %PHP% artisan migrate --seed
    echo.
) else (
    echo.
    echo ERROR al instalar dependencias
    echo.
)

pause

