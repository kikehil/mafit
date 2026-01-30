# Script PowerShell para solucionar error 403 en VPS con Docker
# Verifica y corrige permisos, reinicia contenedores

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$VPS_DIR = "/var/www/html/mafit"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Solucionar Error 403 - VPS con Docker" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host "Directorio: $VPS_DIR" -ForegroundColor Yellow
Write-Host ""

# Verificar que Docker esta disponible
Write-Host "[1/5] Verificando Docker..." -ForegroundColor Cyan
$dockerCheck = ssh ${VPS_USER}@${VPS_IP} "docker --version 2>&1" 2>&1
if ($dockerCheck -notmatch "Docker version") {
    Write-Host "[ERROR] Docker no esta disponible en el VPS" -ForegroundColor Red
    Write-Host "  Usa el script para Apache directo: .\solucionar-403-vps.ps1" -ForegroundColor Yellow
    exit 1
}
Write-Host "[OK] Docker esta disponible" -ForegroundColor Green
Write-Host ""

# Verificar contenedores de mafit
Write-Host "[2/5] Verificando contenedores de MAFIT..." -ForegroundColor Cyan
$containers = ssh ${VPS_USER}@${VPS_IP} "cd $VPS_DIR && docker compose ps 2>&1" 2>&1
Write-Host $containers -ForegroundColor Gray

if ($containers -match "mafit") {
    Write-Host "[OK] Contenedores de MAFIT encontrados" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] No se encontraron contenedores de MAFIT corriendo" -ForegroundColor Yellow
    Write-Host "  Intentando iniciar contenedores..." -ForegroundColor Gray
}

Write-Host ""

# Corregir permisos en el host (necesario para Docker)
Write-Host "[3/5] Corrigiendo permisos en el host..." -ForegroundColor Cyan
$fixPerms = @"
cd $VPS_DIR
# Permisos para archivos
find . -type f -exec chmod 644 {} \;
# Permisos para directorios  
find . -type d -exec chmod 755 {} \;
# Permisos especiales para storage
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
# Propietario
chown -R www-data:www-data . 2>/dev/null || chown -R 33:33 . 2>/dev/null || true
echo '[OK] Permisos corregidos'
"@

$permsOutput = ssh ${VPS_USER}@${VPS_IP} $fixPerms 2>&1
Write-Host $permsOutput -ForegroundColor Gray
Write-Host ""

# Verificar que docker-compose.yml existe
Write-Host "[4/5] Verificando docker-compose.yml..." -ForegroundColor Cyan
$composeExists = ssh ${VPS_USER}@${VPS_IP} "test -f $VPS_DIR/docker-compose.yml && echo 'existe' || echo 'no existe'" 2>&1
if ($composeExists -match "existe") {
    Write-Host "[OK] docker-compose.yml existe" -ForegroundColor Green
    
    # Verificar configuracion de nginx en Docker
    Write-Host "  Verificando configuracion de Nginx en Docker..." -ForegroundColor Gray
    $nginxConfig = ssh ${VPS_USER}@${VPS_IP} "cat $VPS_DIR/docker/nginx/default.conf 2>&1 | grep -i 'root' | head -n 1" 2>&1
    if ($nginxConfig) {
        Write-Host "  Configuracion Nginx:" -ForegroundColor Gray
        Write-Host "  $nginxConfig" -ForegroundColor Gray
        
        if ($nginxConfig -notmatch "/var/www/public") {
            Write-Host "[ADVERTENCIA] El root de Nginx puede no estar correcto" -ForegroundColor Yellow
            Write-Host "  Deberia ser: root /var/www/public;" -ForegroundColor Gray
        }
    }
} else {
    Write-Host "[ADVERTENCIA] docker-compose.yml no existe" -ForegroundColor Yellow
    Write-Host "  Subiendo docker-compose.yml..." -ForegroundColor Gray
    
    if (Test-Path "$PROJECT_DIR\docker-compose.yml") {
        scp "$PROJECT_DIR\docker-compose.yml" ${VPS_USER}@${VPS_IP}:$VPS_DIR/docker-compose.yml
        Write-Host "[OK] docker-compose.yml subido" -ForegroundColor Green
    }
}

Write-Host ""

# Reiniciar contenedores
Write-Host "[5/5] Reiniciando contenedores Docker..." -ForegroundColor Cyan
Write-Host "  Esto puede tardar unos segundos..." -ForegroundColor Gray

$restartCommand = "cd $VPS_DIR && docker compose down && docker compose up -d"
$restartOutput = ssh ${VPS_USER}@${VPS_IP} $restartCommand 2>&1

Write-Host $restartOutput -ForegroundColor Gray

# Verificar estado de contenedores
Write-Host ""
Write-Host "Estado de contenedores:" -ForegroundColor Cyan
$status = ssh ${VPS_USER}@${VPS_IP} "cd $VPS_DIR && docker compose ps" 2>&1
Write-Host $status -ForegroundColor Gray

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Resumen" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Acciones completadas:" -ForegroundColor Yellow
Write-Host "  Permisos corregidos en el host" -ForegroundColor White
Write-Host "  Contenedores reiniciados" -ForegroundColor White
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Verificar logs de contenedores:" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP 'cd $VPS_DIR && docker compose logs nginx'" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Verificar que los contenedores esten corriendo:" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP 'cd $VPS_DIR && docker compose ps'" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Si el puerto es diferente (ej: 8080), verifica:" -ForegroundColor White
Write-Host "   http://mafit.regiontamaulipas.com.mx:8080" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. Verificar configuracion de puertos en docker-compose.yml:" -ForegroundColor White
Write-Host "   El puerto debe estar mapeado correctamente" -ForegroundColor Gray
Write-Host ""


