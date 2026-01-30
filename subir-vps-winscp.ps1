# Script alternativo usando WinSCP (más fácil para contraseñas con caracteres especiales)
# Requiere WinSCP instalado: https://winscp.net/

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$PROJECT_DIR = "C:\WEB\MAFIT"
$VPS_DIR = "/var/www/mafit"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Subir MAFIT al VPS (WinSCP)" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host ""

# Verificar que WinSCP está instalado
$winscpPath = "C:\Program Files (x86)\WinSCP\WinSCP.com"
if (-not (Test-Path $winscpPath)) {
    $winscpPath = "C:\Program Files\WinSCP\WinSCP.com"
    if (-not (Test-Path $winscpPath)) {
        Write-Host "[ERROR] WinSCP no está instalado." -ForegroundColor Red
        Write-Host "Descarga desde: https://winscp.net/" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "O usa el método manual:" -ForegroundColor Yellow
        Write-Host "1. Abre WinSCP" -ForegroundColor White
        Write-Host "2. Conéctate a: $VPS_USER@$VPS_IP" -ForegroundColor White
        Write-Host "3. Arrastra la carpeta $PROJECT_DIR a $VPS_DIR" -ForegroundColor White
        exit 1
    }
}

Write-Host "[INFO] Usando WinSCP para subir archivos..." -ForegroundColor Green
Write-Host ""

# Crear script temporal de WinSCP
$scriptTemp = [System.IO.Path]::GetTempFileName() + ".txt"
$scriptContent = @"
option batch abort
option confirm off
open sftp://$VPS_USER@$VPS_IP -hostkey=*
cd $VPS_DIR
put -exclude="node_modules;vendor;.git;storage\logs;storage\framework\cache;storage\framework\sessions;storage\framework\views;bootstrap\cache;public\build;backups;*.bat;*.log;*.sql;*.zip;*.rar;*.tmp" "$PROJECT_DIR\*" .
exit
"@

$scriptContent | Out-File -FilePath $scriptTemp -Encoding ASCII

Write-Host "[INFO] Script de WinSCP creado: $scriptTemp" -ForegroundColor Cyan
Write-Host ""
Write-Host "INSTRUCCIONES:" -ForegroundColor Yellow
Write-Host "1. Se abrirá WinSCP y te pedirá la contraseña" -ForegroundColor White
Write-Host "2. Ingresa tu contraseña cuando te la pida" -ForegroundColor White
Write-Host "3. El script subirá los archivos automáticamente" -ForegroundColor White
Write-Host ""
$confirmar = Read-Host "¿Continuar? (S/N)"
if ($confirmar -ne "S" -and $confirmar -ne "s") {
    Remove-Item $scriptTemp -ErrorAction SilentlyContinue
    Write-Host "Operación cancelada." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "[INFO] Ejecutando WinSCP..." -ForegroundColor Cyan
Write-Host "Ingresa tu contraseña cuando te la pida..." -ForegroundColor Yellow
Write-Host ""

# Ejecutar WinSCP
& $winscpPath /script=$scriptTemp

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "[OK] Archivos subidos exitosamente" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "[ERROR] Error al subir archivos" -ForegroundColor Red
    Write-Host "Verifica la contraseña y la conexión" -ForegroundColor Yellow
}

# Limpiar script temporal
Remove-Item $scriptTemp -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Próximos pasos" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Conéctate al VPS y ejecuta:" -ForegroundColor Yellow
Write-Host "  ssh $VPS_USER@$VPS_IP" -ForegroundColor Cyan
Write-Host "  cd $VPS_DIR" -ForegroundColor Cyan
Write-Host "  bash actualizar-vps.sh" -ForegroundColor Cyan
Write-Host ""





