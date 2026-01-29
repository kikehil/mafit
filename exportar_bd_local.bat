@echo off
REM Script para exportar la base de datos local de MAFIT
REM Este script exporta todas las tablas necesarias para el funcionamiento completo

echo ========================================
echo Exportando Base de Datos MAFIT Local
echo ========================================
echo.

REM Leer configuración de .env
for /f "tokens=2 delims==" %%a in ('findstr "DB_DATABASE" .env') do set DB_NAME=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_USERNAME" .env') do set DB_USER=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_PASSWORD" .env') do set DB_PASS=%%a

echo Base de datos: %DB_NAME%
echo Usuario: %DB_USER%
echo.

REM Crear directorio de exportación si no existe
if not exist "backups" mkdir backups

REM Generar nombre de archivo con fecha
set FECHA=%date:~-4,4%%date:~-7,2%%date:~-10,2%
set HORA=%time:~0,2%%time:~3,2%%time:~6,2%
set HORA=%HORA: =0%
set ARCHIVO=backups\mafit_export_%FECHA%_%HORA%.sql

echo Exportando a: %ARCHIVO%
echo.

REM Exportar base de datos completa
REM Nota: Ajusta la ruta de mysqldump según tu instalación de XAMPP
"C:\xampp\mysql\bin\mysqldump.exe" -u %DB_USER% -p%DB_PASS% %DB_NAME% > %ARCHIVO%

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo Exportacion completada exitosamente!
    echo ========================================
    echo Archivo: %ARCHIVO%
    echo.
    echo Siguiente paso: Subir este archivo al VPS e importarlo
) else (
    echo.
    echo ========================================
    echo ERROR en la exportacion
    echo ========================================
    echo Verifica:
    echo 1. Que MySQL este corriendo
    echo 2. Que las credenciales en .env sean correctas
    echo 3. Que la ruta de mysqldump sea correcta
)

pause






