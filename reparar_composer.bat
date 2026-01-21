@echo off
echo ========================================
echo Reparar/Re-descargar composer.phar
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

REM Eliminar composer.phar corrupto si existe
if exist "%COMPOSER_DIR%\composer.phar" (
    echo Eliminando composer.phar existente (puede estar corrupto)...
    del "%COMPOSER_DIR%\composer.phar"
)

echo.
echo Descargando composer.phar desde el sitio oficial...
echo.

REM Descargar usando el instalador oficial de Composer
%PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo descargar el instalador
    pause
    exit
)

echo Ejecutando instalador...
%PHP% composer-setup.php --install-dir=%COMPOSER_DIR% --filename=composer.phar

REM Limpiar
if exist "composer-setup.php" (
    del composer-setup.php
)

REM Verificar que se descargó correctamente
if exist "%COMPOSER_DIR%\composer.phar" (
    echo.
    echo ========================================
    echo composer.phar descargado correctamente!
    echo ========================================
    echo.
    echo Verificando integridad...
    %PHP% %COMPOSER_DIR%\composer.phar --version
    
    if %ERRORLEVEL% EQU 0 (
        echo.
        echo ========================================
        echo Composer funciona correctamente!
        echo ========================================
        echo.
        echo Ahora puedes ejecutar:
        echo   %PHP% %COMPOSER_DIR%\composer.phar install
        echo.
    ) else (
        echo.
        echo ADVERTENCIA: Composer puede estar corrupto
        echo Intenta descargarlo manualmente desde:
        echo   https://getcomposer.org/download/
        echo.
    )
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

pause






