# Script para verificar los servicios disponibles en el VPS
# Ayuda a identificar los nombres correctos de los servicios PHP-FPM y Nginx

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Verificar Servicios en VPS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host ""

# Verificar servicios PHP-FPM
Write-Host "[1/3] Verificando servicios PHP-FPM disponibles..." -ForegroundColor Cyan
$phpServices = ssh ${VPS_USER}@${VPS_IP} "systemctl list-units --type=service --all | grep -i php | grep -i fpm" 2>&1
if ($phpServices) {
    Write-Host "Servicios PHP-FPM encontrados:" -ForegroundColor Green
    Write-Host $phpServices -ForegroundColor Gray
} else {
    Write-Host "[INFO] No se encontraron servicios PHP-FPM activos" -ForegroundColor Yellow
    Write-Host "Buscando todos los servicios PHP..." -ForegroundColor Gray
    $allPhp = ssh ${VPS_USER}@${VPS_IP} "systemctl list-units --type=service --all | grep -i php" 2>&1
    if ($allPhp) {
        Write-Host $allPhp -ForegroundColor Gray
    }
}

Write-Host ""

# Verificar versión de PHP instalada
Write-Host "[2/3] Verificando versión de PHP instalada..." -ForegroundColor Cyan
$phpVersion = ssh ${VPS_USER}@${VPS_IP} "php -v 2>&1 | head -n 1" 2>&1
if ($phpVersion) {
    Write-Host "Versión de PHP:" -ForegroundColor Green
    Write-Host $phpVersion -ForegroundColor Gray
    
    # Extraer número de versión
    if ($phpVersion -match "PHP (\d+\.\d+)") {
        $version = $matches[1]
        Write-Host ""
        Write-Host "Servicio PHP-FPM probable: php$version-fpm" -ForegroundColor Yellow
        Write-Host "Comando sugerido: systemctl restart php$version-fpm" -ForegroundColor Cyan
    }
} else {
    Write-Host "[ERROR] No se pudo obtener la versión de PHP" -ForegroundColor Red
}

Write-Host ""

# Verificar Nginx
Write-Host "[3/3] Verificando servicio Nginx..." -ForegroundColor Cyan
$nginxStatus = ssh ${VPS_USER}@${VPS_IP} "systemctl status nginx 2>&1 | head -n 3" 2>&1
if ($nginxStatus) {
    Write-Host "Estado de Nginx:" -ForegroundColor Green
    Write-Host $nginxStatus -ForegroundColor Gray
    
    # Verificar si está activo
    $nginxActive = ssh ${VPS_USER}@${VPS_IP} "systemctl is-active nginx 2>&1" 2>&1
    if ($nginxActive -eq "active") {
        Write-Host ""
        Write-Host "[OK] Nginx está activo" -ForegroundColor Green
        Write-Host "Comando para recargar: systemctl reload nginx" -ForegroundColor Cyan
    } else {
        Write-Host ""
        Write-Host "[ADVERTENCIA] Nginx no está activo" -ForegroundColor Yellow
        Write-Host "Estado: $nginxActive" -ForegroundColor Gray
        Write-Host "Comando para iniciar: systemctl start nginx" -ForegroundColor Cyan
    }
} else {
    Write-Host "[ERROR] No se pudo verificar Nginx" -ForegroundColor Red
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Resumen" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Para reiniciar servicios, usa los comandos sugeridos arriba." -ForegroundColor White
Write-Host ""
Write-Host "Alternativamente, puedes verificar manualmente:" -ForegroundColor Yellow
Write-Host "  ssh $VPS_USER@$VPS_IP 'systemctl list-units --type=service | grep -E \"php|nginx\"'" -ForegroundColor Cyan
Write-Host ""

