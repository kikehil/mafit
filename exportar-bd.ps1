# Script PowerShell para exportar la base de datos MAFIT
# Detecta automaticamente XAMPP, WAMP o MySQL del sistema

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Exportar Base de Datos MAFIT" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$PROJECT_DIR = Split-Path -Parent $MyInvocation.MyCommand.Path
$BACKUP_DIR = Join-Path $PROJECT_DIR "backups"
$DATE_STR = Get-Date -Format "yyyyMMdd_HHmmss"
$BACKUP_FILE = Join-Path $BACKUP_DIR "mafit_backup_$DATE_STR.sql"

# Crear directorio de backups si no existe
if (-not (Test-Path $BACKUP_DIR)) {
    New-Item -ItemType Directory -Path $BACKUP_DIR | Out-Null
    Write-Host "[OK] Directorio de backups creado" -ForegroundColor Green
}

# Buscar mysqldump
$MYSQLDUMP = $null

# 1. Intentar con XAMPP
if (Test-Path "C:\xampp\mysql\bin\mysqldump.exe") {
    $MYSQLDUMP = "C:\xampp\mysql\bin\mysqldump.exe"
    Write-Host "[OK] MySQL encontrado en XAMPP" -ForegroundColor Green
}
# 2. Intentar con WAMP
elseif (Test-Path "C:\wamp64\bin\mysql") {
    $mysqlDirs = Get-ChildItem "C:\wamp64\bin\mysql" -Directory -ErrorAction SilentlyContinue | Sort-Object Name -Descending
    foreach ($dir in $mysqlDirs) {
        $dumpPath = Join-Path $dir.FullName "bin\mysqldump.exe"
        if (Test-Path $dumpPath) {
            $MYSQLDUMP = $dumpPath
            Write-Host "[OK] MySQL encontrado en WAMP: $($dir.Name)" -ForegroundColor Green
            break
        }
    }
}
# 3. Intentar con Laragon
elseif (Test-Path "C:\laragon\bin\mysql") {
    $mysqlDirs = Get-ChildItem "C:\laragon\bin\mysql" -Directory -ErrorAction SilentlyContinue | Sort-Object Name -Descending
    foreach ($dir in $mysqlDirs) {
        $dumpPath = Join-Path $dir.FullName "bin\mysqldump.exe"
        if (Test-Path $dumpPath) {
            $MYSQLDUMP = $dumpPath
            Write-Host "[OK] MySQL encontrado en Laragon: $($dir.Name)" -ForegroundColor Green
            break
        }
    }
}
# 4. Intentar con MySQL del sistema (en PATH)
else {
    $cmd = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($cmd) {
        $MYSQLDUMP = $cmd.Source
        Write-Host "[OK] MySQL encontrado en el sistema" -ForegroundColor Green
    }
}

if (-not $MYSQLDUMP) {
    Write-Host "[ERROR] No se encontro mysqldump" -ForegroundColor Red
    Write-Host ""
    Write-Host "Por favor, ejecuta manualmente:" -ForegroundColor Yellow
    Write-Host "  C:\xampp\mysql\bin\mysqldump.exe -u root mafit > mafit_backup.sql" -ForegroundColor White
    exit 1
}

# Exportar base de datos - intentar primero sin contraseña
Write-Host ""
Write-Host "Exportando base de datos (intentando sin contraseña)..." -ForegroundColor Yellow

$exportSuccess = $false
$errorOutput = $null

# Intentar primero sin contraseña (comun en XAMPP)
try {
    $process = Start-Process -FilePath $MYSQLDUMP -ArgumentList "-u", "root", "mafit" -NoNewWindow -Wait -PassThru -RedirectStandardOutput $BACKUP_FILE -RedirectStandardError "temp_error.txt"
    
    if ($process.ExitCode -eq 0 -and (Test-Path $BACKUP_FILE)) {
        $fileSize = (Get-Item $BACKUP_FILE).Length
        if ($fileSize -gt 0) {
            $exportSuccess = $true
        }
    }
    
    if (Test-Path "temp_error.txt") {
        $errorOutput = Get-Content "temp_error.txt" -Raw
        Remove-Item "temp_error.txt" -ErrorAction SilentlyContinue
    }
}
catch {
    $errorOutput = $_.Exception.Message
}

# Si fallo sin contraseña, pedir contraseña
if (-not $exportSuccess) {
    Write-Host "[INFO] Intento sin contraseña fallo, solicitando contraseña..." -ForegroundColor Yellow
    Write-Host ""
    $password = Read-Host "Ingresa la contraseña de MySQL (o presiona Enter para cancelar)"
    
    if (-not [string]::IsNullOrWhiteSpace($password)) {
        Write-Host "Intentando con contraseña..." -ForegroundColor Yellow
        try {
            $env:MYSQL_PWD = $password
            $process = Start-Process -FilePath $MYSQLDUMP -ArgumentList "-u", "root", "mafit" -NoNewWindow -Wait -PassThru -RedirectStandardOutput $BACKUP_FILE -RedirectStandardError "temp_error.txt"
            
            if ($process.ExitCode -eq 0 -and (Test-Path $BACKUP_FILE)) {
                $fileSize = (Get-Item $BACKUP_FILE).Length
                if ($fileSize -gt 0) {
                    $exportSuccess = $true
                }
            }
            
            Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
            if (Test-Path "temp_error.txt") {
                Remove-Item "temp_error.txt" -ErrorAction SilentlyContinue
            }
        }
        catch {
            Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
            $errorOutput = $_.Exception.Message
        }
    }
}

# Mostrar resultado
Write-Host ""
if ($exportSuccess -and (Test-Path $BACKUP_FILE)) {
    $fileSize = (Get-Item $BACKUP_FILE).Length / 1MB
    Write-Host "[OK] Base de datos exportada exitosamente!" -ForegroundColor Green
    Write-Host "  Archivo: $BACKUP_FILE" -ForegroundColor Cyan
    Write-Host "  Tamaño: $([math]::Round($fileSize, 2)) MB" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Para subir al VPS, ejecuta:" -ForegroundColor Yellow
    Write-Host "  scp `"$BACKUP_FILE`" usuario@tu-vps-ip:/tmp/mafit_backup.sql" -ForegroundColor White
}
else {
    Write-Host "[ERROR] No se pudo exportar la base de datos" -ForegroundColor Red
    if ($errorOutput) {
        Write-Host "  Error: $errorOutput" -ForegroundColor Red
    }
    Write-Host ""
    Write-Host "Intenta ejecutar manualmente:" -ForegroundColor Yellow
    Write-Host "  & `"$MYSQLDUMP`" -u root mafit > `"$BACKUP_FILE`"" -ForegroundColor White
}

Write-Host ""
