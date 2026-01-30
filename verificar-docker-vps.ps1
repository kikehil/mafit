# Script para verificar si el VPS usa Docker
# Detecta si hay contenedores corriendo y la configuracion

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$VPS_DIR = "/var/www/html/mafit"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Verificar Configuracion Docker en VPS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host "Directorio: $VPS_DIR" -ForegroundColor Yellow
Write-Host ""

# Verificar si Docker esta instalado
Write-Host "[1/4] Verificando si Docker esta instalado..." -ForegroundColor Cyan
$dockerVersion = ssh ${VPS_USER}@${VPS_IP} "docker --version 2>&1" 2>&1
if ($dockerVersion -match "Docker version") {
    Write-Host "[OK] Docker esta instalado:" -ForegroundColor Green
    Write-Host $dockerVersion -ForegroundColor Gray
} else {
    Write-Host "[INFO] Docker no esta instalado o no esta en PATH" -ForegroundColor Yellow
    Write-Host "  El servidor probablemente usa Apache/Nginx directamente" -ForegroundColor Gray
}

Write-Host ""

# Verificar si Docker Compose esta instalado
Write-Host "[2/4] Verificando si Docker Compose esta instalado..." -ForegroundColor Cyan
$composeVersion = ssh ${VPS_USER}@${VPS_IP} "docker compose version 2>&1" 2>&1
if ($composeVersion -match "Docker Compose") {
    Write-Host "[OK] Docker Compose esta instalado:" -ForegroundColor Green
    Write-Host $composeVersion -ForegroundColor Gray
} else {
    Write-Host "[INFO] Docker Compose no esta disponible" -ForegroundColor Yellow
}

Write-Host ""

# Verificar contenedores corriendo
Write-Host "[3/4] Verificando contenedores Docker..." -ForegroundColor Cyan
$containers = ssh ${VPS_USER}@${VPS_IP} "docker ps -a 2>&1" 2>&1
if ($containers -match "CONTAINER ID") {
    Write-Host "[OK] Hay contenedores Docker:" -ForegroundColor Green
    Write-Host $containers -ForegroundColor Gray
    
    # Verificar contenedores de mafit
    $mafitContainers = ssh ${VPS_USER}@${VPS_IP} "docker ps -a | grep mafit 2>&1" 2>&1
    if ($mafitContainers) {
        Write-Host ""
        Write-Host "Contenedores de MAFIT encontrados:" -ForegroundColor Yellow
        Write-Host $mafitContainers -ForegroundColor Gray
    }
} else {
    Write-Host "[INFO] No hay contenedores Docker corriendo" -ForegroundColor Yellow
    Write-Host "  O Docker no esta disponible" -ForegroundColor Gray
}

Write-Host ""

# Verificar si existe docker-compose.yml en el VPS
Write-Host "[4/4] Verificando archivos de Docker en el proyecto..." -ForegroundColor Cyan
$dockerComposeExists = ssh ${VPS_USER}@${VPS_IP} "test -f $VPS_DIR/docker-compose.yml && echo 'existe' || echo 'no existe'" 2>&1
if ($dockerComposeExists -match "existe") {
    Write-Host "[OK] docker-compose.yml existe en el VPS" -ForegroundColor Green
    
    # Verificar servicios definidos
    $services = ssh ${VPS_USER}@${VPS_IP} "cd $VPS_DIR && docker compose config --services 2>&1" 2>&1
    if ($services) {
        Write-Host "Servicios definidos:" -ForegroundColor Yellow
        Write-Host $services -ForegroundColor Gray
    }
} else {
    Write-Host "[INFO] docker-compose.yml no existe en el VPS" -ForegroundColor Yellow
}

Write-Host ""

# Verificar servidor web activo
Write-Host "[EXTRA] Verificando servidor web activo..." -ForegroundColor Cyan
$apacheStatus = ssh ${VPS_USER}@${VPS_IP} "systemctl is-active apache2 2>&1" 2>&1
$nginxStatus = ssh ${VPS_USER}@${VPS_IP} "systemctl is-active nginx 2>&1" 2>&1

if ($apacheStatus -eq "active") {
    Write-Host "[INFO] Apache esta activo" -ForegroundColor Yellow
} elseif ($nginxStatus -eq "active") {
    Write-Host "[INFO] Nginx esta activo" -ForegroundColor Yellow
} else {
    Write-Host "[INFO] No se detecto Apache ni Nginx activos como servicios del sistema" -ForegroundColor Yellow
    Write-Host "  Puede que esten corriendo en Docker" -ForegroundColor Gray
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Resumen" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Si Docker esta en uso:" -ForegroundColor Yellow
Write-Host "  - Usa: .\solucionar-403-docker-vps.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "Si NO usa Docker:" -ForegroundColor Yellow
Write-Host "  - Usa: .\solucionar-403-vps.ps1" -ForegroundColor Cyan
Write-Host ""


