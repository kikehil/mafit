@echo off
chcp 65001 >nul
echo ========================================
echo AUMENTAR LIMITES DE SUBIDA DE ARCHIVOS
echo ========================================
echo.
echo Este script te ayudara a aumentar los limites
echo para subir archivos Excel grandes.
echo.
echo IMPORTANTE: Necesitas ejecutar esto como Administrador
echo.
pause

set PHP_INI=C:\xampp\php85\php.ini

if not exist "%PHP_INI%" (
    echo ERROR: No se encontro %PHP_INI%
    echo Verifica que PHP 8.3 este instalado en C:\xampp\php85
    pause
    exit /b 1
)

echo.
echo Abriendo php.ini en el editor...
echo.
echo Busca estas lineas y cambialas a:
echo.
echo post_max_size = 100M
echo upload_max_filesize = 100M
echo max_file_uploads = 20
echo memory_limit = 256M
echo.
echo Luego guarda el archivo y reinicia Apache en XAMPP.
echo.
pause

notepad "%PHP_INI%"

echo.
echo ========================================
echo DESPUES DE EDITAR:
echo ========================================
echo 1. Guarda el archivo php.ini
echo 2. Reinicia Apache en XAMPP Control Panel
echo 3. Vuelve a intentar subir el archivo
echo.
pause

