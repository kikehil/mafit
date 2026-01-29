# Script para subir archivos al VPS
# Configura tus datos aquí

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$PROJECT_DIR = "C:\WEB\MAFIT"
$BACKUP_FILE = "C:\WEB\MAFIT\backups\mafit_backup_20260113_185237.sql"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Subir MAFIT al VPS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host ""

# Verificar que los archivos existen
if (-not (Test-Path $PROJECT_DIR)) {
    Write-Host "[ERROR] No se encuentra el proyecto en: $PROJECT_DIR" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $BACKUP_FILE)) {
    Write-Host "[ADVERTENCIA] No se encuentra el backup: $BACKUP_FILE" -ForegroundColor Yellow
    Write-Host "Continuando sin backup..." -ForegroundColor Yellow
}

# Paso 1: Subir el proyecto completo
Write-Host "[1/3] Subiendo proyecto completo..." -ForegroundColor Cyan
Write-Host "Esto puede tardar varios minutos..." -ForegroundColor Yellow
scp -r "$PROJECT_DIR" ${VPS_USER}@${VPS_IP}:/var/www/mafit

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Proyecto subido exitosamente" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error al subir el proyecto" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Paso 2: Subir los scripts de despliegue y actualización
Write-Host "[2/4] Subiendo scripts de despliegue..." -ForegroundColor Cyan
scp "$PROJECT_DIR\deploy-vps.sh" ${VPS_USER}@${VPS_IP}:/tmp/deploy-vps.sh
scp "$PROJECT_DIR\actualizar-vps.sh" ${VPS_USER}@${VPS_IP}:/var/www/mafit/actualizar-vps.sh

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Scripts de despliegue subidos" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error al subir los scripts" -ForegroundColor Red
}

Write-Host ""

# Paso 3: Subir el backup de la base de datos
if (Test-Path $BACKUP_FILE) {
    Write-Host "[3/4] Subiendo backup de base de datos..." -ForegroundColor Cyan
    Write-Host "Esto puede tardar un momento..." -ForegroundColor Yellow
    scp "$BACKUP_FILE" ${VPS_USER}@${VPS_IP}:/tmp/mafit_backup.sql
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Backup subido exitosamente" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Error al subir el backup" -ForegroundColor Red
    }
} else {
    Write-Host "[3/4] Omitiendo backup (archivo no encontrado)" -ForegroundColor Yellow
}

Write-Host ""

# Paso 4: Dar permisos de ejecución al script de actualización
Write-Host "[4/4] Configurando permisos en el VPS..." -ForegroundColor Cyan
ssh ${VPS_USER}@${VPS_IP} "chmod +x /var/www/mafit/actualizar-vps.sh"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Permisos configurados" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] No se pudieron configurar permisos automáticamente" -ForegroundColor Yellow
    Write-Host "Ejecuta manualmente: chmod +x /var/www/mafit/actualizar-vps.sh" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Archivos subidos correctamente!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Proximos pasos:" -ForegroundColor Yellow
Write-Host "1. Conectate al VPS:" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Ejecuta el script de actualización:" -ForegroundColor White
Write-Host "   cd /var/www/mafit" -ForegroundColor Cyan
Write-Host "   bash actualizar-vps.sh" -ForegroundColor Cyan
Write-Host ""
Write-Host "   O si es primera vez, ejecuta el despliegue completo:" -ForegroundColor White
Write-Host "   chmod +x /tmp/deploy-vps.sh" -ForegroundColor Cyan
Write-Host "   sudo /tmp/deploy-vps.sh" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Importa la base de datos (si es necesario):" -ForegroundColor White
Write-Host "   mysql -u mafit_user -p mafit < /tmp/mafit_backup.sql" -ForegroundColor Cyan
Write-Host ""









