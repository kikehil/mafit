@echo off
echo ========================================
echo Habilitar extensiones PHP necesarias
echo ========================================
echo.

REM Buscar php.ini
if exist "C:\xampp\php85\php.ini" (
    set PHPINI=C:\xampp\php85\php.ini
    set PHP_DIR=C:\xampp\php85
) else if exist "C:\xampp\php\php.ini" (
    set PHPINI=C:\xampp\php\php.ini
    set PHP_DIR=C:\xampp\php
) else (
    echo ERROR: php.ini no encontrado
    pause
    exit
)

echo php.ini encontrado: %PHPINI%
echo.

REM Extensiones necesarias
set EXTENSIONS=fileinfo intl mysqli pdo_mysql mbstring openssl curl zip gd

echo Extensiones a verificar:
for %%e in (%EXTENSIONS%) do echo   - %%e
echo.

echo Abriendo php.ini en Notepad...
echo.
echo INSTRUCCIONES:
echo 1. Busca cada extension con Ctrl+F
echo 2. Si encuentras ;extension=%%e, quita el punto y coma
echo 3. Si no existe, agregala al final: extension=%%e
echo 4. Guarda y cierra Notepad
echo 5. Reinicia Apache en XAMPP Control Panel
echo.
echo Extensiones a buscar:
for %%e in (%EXTENSIONS%) do echo   ;extension=%%e
echo.

pause

notepad %PHPINI%

echo.
echo ========================================
echo IMPORTANTE: Reinicia Apache en XAMPP
echo ========================================
echo.
pause












