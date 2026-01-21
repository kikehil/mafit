@echo off
echo ========================================
echo Habilitar TODAS las extensiones PHP necesarias
echo ========================================
echo.

if exist "C:\xampp\php85\php.ini" (
    set PHPINI=C:\xampp\php85\php.ini
) else if exist "C:\xampp\php\php.ini" (
    set PHPINI=C:\xampp\php\php.ini
) else (
    echo ERROR: php.ini no encontrado
    pause
    exit /b 1
)

echo php.ini encontrado: %PHPINI%
echo.
echo Abriendo php.ini en Notepad...
echo.
echo ========================================
echo EXTENSIONES A HABILITAR:
echo ========================================
echo.
echo Busca y habilita estas extensiones (quita el ; al inicio):
echo.
echo   extension=fileinfo
echo   extension=intl
echo   extension=mysqli
echo   extension=pdo_mysql
echo   extension=mbstring
echo   extension=openssl
echo   extension=curl
echo   extension=zip
echo   extension=gd
echo.
echo INSTRUCCIONES:
echo 1. Presiona Ctrl+F para buscar cada extension
echo 2. Busca: ;extension=[nombre]
echo 3. Quita el punto y coma: extension=[nombre]
echo 4. Repite para todas las extensiones
echo 5. Guarda el archivo (Ctrl+S)
echo 6. Cierra Notepad
echo 7. Reinicia Apache en XAMPP Control Panel
echo.
pause

notepad %PHPINI%

echo.
echo ========================================
echo IMPORTANTE: Reinicia Apache en XAMPP
echo ========================================
echo.
pause






