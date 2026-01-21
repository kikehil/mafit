@echo off
echo ========================================
echo Corregir .env para XAMPP
echo ========================================
echo.

cd /d "%~dp0"

echo Modificando .env...
echo.

REM Crear backup
if exist ".env" (
    copy .env .env.backup >nul
    echo Backup creado: .env.backup
    echo.
)

REM Usar PowerShell para modificar el archivo
powershell -Command "$content = Get-Content .env -Raw; $content = $content -replace 'DB_HOST=mysql', 'DB_HOST=127.0.0.1'; $content = $content -replace 'DB_USERNAME=mafit', 'DB_USERNAME=root'; $content = $content -replace 'DB_PASSWORD=root', 'DB_PASSWORD='; Set-Content .env -Value $content -NoNewline"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo .env actualizado correctamente
    echo ========================================
    echo.
    echo Cambios realizados:
    echo   DB_HOST=127.0.0.1 (antes: mysql)
    echo   DB_USERNAME=root (antes: mafit)
    echo   DB_PASSWORD= (vacio, antes: root)
    echo.
) else (
    echo.
    echo ERROR al modificar .env
    echo.
    echo Edita manualmente .env y cambia:
    echo   DB_HOST=mysql -> DB_HOST=127.0.0.1
    echo   DB_USERNAME=mafit -> DB_USERNAME=root
    echo   DB_PASSWORD=root -> DB_PASSWORD=
    echo.
)

pause






