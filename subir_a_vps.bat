@echo off
REM Script simple para preparar archivos para subir al VPS
REM Uso: subir_a_vps.bat

echo ========================================
echo   Preparar Archivos para VPS - MAFIT
echo ========================================
echo.

REM Obtener la ruta del proyecto
set "PROYECTO_PATH=%~dp0"
set "ZIP_PATH=%PROYECTO_PATH%..\mafit_produccion.zip"

echo Proyecto: %PROYECTO_PATH%
echo Destino ZIP: %ZIP_PATH%
echo.

REM Eliminar ZIP anterior si existe
if exist "%ZIP_PATH%" (
    echo Eliminando ZIP anterior...
    del /F /Q "%ZIP_PATH%"
)

echo Preparando archivos para comprimir...
echo Esto puede tardar varios minutos...
echo.

REM Crear ZIP usando PowerShell (más eficiente que Compress-Archive básico)
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
"$excluir = @('node_modules', 'vendor', '.git', 'public\build', 'public\hot', '.env', '*.bat', 'mafit_backup.sql'); ^
$archivos = Get-ChildItem -Path '%PROYECTO_PATH%' -Recurse | Where-Object { ^
    $ruta = $_.FullName.Substring('%PROYECTO_PATH%'.Length); ^
    $excluirTodo = $false; ^
    foreach ($patron in $excluir) { ^
        if ($patron -like '*.*') { ^
            if ($_.Extension -eq $patron.Substring(1)) { $excluirTodo = $true; break } ^
        } else { ^
            if ($ruta -like '$patron*' -or $ruta -eq $patron) { $excluirTodo = $true; break } ^
        } ^
    }; ^
    return -not $excluirTodo ^
}; ^
$archivos | Compress-Archive -DestinationPath '%ZIP_PATH%' -Force"

if exist "%ZIP_PATH%" (
    echo.
    echo ========================================
    echo   ZIP Creado Exitosamente
    echo ========================================
    echo.
    echo Archivo: %ZIP_PATH%
    
    REM Mostrar tamaño
    for %%A in ("%ZIP_PATH%") do set "TAMANO=%%~zA"
    set /a TAMANO_MB=%TAMANO% / 1048576
    echo Tamaño: %TAMANO_MB% MB
    echo.
    echo ========================================
    echo   Próximos Pasos
    echo ========================================
    echo.
    echo 1. Sube el archivo ZIP a tu VPS usando:
    echo    - WinSCP (recomendado): https://winscp.net/
    echo    - SCP desde PowerShell: scp %ZIP_PATH% usuario@vps-ip:/tmp/
    echo.
    echo 2. En el servidor VPS, ejecuta:
    echo    cd /tmp
    echo    unzip -q mafit_produccion.zip -d mafit_temp
    echo    sudo mv mafit_temp\MAFIT /var/www/mafit
    echo    sudo chown -R www-data:www-data /var/www/mafit
    echo    cd /var/www/mafit
    echo    bash instalar_en_servidor.sh
    echo.
    echo O usa el script PowerShell avanzado:
    echo    .\subir_a_vps.ps1
    echo.
    pause
) else (
    echo.
    echo ERROR: No se pudo crear el archivo ZIP
    echo.
    echo Intenta usar el script PowerShell avanzado:
    echo    .\subir_a_vps.ps1
    echo.
    pause
    exit /b 1
)

