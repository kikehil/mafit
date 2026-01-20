@echo off
REM Script para preparar el proyecto MAFIT antes de subirlo al VPS
REM Este script crea un backup de la base de datos y prepara los archivos

echo ==========================================
echo   Preparacion de MAFIT para VPS
echo ==========================================
echo.

set PROJECT_DIR=%~dp0
set BACKUP_DIR=%PROJECT_DIR%backups
set DATE_STR=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE_STR=%DATE_STR: =0%

echo [1/4] Creando directorio de backups...
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
echo ✓ Directorio creado
echo.

echo [2/4] Exportando base de datos...
echo Por favor, ingresa la contraseña de MySQL (o presiona Enter si no tienes):
set /p MYSQL_PASS=

REM Intentar con XAMPP primero
if exist "C:\xampp\mysql\bin\mysqldump.exe" (
    echo Usando MySQL de XAMPP...
    if "%MYSQL_PASS%"=="" (
        C:\xampp\mysql\bin\mysqldump.exe -u root mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql"
    ) else (
        C:\xampp\mysql\bin\mysqldump.exe -u root -p%MYSQL_PASS% mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql"
    )
) else if exist "C:\wamp64\bin\mysql\mysql8.0.xx\bin\mysqldump.exe" (
    echo Usando MySQL de WAMP...
    for %%I in (C:\wamp64\bin\mysql\mysql*) do (
        if exist "%%I\bin\mysqldump.exe" (
            if "%MYSQL_PASS%"=="" (
                "%%I\bin\mysqldump.exe" -u root mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql"
            ) else (
                "%%I\bin\mysqldump.exe" -u root -p%MYSQL_PASS% mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql"
            )
            goto :db_exported
        )
    )
) else (
    echo Intentando con MySQL del sistema...
    if "%MYSQL_PASS%"=="" (
        mysqldump -u root mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql" 2>nul
    ) else (
        mysqldump -u root -p%MYSQL_PASS% mafit > "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql" 2>nul
    )
)

:db_exported
if exist "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql" (
    echo ✓ Base de datos exportada: mafit_backup_%DATE_STR%.sql
) else (
    echo ✗ Error al exportar la base de datos
    echo   Puedes exportarla manualmente con:
    echo   mysqldump -u root -p mafit ^> mafit_backup.sql
)
echo.

echo [3/4] Verificando archivos importantes...
if exist ".env.example" (
    echo ✓ .env.example encontrado
) else (
    echo ✗ .env.example no encontrado
)

if exist "composer.json" (
    echo ✓ composer.json encontrado
) else (
    echo ✗ composer.json no encontrado
)

if exist "package.json" (
    echo ✓ package.json encontrado
) else (
    echo ✗ package.json no encontrado
)

if exist "deploy-vps.sh" (
    echo ✓ deploy-vps.sh encontrado
) else (
    echo ✗ deploy-vps.sh no encontrado
)
echo.

echo [4/4] Informacion de despliegue
echo.
echo ==========================================
echo   Resumen
echo ==========================================
echo.
echo Proyecto: %PROJECT_DIR%
echo Backup BD: %BACKUP_DIR%\mafit_backup_%DATE_STR%.sql
echo.
echo ==========================================
echo   Proximos pasos
echo ==========================================
echo.
echo 1. Sube el proyecto al VPS:
echo    scp -r "%PROJECT_DIR%" usuario@tu-vps-ip:/var/www/mafit
echo.
echo 2. Sube el backup de la base de datos:
echo    scp "%BACKUP_DIR%\mafit_backup_%DATE_STR%.sql" usuario@tu-vps-ip:/tmp/mafit_backup.sql
echo.
echo 3. Sube el script de despliegue:
echo    scp "%PROJECT_DIR%deploy-vps.sh" usuario@tu-vps-ip:/tmp/deploy-vps.sh
echo.
echo 4. Conectate al VPS y ejecuta:
echo    ssh usuario@tu-vps-ip
echo    chmod +x /tmp/deploy-vps.sh
echo    sudo /tmp/deploy-vps.sh
echo.
echo 5. Importa la base de datos (despues del despliegue):
echo    mysql -u mafit_user -p mafit ^< /tmp/mafit_backup.sql
echo.
echo ==========================================
echo   Listo para desplegar!
echo ==========================================
echo.
pause





