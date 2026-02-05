# Script PowerShell para subir la corrección del problema de URLs con :443
# Uso: .\subir-correccion-https.ps1

param(
    [Parameter(Mandatory=$false)]
    [string]$VPS_IP = "147.93.118.121",
    
    [Parameter(Mandatory=$false)]
    [string]$VPS_Usuario = "root",
    
    [Parameter(Mandatory=$false)]
    [string]$VPS_Ruta = "/opt/mafit"
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  SUBIR CORRECCIÓN HTTPS (SIN :443)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$PROJECT_DIR = $PSScriptRoot

Write-Host "Proyecto: $PROJECT_DIR" -ForegroundColor Yellow
Write-Host "Servidor: $VPS_Usuario@$VPS_IP" -ForegroundColor Yellow
Write-Host "Ruta destino: $VPS_Ruta" -ForegroundColor Yellow
Write-Host ""

# Verificar que los archivos existen
$archivos = @(
    @{Local = "app\Providers\AppServiceProvider.php"; Remote = "$VPS_Ruta/app/Providers/AppServiceProvider.php"},
    @{Local = "app\Http\Middleware\ForceHttps.php"; Remote = "$VPS_Ruta/app/Http/Middleware/ForceHttps.php"},
    @{Local = "resources\views\layouts\app.blade.php"; Remote = "$VPS_Ruta/resources/views/layouts/app.blade.php"},
    @{Local = "corregir-urls-https.sh"; Remote = "$VPS_Ruta/corregir-urls-https.sh"}
)

Write-Host "[1/4] Verificando archivos locales..." -ForegroundColor Green
$archivosFaltantes = @()
foreach ($archivo in $archivos) {
    $rutaLocal = Join-Path $PROJECT_DIR $archivo.Local
    if (Test-Path $rutaLocal) {
        Write-Host "  ✓ $($archivo.Local)" -ForegroundColor Green
    } else {
        Write-Host "  ✗ FALTA: $($archivo.Local)" -ForegroundColor Red
        $archivosFaltantes += $archivo.Local
    }
}

if ($archivosFaltantes.Count -gt 0) {
    Write-Host ""
    Write-Host "ERROR: Faltan los siguientes archivos:" -ForegroundColor Red
    $archivosFaltantes | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
    exit 1
}

Write-Host ""
Write-Host "[2/4] Verificando conexión SSH..." -ForegroundColor Green

# Encontrar la ruta de SSH y SCP
$sshPath = (Get-Command ssh -ErrorAction SilentlyContinue).Source
if (-not $sshPath) {
    $sshPath = "C:\Windows\System32\OpenSSH\ssh.exe"
}

$scpPath = (Get-Command scp -ErrorAction SilentlyContinue).Source
if (-not $scpPath) {
    $scpPath = "C:\Windows\System32\OpenSSH\scp.exe"
}

if (Test-Path $sshPath) {
    $testConnection = & $sshPath -o ConnectTimeout=5 -o BatchMode=yes "${VPS_Usuario}@${VPS_IP}" "echo 'OK'" 2>&1
} else {
    $testConnection = & ssh.exe -o ConnectTimeout=5 -o BatchMode=yes "${VPS_Usuario}@${VPS_IP}" "echo 'OK'" 2>&1
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "  ⚠️  No se pudo verificar la conexión automáticamente" -ForegroundColor Yellow
    Write-Host "  Se intentará conectar al subir los archivos..." -ForegroundColor Yellow
} else {
    Write-Host "  ✓ Conexión SSH verificada" -ForegroundColor Green
}

Write-Host ""
Write-Host "[3/4] Subiendo archivos al servidor..." -ForegroundColor Green
$errores = 0

foreach ($archivo in $archivos) {
    $rutaLocal = Join-Path $PROJECT_DIR $archivo.Local
    $rutaRemota = $archivo.Remote
    
    Write-Host "  Subiendo: $($archivo.Local)..." -ForegroundColor Yellow
    
    # Crear el directorio remoto si no existe
    $directorioRemoto = Split-Path -Parent $rutaRemota -ErrorAction SilentlyContinue
    if ($directorioRemoto) {
        if (Test-Path $sshPath) {
            & $sshPath "${VPS_Usuario}@${VPS_IP}" "mkdir -p `"$directorioRemoto`"" 2>&1 | Out-Null
        } else {
            & ssh.exe "${VPS_Usuario}@${VPS_IP}" "mkdir -p `"$directorioRemoto`"" 2>&1 | Out-Null
        }
    }
    
    # Subir el archivo (usar ruta completa para evitar alias de PowerShell)
    if (Test-Path $scpPath) {
        & $scpPath "`"$rutaLocal`"" "${VPS_Usuario}@${VPS_IP}:`"$rutaRemota`""
    } else {
        # Intentar con scp directamente (puede fallar si hay alias)
        & scp.exe "`"$rutaLocal`"" "${VPS_Usuario}@${VPS_IP}:`"$rutaRemota`""
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "    ✓ Subido correctamente" -ForegroundColor Green
    } else {
        Write-Host "    ✗ Error al subir" -ForegroundColor Red
        $errores++
    }
}

if ($errores -gt 0) {
    Write-Host ""
    Write-Host "ERROR: Hubo $errores error(es) al subir archivos" -ForegroundColor Red
    Write-Host "Verifica tu conexión SSH y permisos" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "[4/4] Configurando permisos y ejecutando script de corrección..." -ForegroundColor Green

# Dar permisos de ejecución al script
if (Test-Path $sshPath) {
    & $sshPath "${VPS_Usuario}@${VPS_IP}" "chmod +x `"$VPS_Ruta/corregir-urls-https.sh`"" 2>&1 | Out-Null
} else {
    & ssh.exe "${VPS_Usuario}@${VPS_IP}" "chmod +x `"$VPS_Ruta/corregir-urls-https.sh`"" 2>&1 | Out-Null
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ARCHIVOS SUBIDOS CORRECTAMENTE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos pasos en el servidor:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Conéctate al servidor:" -ForegroundColor White
Write-Host "   ssh $VPS_Usuario@$VPS_IP" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Ejecuta el script de corrección:" -ForegroundColor White
Write-Host "   cd $VPS_Ruta" -ForegroundColor Cyan
Write-Host "   ./corregir-urls-https.sh" -ForegroundColor Cyan
Write-Host ""
Write-Host "O ejecuta manualmente:" -ForegroundColor White
Write-Host "   cd $VPS_Ruta" -ForegroundColor Cyan
Write-Host "   sed -i 's|APP_URL=.*|APP_URL=https://mafit.regiontamaulipas.com.mx|g' .env" -ForegroundColor Cyan
Write-Host "   php artisan config:clear" -ForegroundColor Cyan
Write-Host "   php artisan route:clear" -ForegroundColor Cyan
Write-Host "   php artisan view:clear" -ForegroundColor Cyan
Write-Host "   php artisan cache:clear" -ForegroundColor Cyan
Write-Host "   php artisan config:cache" -ForegroundColor Cyan
Write-Host "   sudo systemctl restart php8.3-fpm" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Verifica que funciona:" -ForegroundColor White
Write-Host "   curl -I https://mafit.regiontamaulipas.com.mx" -ForegroundColor Cyan
Write-Host "   # Debe redirigir a /login SIN :443" -ForegroundColor Gray
Write-Host ""

