

@echo off
echo ========================================
echo Descargar composer.phar
echo ========================================
echo.

REM Verificar PHP
if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
    set COMPOSER_DIR=C:\xampp\php85
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
    set COMPOSER_DIR=C:\xampp\php
) else (
    echo ERROR: PHP no encontrado
    pause
    exit
)

echo PHP encontrado: %PHP%
echo Directorio: %COMPOSER_DIR%
echo.

if exist "%COMPOSER_DIR%\composer.phar" (
    echo composer.phar ya existe en: %COMPOSER_DIR%\composer.phar
    echo.
    choice /C SN /M "Deseas descargarlo de nuevo"
    if errorlevel 2 goto :skip
)

echo Descargando composer.phar...
echo.

%PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
%PHP% composer-setup.php --install-dir=%COMPOSER_DIR% --filename=composer.phar
del composer-setup.php

if exist "%COMPOSER_DIR%\composer.phar" (
    echo.
    echo ========================================
    echo composer.phar descargado correctamente!
    echo ========================================
    echo.
    echo Ubicacion: %COMPOSER_DIR%\composer.phar
    echo.
    echo Ahora puedes ejecutar:
    echo   %PHP% %COMPOSER_DIR%\composer.phar install
    echo.
) else (
    echo.
    echo ERROR: No se pudo descargar composer.phar
    echo.
    echo Descarga manualmente:
    echo 1. Ve a: https://getcomposer.org/download/
    echo 2. Descarga composer.phar
    echo 3. Colocalo en: %COMPOSER_DIR%\composer.phar
    echo.
)

:skip
pause






