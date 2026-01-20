@echo off
chcp 65001 >nul
echo ========================================
echo SOLUCIONAR ERROR 404 - RUTAS LARAVEL
echo ========================================
echo.
echo Este script verificara y corregira la configuracion
echo de Apache para que las rutas de Laravel funcionen.
echo.
echo IMPORTANTE: Ejecuta esto como Administrador
echo.
pause

set HTTPD_CONF=C:\xampp\apache\conf\httpd.conf

echo.
echo [1/3] Verificando mod_rewrite...
findstr /C:"LoadModule rewrite_module" "%HTTPD_CONF%" >nul
if errorlevel 1 (
    echo ERROR: mod_rewrite no esta habilitado
    echo Necesitas descomentar esta linea en httpd.conf:
    echo LoadModule rewrite_module modules/mod_rewrite.so
    echo.
    echo Abriendo httpd.conf para que lo hagas manualmente...
    notepad "%HTTPD_CONF%"
    pause
) else (
    echo OK - mod_rewrite esta habilitado
)
echo.

echo [2/3] Verificando alias /MAFIT...
findstr /C:"Alias /MAFIT" "%HTTPD_CONF%" >nul
if errorlevel 1 (
    echo El alias /MAFIT no existe. Agregandolo...
    echo. >> "%HTTPD_CONF%"
    echo # MAFIT Alias >> "%HTTPD_CONF%"
    echo Alias /MAFIT "C:/WEB/MAFIT/public" >> "%HTTPD_CONF%"
    echo. >> "%HTTPD_CONF%"
    echo ^<Directory "C:/WEB/MAFIT/public"^> >> "%HTTPD_CONF%"
    echo     Options Indexes FollowSymLinks >> "%HTTPD_CONF%"
    echo     AllowOverride All >> "%HTTPD_CONF%"
    echo     Require all granted >> "%HTTPD_CONF%"
    echo ^</Directory^> >> "%HTTPD_CONF%"
    echo OK - Alias agregado
) else (
    echo OK - Alias /MAFIT ya existe
)
echo.

echo [3/3] Verificando .htaccess...
if exist "C:\WEB\MAFIT\public\.htaccess" (
    echo OK - .htaccess existe
) else (
    echo ERROR - .htaccess no existe
    echo Creando .htaccess...
    copy /Y "C:\WEB\MAFIT\public\.htaccess.example" "C:\WEB\MAFIT\public\.htaccess" >nul 2>&1
    if exist "C:\WEB\MAFIT\public\.htaccess" (
        echo OK - .htaccess creado
    ) else (
        echo ERROR - No se pudo crear .htaccess
    )
)
echo.

echo ========================================
echo CONFIGURACION COMPLETA
echo ========================================
echo.
echo Ahora necesitas:
echo 1. Reiniciar Apache en XAMPP Control Panel
echo 2. Acceder a: http://localhost/MAFIT/maf/search
echo.
echo NOTA: Si aun no funciona, prueba:
echo http://localhost:8000/maf/search
echo (servidor de desarrollo de Laravel)
echo.
pause










