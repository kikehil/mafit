@echo off
setlocal enabledelayedexpansion
title Reparar Composer.phar
color 0A

echo ========================================
echo Reparar composer.phar - Descarga Limpia
echo ========================================
echo.

cd /d "%~dp0"
if !ERRORLEVEL! NEQ 0 (
    echo ERROR: No se pudo cambiar al directorio del script
    echo.
    pause
    exit /b 1
)

echo Directorio actual: %CD%
echo.
echo Presiona cualquier tecla para continuar
pause >nul
echo.

REM Verificar PHP
echo [PASO 0] Verificando PHP
echo.

if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
    set COMPOSER_DIR=C:\xampp\php85
    echo    OK: PHP encontrado en C:\xampp\php85
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
    set COMPOSER_DIR=C:\xampp\php
    echo    OK: PHP encontrado en C:\xampp\php
) else (
    echo.
    echo ========================================
    echo ERROR: PHP no encontrado
    echo ========================================
    echo.
    echo Buscando en:
    echo   - C:\xampp\php85\php.exe
    echo   - C:\xampp\php\php.exe
    echo.
    echo Verifica que XAMPP este instalado correctamente
    echo.
    pause
    exit /b 1
)

echo    PHP: %PHP%
echo    Directorio Composer: %COMPOSER_DIR%
echo.
echo Presiona cualquier tecla para continuar con el paso 1
pause >nul
echo.

REM Eliminar composer.phar corrupto
if exist "%COMPOSER_DIR%\composer.phar" (
    echo [1/5] Eliminando composer.phar existente (puede estar corrupto)
    del "%COMPOSER_DIR%\composer.phar"
    if %ERRORLEVEL% EQU 0 (
        echo    OK: Archivo eliminado
    ) else (
        echo    ERROR: No se pudo eliminar el archivo
    )
    echo.
    echo Presiona cualquier tecla para continuar
    pause >nul
    echo.
) else (
    echo [1/5] No existe composer.phar previo, continuando
    echo.
    echo Presiona cualquier tecla para continuar
    pause >nul
    echo.
)

REM Eliminar archivos temporales
echo [2/5] Limpiando archivos temporales
if exist "composer-setup.php" (
    del composer-setup.php
    echo    OK: composer-setup.php eliminado
)
if exist "composer.phar" (
    del composer.phar
    echo    OK: composer.phar temporal eliminado
)
echo.
echo Presiona cualquier tecla para continuar con la descarga
pause >nul
echo.

echo [3/5] Descargando instalador desde getcomposer.org
echo    Esto puede tardar un momento
echo.

REM Descargar usando el instalador oficial
echo Ejecutando: php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
echo.
%PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
set DOWNLOAD_ERROR=!ERRORLEVEL!

echo.
echo Codigo de error de descarga: !DOWNLOAD_ERROR!
echo.

if not exist "composer-setup.php" (
    echo.
    echo ========================================
    echo ERROR: No se pudo descargar el instalador
    echo ========================================
    echo.
    echo Posibles causas:
    echo   - Sin conexion a internet
    echo   - Firewall bloqueando la descarga
    echo   - Problemas con SSL
    echo.
    echo Descarga manualmente desde:
    echo   https://getcomposer.org/download/
    echo.
    echo Guarda el archivo como: %COMPOSER_DIR%\composer.phar
    echo.
    pause
    exit /b 1
)

if exist "composer-setup.php" (
    echo    OK: Instalador descargado
) else (
    echo    ERROR: El archivo composer-setup.php no existe
)
echo.
echo Presiona cualquier tecla para continuar con la instalacion
pause >nul
echo.

echo [4/5] Ejecutando instalador de Composer
echo    Instalando en: %COMPOSER_DIR%
echo.
echo Ejecutando: php composer-setup.php --install-dir=%COMPOSER_DIR% --filename=composer.phar
echo.
%PHP% composer-setup.php --install-dir=%COMPOSER_DIR% --filename=composer.phar
set INSTALL_ERROR=!ERRORLEVEL!

echo.
echo Codigo de error de instalacion: !INSTALL_ERROR!
echo.
echo Presiona cualquier tecla para continuar
pause >nul
echo.

REM Limpiar
echo.
echo Limpiando archivos temporales
if exist "composer-setup.php" del composer-setup.php
echo    OK: Archivos temporales eliminados
echo.

timeout /t 1 >nul

REM Verificar
echo [5/5] Verificando instalacion
echo.
if exist "%COMPOSER_DIR%\composer.phar" (
    echo.
    echo ========================================
    echo Verificando composer.phar
    echo ========================================
    echo.
    
    echo Ejecutando: composer --version
    echo.
    %PHP% %COMPOSER_DIR%\composer.phar --version
    echo.
    
    if %ERRORLEVEL% EQU 0 (
        echo ========================================
        echo COMPOSER.PHAR REPARADO CORRECTAMENTE!
        echo ========================================
        echo.
        echo Ubicacion: %COMPOSER_DIR%\composer.phar
        echo.
        echo Siguiente paso:
        echo   Ejecuta: SOLUCION_FINAL.bat
        echo.
        color 0A
    ) else (
        echo.
        echo ========================================
        echo ADVERTENCIA: Composer puede tener problemas
        echo ========================================
        echo.
        echo Intenta descargarlo manualmente desde:
        echo   https://getcomposer.org/download/
        echo.
        color 0C
    )
) else (
    echo.
    echo ========================================
    echo ERROR: No se pudo descargar composer.phar
    echo ========================================
    echo.
    echo El archivo no existe en: %COMPOSER_DIR%\composer.phar
    echo.
    echo Descarga manualmente:
    echo   1. Ve a: https://getcomposer.org/download/
    echo   2. Haz clic en "Download" para descargar composer.phar
    echo   3. Guarda el archivo en: %COMPOSER_DIR%\composer.phar
    echo.
    color 0C
)

echo.
echo ========================================
echo FIN DEL SCRIPT
echo ========================================
echo.
echo Presiona cualquier tecla para cerrar esta ventana
pause
