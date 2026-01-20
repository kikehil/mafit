@echo off
chcp 65001 >nul
echo ========================================
echo CONFIGURAR APACHE PARA MAFIT
echo ========================================
echo.
echo Este script configurara Apache para que funcione
echo con el proyecto en C:\WEB\MAFIT
echo.
echo IMPORTANTE: Necesitas ejecutar esto como Administrador
echo.
pause

set HTTPD_CONF=C:\xampp\apache\conf\httpd.conf
set VHOSTS_CONF=C:\xampp\apache\conf\extra\httpd-vhosts.conf
set HOSTS_FILE=C:\Windows\System32\drivers\etc\hosts

echo.
echo [1/4] Verificando archivos de configuracion...
if not exist "%HTTPD_CONF%" (
    echo ERROR: No se encontro %HTTPD_CONF%
    pause
    exit /b 1
)
echo OK - httpd.conf encontrado

if not exist "%VHOSTS_CONF%" (
    echo ERROR: No se encontro %VHOSTS_CONF%
    pause
    exit /b 1
)
echo OK - httpd-vhosts.conf encontrado
echo.

echo [2/4] Verificando que mod_rewrite este habilitado...
findstr /C:"LoadModule rewrite_module" "%HTTPD_CONF%" >nul
if errorlevel 1 (
    echo ADVERTENCIA: mod_rewrite no esta habilitado
    echo Necesitas agregar esta linea en httpd.conf:
    echo LoadModule rewrite_module modules/mod_rewrite.so
) else (
    echo OK - mod_rewrite esta habilitado
)
echo.

echo [3/4] Verificando que httpd-vhosts.conf este incluido...
findstr /C:"httpd-vhosts.conf" "%HTTPD_CONF%" >nul
if errorlevel 1 (
    echo ADVERTENCIA: httpd-vhosts.conf no esta incluido
    echo Necesitas descomentar esta linea en httpd.conf:
    echo #Include conf/extra/httpd-vhosts.conf
) else (
    echo OK - httpd-vhosts.conf esta incluido
)
echo.

echo [4/4] Creando configuracion de VirtualHost...
echo.
echo Se agregara esta configuracion a %VHOSTS_CONF%:
echo.
echo ^<VirtualHost *:80^>
echo     ServerName mafit.local
echo     DocumentRoot "C:/WEB/MAFIT/public"
echo     
echo     ^<Directory "C:/WEB/MAFIT/public"^>
echo         AllowOverride All
echo         Require all granted
echo     ^</Directory^>
echo     
echo     ErrorLog "C:/xampp/apache/logs/mafit_error.log"
echo     CustomLog "C:/xampp/apache/logs/mafit_access.log" common
echo ^</VirtualHost^>
echo.
pause

echo Agregando VirtualHost a httpd-vhosts.conf...
echo. >> "%VHOSTS_CONF%"
echo # MAFIT Configuration >> "%VHOSTS_CONF%"
echo ^<VirtualHost *:80^> >> "%VHOSTS_CONF%"
echo     ServerName mafit.local >> "%VHOSTS_CONF%"
echo     DocumentRoot "C:/WEB/MAFIT/public" >> "%VHOSTS_CONF%"
echo. >> "%VHOSTS_CONF%"
echo     ^<Directory "C:/WEB/MAFIT/public"^> >> "%VHOSTS_CONF%"
echo         AllowOverride All >> "%VHOSTS_CONF%"
echo         Require all granted >> "%VHOSTS_CONF%"
echo     ^</Directory^> >> "%VHOSTS_CONF%"
echo. >> "%VHOSTS_CONF%"
echo     ErrorLog "C:/xampp/apache/logs/mafit_error.log" >> "%VHOSTS_CONF%"
echo     CustomLog "C:/xampp/apache/logs/mafit_access.log" common >> "%VHOSTS_CONF%"
echo ^</VirtualHost^> >> "%VHOSTS_CONF%"

echo OK - VirtualHost agregado
echo.

echo Ahora necesitas:
echo 1. Agregar "127.0.0.1    mafit.local" al archivo hosts
echo 2. Reiniciar Apache en XAMPP
echo.
echo El archivo hosts esta en: %HOSTS_FILE%
echo.
echo Despues de reiniciar Apache, accede a: http://mafit.local
echo.
pause












