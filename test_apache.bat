@echo off
echo ========================================
echo Test de configuracion Apache/PHP
echo ========================================
echo.

cd /d "%~dp0"

echo Verificando que Apache este corriendo...
echo.

echo Probando conexion a PHP...
C:\xampp\php85\php.exe -r "echo 'PHP funciona correctamente' . PHP_EOL; echo 'Version: ' . PHP_VERSION . PHP_EOL;"

echo.
echo Verificando extensiones necesarias...
C:\xampp\php85\php.exe -m | findstr /C:"pdo_mysql" /C:"mbstring" /C:"openssl" /C:"fileinfo" /C:"gd" /C:"zip" /C:"intl"

echo.
echo ========================================
echo Si ves las extensiones arriba, PHP esta bien configurado
echo ========================================
echo.
pause






