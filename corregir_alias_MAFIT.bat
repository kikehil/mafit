@echo off
chcp 65001 >nul
echo ========================================
echo CORREGIR ALIAS PARA /MAFIT (mayuscula)
echo ========================================
echo.
echo Este script agregara un alias adicional
echo para que puedas acceder a: http://localhost/MAFIT
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
echo Verificando si el alias /MAFIT ya existe...
findstr /C:"Alias /MAFIT" "%HTTPD_CONF%" >nul
if not errorlevel 1 (
    echo El alias /MAFIT ya existe en httpd.conf
    echo No se necesita agregar nada.
    pause
    exit /b 0
)

echo Agregando alias /MAFIT al final de httpd.conf...
echo. >> "%HTTPD_CONF%"
echo # MAFIT Alias (mayuscula) >> "%HTTPD_CONF%"
echo Alias /MAFIT "C:/WEB/MAFIT/public" >> "%HTTPD_CONF%"
echo. >> "%HTTPD_CONF%"
echo ^<Directory "C:/WEB/MAFIT/public"^> >> "%HTTPD_CONF%"
echo     Options Indexes FollowSymLinks >> "%HTTPD_CONF%"
echo     AllowOverride All >> "%HTTPD_CONF%"
echo     Require all granted >> "%HTTPD_CONF%"
echo ^</Directory^> >> "%HTTPD_CONF%"

echo.
echo ========================================
echo ALIAS /MAFIT AGREGADO EXITOSAMENTE
echo ========================================
echo.
echo Ahora necesitas:
echo 1. Reiniciar Apache en XAMPP Control Panel
echo 2. Acceder a: http://localhost/MAFIT
echo.
echo NOTA: Ahora puedes usar tanto /mafit como /MAFIT
echo.
pause






