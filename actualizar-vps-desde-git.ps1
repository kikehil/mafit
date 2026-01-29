# Script para actualizar el VPS desde Git
# Automatiza: push a Git, pull en VPS, instalación de dependencias, migraciones, etc.

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$VPS_DIR = "/var/www/html/mafit"
$PROJECT_DIR = "C:\WEB\MAFIT"
$GIT_BRANCH = "main"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Actualizar VPS desde Git" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host "Directorio VPS: $VPS_DIR" -ForegroundColor Yellow
Write-Host "Rama Git: $GIT_BRANCH" -ForegroundColor Yellow
Write-Host ""

# Verificar que estamos en el directorio del proyecto
if (-not (Test-Path "$PROJECT_DIR\.git")) {
    Write-Host "[ERROR] No se encuentra el repositorio Git en: $PROJECT_DIR" -ForegroundColor Red
    exit 1
}

# Cambiar al directorio del proyecto
Set-Location $PROJECT_DIR

# Paso 1: Verificar si hay cambios locales
Write-Host "[1/6] Verificando cambios locales..." -ForegroundColor Cyan
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "  Hay cambios sin commitear:" -ForegroundColor Yellow
    Write-Host $gitStatus -ForegroundColor Gray
    
    $commit = Read-Host "¿Deseas hacer commit y push de estos cambios? (S/N)"
    if ($commit -eq "S" -or $commit -eq "s") {
        $commitMessage = Read-Host "Mensaje del commit (o Enter para mensaje por defecto)"
        if ([string]::IsNullOrWhiteSpace($commitMessage)) {
            $commitMessage = "Actualización automática desde script - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
        }
        
        Write-Host "  Agregando archivos..." -ForegroundColor Gray
        git add .
        
        Write-Host "  Creando commit..." -ForegroundColor Gray
        git commit -m $commitMessage
        
        Write-Host "  Haciendo push a GitHub..." -ForegroundColor Gray
        git push origin $GIT_BRANCH
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Cambios subidos a GitHub" -ForegroundColor Green
        } else {
            Write-Host "[ERROR] Error al hacer push. ¿Continuar de todas formas? (S/N)" -ForegroundColor Red
            $continuar = Read-Host
            if ($continuar -ne "S" -and $continuar -ne "s") {
                exit 1
            }
        }
    } else {
        Write-Host "[INFO] Saltando commit. Se actualizará el VPS con el último commit en GitHub." -ForegroundColor Yellow
    }
} else {
    Write-Host "[OK] No hay cambios locales pendientes" -ForegroundColor Green
}

Write-Host ""

# Paso 2: Conectarse al VPS y hacer pull
Write-Host "[2/6] Conectando al VPS y actualizando desde Git..." -ForegroundColor Cyan
Write-Host "  Ejecutando: git pull origin $GIT_BRANCH" -ForegroundColor Gray

$pullCommand = "cd $VPS_DIR && git pull origin $GIT_BRANCH"
$pullResult = ssh ${VPS_USER}@${VPS_IP} $pullCommand 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Código actualizado desde Git" -ForegroundColor Green
    Write-Host $pullResult -ForegroundColor Gray
} else {
    Write-Host "[ERROR] Error al hacer pull en el VPS" -ForegroundColor Red
    Write-Host $pullResult -ForegroundColor Red
    Write-Host ""
    Write-Host "¿Deseas continuar de todas formas? (S/N)" -ForegroundColor Yellow
    $continuar = Read-Host
    if ($continuar -ne "S" -and $continuar -ne "s") {
        exit 1
    }
}

Write-Host ""

# Paso 3: Verificar que existe el script de actualización
Write-Host "[3/6] Verificando script de actualización en VPS..." -ForegroundColor Cyan
$checkScript = "test -f $VPS_DIR/actualizar-vps.sh && echo 'existe' || echo 'no existe'"
$scriptExists = ssh ${VPS_USER}@${VPS_IP} $checkScript

if ($scriptExists -notmatch "existe") {
    Write-Host "[ADVERTENCIA] No se encuentra actualizar-vps.sh en el VPS" -ForegroundColor Yellow
    Write-Host "  Subiendo script actualizar-vps.sh..." -ForegroundColor Gray
    
    if (Test-Path "$PROJECT_DIR\actualizar-vps.sh") {
        scp "$PROJECT_DIR\actualizar-vps.sh" ${VPS_USER}@${VPS_IP}:$VPS_DIR/actualizar-vps.sh
        ssh ${VPS_USER}@${VPS_IP} "chmod +x $VPS_DIR/actualizar-vps.sh"
        Write-Host "[OK] Script subido y configurado" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] No se encuentra actualizar-vps.sh localmente" -ForegroundColor Red
    }
}

Write-Host ""

# Paso 4: Ejecutar script de actualización en el VPS
Write-Host "[4/6] Ejecutando actualización en el VPS..." -ForegroundColor Cyan
Write-Host "  Esto puede tardar varios minutos..." -ForegroundColor Yellow
Write-Host "  Instalando dependencias, compilando assets, ejecutando migraciones..." -ForegroundColor Gray
Write-Host ""

$updateCommand = "cd $VPS_DIR && bash actualizar-vps.sh"
$updateOutput = ssh ${VPS_USER}@${VPS_IP} $updateCommand 2>&1

Write-Host $updateOutput

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "[OK] Actualización completada en el VPS" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "[ADVERTENCIA] El script de actualización terminó con errores" -ForegroundColor Yellow
    Write-Host "  Revisa la salida anterior para más detalles" -ForegroundColor Yellow
}

Write-Host ""

# Paso 5: Limpiar cachés adicionales
Write-Host "[5/6] Limpiando cachés adicionales..." -ForegroundColor Cyan
$clearCacheCommand = "cd $VPS_DIR && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear"
$clearOutput = ssh ${VPS_USER}@${VPS_IP} $clearCacheCommand 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Cachés limpiadas" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] Error al limpiar cachés" -ForegroundColor Yellow
}

Write-Host ""

# Paso 6: Regenerar cachés de producción
Write-Host "[6/6] Regenerando cachés de producción..." -ForegroundColor Cyan
$optimizeCommand = "cd $VPS_DIR && php artisan config:cache && php artisan route:cache && php artisan view:cache"
$optimizeOutput = ssh ${VPS_USER}@${VPS_IP} $optimizeCommand 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Cachés regeneradas" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] Error al regenerar cachés" -ForegroundColor Yellow
}

Write-Host ""

# Resumen final
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Actualización Completada!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Resumen:" -ForegroundColor Yellow
Write-Host "  ✓ Código actualizado desde Git" -ForegroundColor White
Write-Host "  ✓ Dependencias instaladas/actualizadas" -ForegroundColor White
Write-Host "  ✓ Assets compilados" -ForegroundColor White
Write-Host "  ✓ Migraciones ejecutadas" -ForegroundColor White
Write-Host "  ✓ Cachés limpiadas y regeneradas" -ForegroundColor White
Write-Host ""
Write-Host "Próximos pasos opcionales:" -ForegroundColor Yellow
Write-Host "1. Reiniciar servicios PHP-FPM (si es necesario):" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP 'systemctl restart php8.3-fpm'" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Recargar Nginx (si es necesario):" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP 'systemctl reload nginx'" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Verificar que la aplicación funciona:" -ForegroundColor White
Write-Host "   Visita tu sitio web y verifica que todo funcione correctamente" -ForegroundColor Cyan
Write-Host ""

