@echo off
chcp 65001 >nul
echo ========================================
echo VERIFICAR CONFIGURACION DE RUTAS
echo ========================================
echo.

echo [1] Verificando rutas de Laravel...
cd /d C:\WEB\MAFIT
C:\xampp\php85\php.exe artisan route:list --name=maf.search
echo.

echo [2] Verificando .htaccess...
if exist "public\.htaccess" (
    echo OK - .htaccess existe
) else (
    echo ERROR - .htaccess no existe
)
echo.

echo [3] Verificando mod_rewrite en httpd.conf...
findstr /C:"LoadModule rewrite_module" C:\xampp\apache\conf\httpd.conf >nul
if not errorlevel 1 (
    echo OK - mod_rewrite encontrado en httpd.conf
) else (
    echo ADVERTENCIA - mod_rewrite no encontrado
)
echo.

echo [4] Verificando alias /MAFIT...
findstr /C:"Alias /MAFIT" C:\xampp\apache\conf\httpd.conf >nul
if not errorlevel 1 (
    echo OK - Alias /MAFIT encontrado
) else (
    echo ADVERTENCIA - Alias /MAFIT no encontrado
    echo Puedes ejecutar: corregir_alias_MAFIT.bat
)
echo.

echo ========================================
echo SOLUCION RAPIDA
echo ========================================
echo.
echo Si las rutas no funcionan, prueba:
echo.
echo 1. Acceder a: http://localhost:8000/maf/search
echo    (servidor de desarrollo de Laravel)
echo.
echo 2. O verificar que Apache tenga:
echo    - mod_rewrite habilitado
echo    - AllowOverride All en el directorio public
echo    - Alias /MAFIT configurado correctamente
echo.
pause










