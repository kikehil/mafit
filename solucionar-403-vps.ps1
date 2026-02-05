# Script PowerShell para solucionar error 403 en VPS Apache
# Sube y ejecuta el script de solucion en el VPS

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$VPS_DIR = "/var/www/html/mafit"
$PROJECT_DIR = "C:\WEB\MAFIT"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Solucionar Error 403 Forbidden en VPS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host "Directorio VPS: $VPS_DIR" -ForegroundColor Yellow
Write-Host ""

# Verificar que existe el script local
if (-not (Test-Path "$PROJECT_DIR\solucionar-403-apache-vps.sh")) {
    Write-Host "[ERROR] No se encuentra solucionar-403-apache-vps.sh" -ForegroundColor Red
    exit 1
}

# Subir script al VPS
Write-Host "[1/3] Subiendo script de solucion al VPS..." -ForegroundColor Cyan
scp "$PROJECT_DIR\solucionar-403-apache-vps.sh" ${VPS_USER}@${VPS_IP}:/tmp/solucionar-403-apache-vps.sh

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Script subido" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error al subir script" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Dar permisos de ejecucion
Write-Host "[2/3] Configurando permisos de ejecucion..." -ForegroundColor Cyan
ssh ${VPS_USER}@${VPS_IP} "chmod +x /tmp/solucionar-403-apache-vps.sh"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Permisos configurados" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error al configurar permisos" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Ejecutar script en el VPS
Write-Host "[3/3] Ejecutando script de solucion en el VPS..." -ForegroundColor Cyan
Write-Host "  Esto corregira permisos y configuracion de Apache" -ForegroundColor Gray
Write-Host ""

$scriptOutput = ssh ${VPS_USER}@${VPS_IP} "bash /tmp/solucionar-403-apache-vps.sh" 2>&1

Write-Host $scriptOutput

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "[OK] Script ejecutado correctamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos pasos:" -ForegroundColor Yellow
    Write-Host "1. Reiniciar Apache en el VPS:" -ForegroundColor White
    Write-Host "   ssh $VPS_USER@$VPS_IP 'systemctl restart apache2'" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "2. Verificar que el sitio funciona:" -ForegroundColor White
    Write-Host "   Visita: http://mafit.regiontamaulipas.com.mx" -ForegroundColor Cyan
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "[ADVERTENCIA] El script terminó con errores" -ForegroundColor Yellow
    Write-Host "  Revisa la salida anterior para más detalles" -ForegroundColor Yellow
}

Write-Host ""






