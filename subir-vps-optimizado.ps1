# Script optimizado para subir solo archivos necesarios al VPS
# Excluye: node_modules, vendor, storage/logs, .git, etc.

$VPS_USER = "root"
$VPS_IP = "147.93.118.121"
$PROJECT_DIR = "C:\WEB\MAFIT"
$VPS_DIR = "/var/www/html/mafit"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Subir MAFIT al VPS (Optimizado)" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "VPS: $VPS_USER@$VPS_IP" -ForegroundColor Yellow
Write-Host "Este script solo sube archivos necesarios (excluye node_modules, vendor, storage/logs)" -ForegroundColor Green
Write-Host ""

# Verificar que el proyecto existe
if (-not (Test-Path $PROJECT_DIR)) {
    Write-Host "[ERROR] No se encuentra el proyecto en: $PROJECT_DIR" -ForegroundColor Red
    exit 1
}

# Crear lista de exclusiones (más completo)
$excluir = @(
    'node_modules',
    'vendor',
    '.git',
    'storage\logs',
    'storage\framework\cache',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\framework\testing',
    'bootstrap\cache',
    'public\build',
    'public\hot',
    '.env',
    '*.bat',
    '*.log',
    '*.sql',
    'mafit_backup*.sql',
    'backups',
    '.idea',
    '.vscode',
    '*.zip',
    '*.rar',
    '*.tmp',
    'Thumbs.db',
    '.DS_Store'
)

# Patrones adicionales para exclusiones más agresivas
$excluirPatrones = @(
    '*\node_modules\*',
    '*\vendor\*',
    '*\.git\*',
    '*\storage\logs\*',
    '*\storage\framework\cache\*',
    '*\storage\framework\sessions\*',
    '*\storage\framework\views\*',
    '*\bootstrap\cache\*',
    '*\public\build\*',
    '*\backups\*'
)

Write-Host "[INFO] Preparando lista de archivos a subir..." -ForegroundColor Cyan
Write-Host "Excluyendo: node_modules, vendor, storage/logs, .git, etc." -ForegroundColor Yellow
Write-Host ""

# Función para verificar si un archivo debe excluirse
function Should-Exclude {
    param($filePath, $relativePath)
    
    # Normalizar rutas para comparación
    $normalizedPath = $filePath.Replace('\', '/').ToLower()
    $normalizedRelative = $relativePath.Replace('\', '/').ToLower()
    
    # Verificar patrones de exclusión completos primero
    foreach ($patronItem in $excluirPatrones) {
        $patronNormalized = $patronItem.Replace('\', '/').ToLower()
        if ($normalizedPath -like $patronNormalized) {
            return $true
        }
    }
    
    # Verificar exclusiones simples
    foreach ($patronItem in $excluir) {
        # Guardar el patrón original como string
        $patronStr = [string]$patronItem
        
        # Si es un patrón de extensión (*.ext)
        if ($patronStr -like '*.*') {
            $ext = $patronStr.Substring(1).ToLower()
            if ($filePath.ToLower() -like "*$ext") {
                return $true
            }
        }
        # Si es una carpeta o ruta
        else {
            $patronNormalized = $patronStr.Replace('\', '/').ToLower()
            if ($normalizedRelative -like "*$patronNormalized*" -or $normalizedRelative -eq $patronNormalized) {
                return $true
            }
        }
    }
    return $false
}

# Obtener archivos a subir (excluyendo carpetas grandes primero)
Write-Host "[INFO] Filtrando archivos..." -ForegroundColor Cyan

# Obtener todos los archivos primero
$todosArchivos = Get-ChildItem -Path $PROJECT_DIR -Recurse -File -ErrorAction SilentlyContinue

# Filtrar excluyendo carpetas grandes
$archivos = $todosArchivos | Where-Object {
    $fullPath = $_.FullName
    $relativePath = $fullPath.Substring($PROJECT_DIR.Length + 1)
    
    # Excluir si está en alguna de las carpetas grandes
    $excluir = $false
    
    # Verificar rutas específicas (más rápido)
    if ($fullPath -match '\\node_modules\\' -or 
        $fullPath -match '\\vendor\\' -or 
        $fullPath -match '\\storage\\logs\\' -or
        $fullPath -match '\\storage\\framework\\cache\\' -or
        $fullPath -match '\\storage\\framework\\sessions\\' -or
        $fullPath -match '\\storage\\framework\\views\\' -or
        $fullPath -match '\\.git\\' -or
        $fullPath -match '\\bootstrap\\cache\\' -or
        $fullPath -match '\\public\\build\\' -or
        $fullPath -match '\\backups\\' -or
        $fullPath -match '\\.env$' -or
        $fullPath -match '\.(bat|log|sql|zip|rar|tmp)$' -or
        $fullPath -match 'Thumbs\.db$' -or
        $fullPath -match '\.DS_Store$') {
        $excluir = $true
    }
    
    # Si no está excluido, verificar con la función
    if (-not $excluir) {
        $excluir = Should-Exclude $fullPath $relativePath
    }
    
    return -not $excluir
}

$totalArchivos = $archivos.Count
$totalSize = ($archivos | Measure-Object -Property Length -Sum).Sum / 1MB

Write-Host "[INFO] Archivos a subir: $totalArchivos archivos (~$([math]::Round($totalSize, 2)) MB)" -ForegroundColor Green
Write-Host ""

# Confirmar antes de continuar
$confirmar = Read-Host "¿Continuar con la subida? (S/N)"
if ($confirmar -ne "S" -and $confirmar -ne "s") {
    Write-Host "Operación cancelada." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "[1/4] Subiendo archivos del proyecto..." -ForegroundColor Cyan
Write-Host "Esto puede tardar varios minutos..." -ForegroundColor Yellow

# Subir archivos usando rsync (si está disponible) o scp
$archivosSubidos = 0
$errores = 0

foreach ($archivo in $archivos) {
    $relativePath = $archivo.FullName.Substring($PROJECT_DIR.Length + 1)
    $destPath = "$VPS_DIR/$relativePath".Replace('\', '/')
    $destDir = Split-Path $destPath -Parent
    
    # Crear directorio remoto si no existe
    ssh ${VPS_USER}@${VPS_IP} "mkdir -p `"$destDir`"" 2>$null
    
    # Subir archivo
    scp -q "`"$($archivo.FullName)`"" "${VPS_USER}@${VPS_IP}:`"$destPath`""
    
    if ($LASTEXITCODE -eq 0) {
        $archivosSubidos++
        if ($archivosSubidos % 50 -eq 0) {
            Write-Host "  Subidos: $archivosSubidos / $totalArchivos archivos..." -ForegroundColor Gray
        }
    } else {
        $errores++
        Write-Host "  [ERROR] No se pudo subir: $relativePath" -ForegroundColor Red
    }
}

Write-Host ""
if ($errores -eq 0) {
    Write-Host "[OK] $archivosSubidos archivos subidos exitosamente" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] $archivosSubidos archivos subidos, $errores errores" -ForegroundColor Yellow
}

Write-Host ""

# Paso 2: Subir scripts necesarios
Write-Host "[2/4] Subiendo scripts de actualización..." -ForegroundColor Cyan
scp "$PROJECT_DIR\actualizar-vps.sh" ${VPS_USER}@${VPS_IP}:$VPS_DIR/actualizar-vps.sh
scp "$PROJECT_DIR\deploy-vps.sh" ${VPS_USER}@${VPS_IP}:/tmp/deploy-vps.sh

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Scripts subidos" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error al subir scripts" -ForegroundColor Red
}

Write-Host ""

# Paso 3: Configurar permisos
Write-Host "[3/4] Configurando permisos..." -ForegroundColor Cyan
ssh ${VPS_USER}@${VPS_IP} "chmod +x $VPS_DIR/actualizar-vps.sh && chmod +x /tmp/deploy-vps.sh && mkdir -p $VPS_DIR/storage/logs $VPS_DIR/storage/framework/cache $VPS_DIR/storage/framework/sessions $VPS_DIR/storage/framework/views $VPS_DIR/bootstrap/cache"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Permisos configurados" -ForegroundColor Green
} else {
    Write-Host "[ADVERTENCIA] No se pudieron configurar permisos automáticamente" -ForegroundColor Yellow
}

Write-Host ""

# Paso 4: Crear directorios necesarios en el servidor
Write-Host "[4/4] Creando directorios necesarios..." -ForegroundColor Cyan
ssh ${VPS_USER}@${VPS_IP} "cd $VPS_DIR && mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache public/storage"

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Directorios creados" -ForegroundColor Green
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Archivos subidos correctamente!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Resumen:" -ForegroundColor Yellow
Write-Host "  - Archivos subidos: $archivosSubidos" -ForegroundColor White
Write-Host "  - Tamaño aproximado: ~$([math]::Round($totalSize, 2)) MB" -ForegroundColor White
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Conéctate al VPS:" -ForegroundColor White
Write-Host "   ssh $VPS_USER@$VPS_IP" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Instala dependencias y actualiza:" -ForegroundColor White
Write-Host "   cd $VPS_DIR" -ForegroundColor Cyan
Write-Host "   bash actualizar-vps.sh" -ForegroundColor Cyan
Write-Host ""
Write-Host "   Esto instalará:" -ForegroundColor White
Write-Host "   - Dependencias de Composer (vendor/)" -ForegroundColor Gray
Write-Host "   - Dependencias de NPM (node_modules/)" -ForegroundColor Gray
Write-Host "   - Compilará los assets" -ForegroundColor Gray
Write-Host "   - Ejecutará migraciones" -ForegroundColor Gray
Write-Host "   - Configurará permisos" -ForegroundColor Gray
Write-Host ""

