@echo off
chcp 65001 >nul
echo ========================================
echo AGREGAR ALIAS SIMPLE PARA MAFIT
echo ========================================
echo.
echo Este script agregara un Alias a httpd.conf
echo para que puedas acceder a: http://localhost/mafit
echo.
echo IMPORTANTE: Necesitas ejecutar esto como Administrador
echo.
pause

set HTTPD_CONF=C:\xampp\apache\conf\httpd.conf

if not exist "%HTTPD_CONF%" (
    echo ERROR: No se encontro %HTTPD_CONF%
    pause
    exit /b 1
)

echo.
echo Verificando si el alias ya existe...
findstr /C:"Alias /mafit" "%HTTPD_CONF%" >nul
if not errorlevel 1 (
    echo El alias ya existe en httpd.conf
    echo No se necesita agregar nada.
    pause
    exit /b 0
)

echo Agregando alias al final de httpd.conf...
echo. >> "%HTTPD_CONF%"
echo # MAFIT Alias >> "%HTTPD_CONF%"
echo Alias /mafit "C:/WEB/MAFIT/public" >> "%HTTPD_CONF%"
echo. >> "%HTTPD_CONF%"
echo ^<Directory "C:/WEB/MAFIT/public"^> >> "%HTTPD_CONF%"
echo     Options Indexes FollowSymLinks >> "%HTTPD_CONF%"
echo     AllowOverride All >> "%HTTPD_CONF%"
echo     Require all granted >> "%HTTPD_CONF%"
echo ^</Directory^> >> "%HTTPD_CONF%"

echo.
echo ========================================
echo ALIAS AGREGADO EXITOSAMENTE
echo ========================================
echo.
echo Ahora necesitas:
echo 1. Reiniciar Apache en XAMPP Control Panel
echo 2. Acceder a: http://localhost/mafit
echo.
echo NOTA: No necesitas modificar el archivo hosts
echo.
pause






