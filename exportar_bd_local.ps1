# Script PowerShell para exportar la base de datos local de MAFIT
# Uso: .\exportar_bd_local.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Exportando Base de Datos MAFIT Local" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Leer configuración de .env
$envContent = Get-Content .env
$dbName = ($envContent | Select-String "DB_DATABASE=").ToString().Split("=")[1].Trim()
$dbUser = ($envContent | Select-String "DB_USERNAME=").ToString().Split("=")[1].Trim()
$dbPassLine = $envContent | Select-String "DB_PASSWORD="
if ($dbPassLine) {
    $dbPass = $dbPassLine.ToString().Split("=")[1].Trim()
} else {
    $dbPass = ""
}

Write-Host "Base de datos: $dbName" -ForegroundColor Yellow
Write-Host "Usuario: $dbUser" -ForegroundColor Yellow
Write-Host ""

# Crear directorio de exportación si no existe
if (-not (Test-Path "backups")) {
    New-Item -ItemType Directory -Path "backups" | Out-Null
    Write-Host "Carpeta 'backups' creada" -ForegroundColor Green
}

# Generar nombre de archivo con fecha
$fecha = Get-Date -Format "yyyyMMdd"
$hora = Get-Date -Format "HHmmss"
$archivo = "backups\mafit_export_${fecha}_${hora}.sql"

Write-Host "Exportando a: $archivo" -ForegroundColor Yellow
Write-Host ""

# Verificar que mysqldump existe
$mysqldumpPath = "C:\xampp\mysql\bin\mysqldump.exe"
if (-not (Test-Path $mysqldumpPath)) {
    Write-Host "ERROR: No se encontró mysqldump.exe en: $mysqldumpPath" -ForegroundColor Red
    Write-Host "Por favor, ajusta la ruta en el script según tu instalación de XAMPP" -ForegroundColor Yellow
    exit 1
}

# Exportar base de datos completa
Write-Host "Exportando base de datos..." -ForegroundColor Cyan

# Construir comando según si hay contraseña o no
if ([string]::IsNullOrEmpty($dbPass)) {
    # Sin contraseña
    $result = & $mysqldumpPath -u $dbUser $dbName | Out-File -FilePath $archivo -Encoding UTF8
} else {
    # Con contraseña (usar --password= para evitar prompt interactivo)
    $result = & $mysqldumpPath -u $dbUser --password=$dbPass $dbName | Out-File -FilePath $archivo -Encoding UTF8
}

if ($LASTEXITCODE -eq 0) {
    $fileSize = (Get-Item $archivo).Length / 1MB
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "Exportación completada exitosamente!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "Archivo: $archivo" -ForegroundColor Yellow
    Write-Host "Tamaño: $([math]::Round($fileSize, 2)) MB" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Siguiente paso: Subir este archivo al VPS e importarlo" -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "ERROR en la exportación" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "Verifica:" -ForegroundColor Yellow
    Write-Host "1. Que MySQL esté corriendo" -ForegroundColor Yellow
    Write-Host "2. Que las credenciales en .env sean correctas" -ForegroundColor Yellow
    Write-Host "3. Que la ruta de mysqldump sea correcta" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Read-Host "Presiona Enter para continuar"

