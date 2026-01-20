# Script simple para exportar base de datos MAFIT (sin contraseña)
# Para XAMPP sin contraseña

Write-Host "Exportando base de datos MAFIT..." -ForegroundColor Cyan

$PROJECT_DIR = Split-Path -Parent $MyInvocation.MyCommand.Path
$BACKUP_DIR = Join-Path $PROJECT_DIR "backups"
$DATE_STR = Get-Date -Format "yyyyMMdd_HHmmss"
$BACKUP_FILE = Join-Path $BACKUP_DIR "mafit_backup_$DATE_STR.sql"

# Crear directorio de backups
if (-not (Test-Path $BACKUP_DIR)) {
    New-Item -ItemType Directory -Path $BACKUP_DIR | Out-Null
}

# Buscar mysqldump en XAMPP
$MYSQLDUMP = "C:\xampp\mysql\bin\mysqldump.exe"

if (-not (Test-Path $MYSQLDUMP)) {
    Write-Host "[ERROR] No se encontro MySQL en XAMPP" -ForegroundColor Red
    Write-Host "Ruta esperada: $MYSQLDUMP" -ForegroundColor Yellow
    exit 1
}

# Exportar sin contraseña
Write-Host "Ejecutando mysqldump..." -ForegroundColor Yellow
& $MYSQLDUMP -u root mafit | Out-File -FilePath $BACKUP_FILE -Encoding UTF8

if (Test-Path $BACKUP_FILE) {
    $fileSize = (Get-Item $BACKUP_FILE).Length / 1MB
    Write-Host "[OK] Exportacion completada!" -ForegroundColor Green
    Write-Host "Archivo: $BACKUP_FILE" -ForegroundColor Cyan
    Write-Host "Tamaño: $([math]::Round($fileSize, 2)) MB" -ForegroundColor Cyan
}
else {
    Write-Host "[ERROR] No se pudo crear el archivo de backup" -ForegroundColor Red
}





