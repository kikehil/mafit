@echo off
echo ========================================
echo Habilitar extension GD en PHP
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
echo INSTRUCCIONES:
echo 1. Presiona Ctrl+F para buscar
echo 2. Busca: ;extension=gd
echo 3. Quita el punto y coma para que quede: extension=gd
echo 4. Guarda el archivo (Ctrl+S)
echo 5. Cierra Notepad
echo 6. Reinicia Apache en XAMPP Control Panel
echo.
pause

notepad %PHPINI%

echo.
echo ========================================
echo IMPORTANTE: Reinicia Apache en XAMPP
echo ========================================
echo.
pause






