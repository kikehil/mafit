@echo off
chcp 65001 >nul
echo ========================================
echo AUMENTAR LIMITES DE SUBIDA DE ARCHIVOS
echo ========================================
echo.
echo Este script aumentara los limites de PHP
echo para permitir subir archivos Excel grandes
echo.
echo IMPORTANTE: Necesitas ejecutar esto como Administrador
echo.
pause

set PHP_INI=C:\xampp\php85\php.ini

if not exist "%PHP_INI%" (
    echo ERROR: No se encontro %PHP_INI%
    echo Verifica que XAMPP este instalado en C:\xampp
    pause
    exit /b 1
)

echo.
echo Archivo php.ini encontrado: %PHP_INI%
echo.
echo Valores actuales:
findstr /C:"post_max_size" "%PHP_INI%"
findstr /C:"upload_max_filesize" "%PHP_INI%"
findstr /C:"max_execution_time" "%PHP_INI%"
findstr /C:"memory_limit" "%PHP_INI%"
echo.

echo Configurando nuevos valores...
echo.

REM Backup del archivo
copy "%PHP_INI%" "%PHP_INI%.backup" >nul
echo Backup creado: %PHP_INI%.backup
echo.

REM Reemplazar valores
powershell -Command "(Get-Content '%PHP_INI%') -replace '^;?post_max_size\s*=.*', 'post_max_size = 100M' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^;?upload_max_filesize\s*=.*', 'upload_max_filesize = 100M' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^;?max_execution_time\s*=.*', 'max_execution_time = 300' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^;?memory_limit\s*=.*', 'memory_limit = 512M' | Set-Content '%PHP_INI%'"

echo.
echo ========================================
echo CONFIGURACION COMPLETADA
echo ========================================
echo.
echo Valores configurados:
echo - post_max_size: 100M
echo - upload_max_filesize: 100M
echo - max_execution_time: 300 segundos
echo - memory_limit: 512M
echo.
echo IMPORTANTE: Debes reiniciar Apache en XAMPP
echo para que los cambios tengan efecto.
echo.
pause












