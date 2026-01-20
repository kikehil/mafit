@echo off
chcp 65001 >nul
echo ========================================
echo CORREGIR LIMITES DE PHP AUTOMATICAMENTE
echo ========================================
echo.

set PHP_INI=C:\xampp\php85\php.ini

if not exist "%PHP_INI%" (
    echo ERROR: No se encontro %PHP_INI%
    pause
    exit /b 1
)

echo Haciendo backup de php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup.%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%%time:~6,2%" >nul
echo Backup creado.

echo.
echo Modificando limites en php.ini...
echo.

powershell -Command "(Get-Content '%PHP_INI%') -replace '^;?post_max_size\s*=.*', 'post_max_size = 100M' -replace '^;?upload_max_filesize\s*=.*', 'upload_max_filesize = 100M' -replace '^;?memory_limit\s*=.*', 'memory_limit = 256M' | Set-Content '%PHP_INI%'"

echo.
echo ========================================
echo LIMITES ACTUALIZADOS
echo ========================================
echo.
echo Valores configurados:
echo   post_max_size = 100M
echo   upload_max_filesize = 100M
echo   memory_limit = 256M
echo.
echo IMPORTANTE: Reinicia Apache en XAMPP Control Panel
echo para que los cambios tengan efecto.
echo.
pause

