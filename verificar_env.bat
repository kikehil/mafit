@echo off
echo ========================================
echo Verificar configuracion .env
echo ========================================
echo.

cd /d "%~dp0"

echo Configuracion actual de base de datos:
echo.
findstr /C:"DB_HOST" .env
findstr /C:"DB_DATABASE" .env
findstr /C:"DB_USERNAME" .env
findstr /C:"DB_PASSWORD" .env
echo.

echo Si DB_HOST=mysql, necesitas cambiarlo a DB_HOST=127.0.0.1
echo.
pause






