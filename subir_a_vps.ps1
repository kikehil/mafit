# Script PowerShell para preparar y subir archivos al VPS
# Uso: .\subir_a_vps.ps1

param(
    [Parameter(Mandatory=$false)]
    [string]$VPS_Usuario = "",
    
    [Parameter(Mandatory=$false)]
    [string]$VPS_IP = "",
    
    [Parameter(Mandatory=$false)]
    [string]$VPS_Ruta = "/var/www/mafit",
    
    [Parameter(Mandatory=$false)]
    [switch]$SoloPreparar = $false
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Script de Despliegue a VPS - MAFIT" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Obtener la ruta del proyecto
$ProyectoPath = $PSScriptRoot
$ProyectoNombre = Split-Path -Leaf $ProyectoPath
$ZipPath = Join-Path (Split-Path -Parent $ProyectoPath) "mafit_produccion.zip"

Write-Host "Proyecto: $ProyectoPath" -ForegroundColor Yellow
Write-Host ""

# Funcion para crear ZIP excluyendo archivos innecesarios
function Crear-ZipProduccion {
    Write-Host "Preparando archivos para produccion..." -ForegroundColor Green
    
    # Lista de archivos y carpetas a excluir
    $Excluir = @(
        "node_modules",
        "vendor",
        ".git",
        "public/build",
        "public/hot",
        "public/storage",
        ".env",
        ".env.backup",
        ".env.production",
        ".phpunit.result.cache",
        "Homestead.json",
        "Homestead.yaml",
        "auth.json",
        "npm-debug.log",
        "yarn-error.log",
        ".fleet",
        ".idea",
        ".vscode",
        "*.bat",
        "mafit_backup.sql",
        "query",
        "tmp_check_headers.php",
        "DB_HOST",
        "DB_PASSWORD",
        "DB_USERNAME"
    )
    
    # Crear archivo temporal con lista de exclusiones
    $ExcluirArchivo = Join-Path $env:TEMP "excluir_mafit.txt"
    $Excluir | ForEach-Object { Add-Content -Path $ExcluirArchivo -Value $_ }
    
    # Si existe un ZIP anterior, eliminarlo
    if (Test-Path $ZipPath) {
        Remove-Item $ZipPath -Force
        Write-Host "Archivo ZIP anterior eliminado." -ForegroundColor Yellow
    }
    
    Write-Host "Comprimiendo archivos (esto puede tardar unos minutos)..." -ForegroundColor Yellow
    
    # Crear ZIP usando 7-Zip si esta disponible, sino usar Compress-Archive
    $Usar7Zip = $false
    if (Get-Command 7z -ErrorAction SilentlyContinue) {
        $Usar7Zip = $true
        Write-Host "Usando 7-Zip para compresion optimizada..." -ForegroundColor Green
        
        # Construir comando 7z
        $ExcluirParams = $Excluir | ForEach-Object { "-xr!$_" }
        $7zArgs = @("a", "-tzip", "-mx=5", $ZipPath) + $ExcluirParams + @(".")
        
        Push-Location $ProyectoPath
        & 7z $7zArgs | Out-Null
        Pop-Location
        
        if (Test-Path $ZipPath) {
            Write-Host "ZIP creado exitosamente con 7-Zip" -ForegroundColor Green
        } else {
            $Usar7Zip = $false
        }
    }
    
    if (-not $Usar7Zip) {
        # Metodo alternativo con Compress-Archive (mas lento pero funciona)
        Write-Host "Usando Compress-Archive (metodo estandar)..." -ForegroundColor Yellow
        
        # Obtener todos los archivos excepto los excluidos
        $Archivos = Get-ChildItem -Path $ProyectoPath -Recurse | Where-Object {
            $RutaRelativa = $_.FullName.Substring($ProyectoPath.Length + 1)
            $ExcluirTodo = $false
            
            foreach ($Patron in $Excluir) {
                if ($Patron -like "*.*") {
                    # Es un patron de archivo (ej: *.bat)
                    $Extension = $Patron.Substring(1)
                    if ($_.Extension -eq $Extension) {
                        $ExcluirTodo = $true
                        break
                    }
                } else {
                    # Es una carpeta o archivo especifico
                    if ($RutaRelativa -like "$Patron*" -or $RutaRelativa -eq $Patron) {
                        $ExcluirTodo = $true
                        break
                    }
                }
            }
            
            return -not $ExcluirTodo
        }
        
        # Crear ZIP
        $Archivos | Compress-Archive -DestinationPath $ZipPath -Force
        
        Write-Host "ZIP creado exitosamente" -ForegroundColor Green
    }
    
    # Mostrar tamano del archivo
    $TamanoMB = (Get-Item $ZipPath).Length / 1MB
    $TamanoRedondeado = [math]::Round($TamanoMB, 2)
    Write-Host "Tamano del archivo: $TamanoRedondeado MB" -ForegroundColor Cyan
    
    # Limpiar archivo temporal
    if (Test-Path $ExcluirArchivo) {
        Remove-Item $ExcluirArchivo -Force
    }
    
    return $ZipPath
}

# Funcion para subir archivos via SCP
function Subir-ViaSCP {
    param(
        [string]$ArchivoZip,
        [string]$Usuario,
        [string]$IP,
        [string]$RutaDestino
    )
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "  Subiendo archivos al VPS..." -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    
    # Verificar que SCP este disponible
    if (-not (Get-Command scp -ErrorAction SilentlyContinue)) {
        Write-Host "ERROR: SCP no esta disponible." -ForegroundColor Red
        Write-Host "Opciones:" -ForegroundColor Yellow
        Write-Host "1. Instalar OpenSSH Client en Windows:" -ForegroundColor Yellow
        Write-Host "   Configuracion > Aplicaciones > Caracteristicas opcionales > Agregar OpenSSH Client" -ForegroundColor Yellow
        Write-Host "2. Usar WinSCP (interfaz grafica): https://winscp.net/" -ForegroundColor Yellow
        Write-Host "3. Usar el archivo ZIP manualmente: $ArchivoZip" -ForegroundColor Yellow
        return $false
    }
    
    # Crear directorio temporal en el servidor
    $RutaTemp = "/tmp/mafit_upload"
    
    Write-Host "Subiendo archivo ZIP al servidor..." -ForegroundColor Yellow
    $DestinoCompleto = "${Usuario}@${IP}:${RutaTemp}.zip"
    Write-Host "Destino: $DestinoCompleto" -ForegroundColor Cyan
    
    # Subir ZIP
    scp $ArchivoZip $DestinoCompleto
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Archivo subido exitosamente" -ForegroundColor Green
        
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Cyan
        Write-Host "  Proximos pasos en el servidor:" -ForegroundColor Cyan
        Write-Host "========================================" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "1. Conectate al servidor:" -ForegroundColor Yellow
        $ComandoSSH = "   ssh ${Usuario}@${IP}"
        Write-Host $ComandoSSH -ForegroundColor White
        Write-Host ""
        Write-Host "2. Ejecuta estos comandos:" -ForegroundColor Yellow
        Write-Host "   cd /tmp" -ForegroundColor White
        Write-Host "   unzip -q mafit_upload.zip -d mafit_temp" -ForegroundColor White
        Write-Host "   sudo rm -rf $RutaDestino" -ForegroundColor White
        $ComandoMV = "   sudo mv mafit_temp/$ProyectoNombre $RutaDestino"
        Write-Host $ComandoMV -ForegroundColor White
        Write-Host "   sudo chown -R www-data:www-data $RutaDestino" -ForegroundColor White
        Write-Host "   cd $RutaDestino" -ForegroundColor White
        Write-Host "   bash instalar_en_servidor.sh" -ForegroundColor White
        Write-Host ""
        
        return $true
    } else {
        Write-Host "ERROR: Fallo al subir archivo" -ForegroundColor Red
        Write-Host "Verifica tus credenciales y conexion SSH" -ForegroundColor Yellow
        return $false
    }
}

# Funcion para subir usando rsync (mas eficiente para actualizaciones)
function Subir-ViaRsync {
    param(
        [string]$Usuario,
        [string]$IP,
        [string]$RutaDestino
    )
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "  Subiendo archivos via Rsync..." -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    
    # Verificar que rsync este disponible
    if (-not (Get-Command rsync -ErrorAction SilentlyContinue)) {
        Write-Host "ERROR: Rsync no esta disponible en Windows." -ForegroundColor Red
        Write-Host "Puedes usar WSL (Windows Subsystem for Linux) o usar el metodo ZIP." -ForegroundColor Yellow
        return $false
    }
    
    Write-Host "Sincronizando archivos (esto puede tardar)..." -ForegroundColor Yellow
    
    # Excluir archivos usando --exclude
    $ExcluirParams = @(
        "--exclude=node_modules",
        "--exclude=vendor",
        "--exclude=.git",
        "--exclude=public/build",
        "--exclude=public/hot",
        "--exclude=.env",
        "--exclude=*.bat",
        "--exclude=mafit_backup.sql"
    )
    
    $DestinoRsync = "${Usuario}@${IP}:${RutaDestino}/"
    $OrigenRsync = "${ProyectoPath}/"
    
    rsync -avz --progress $ExcluirParams $OrigenRsync $DestinoRsync
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Archivos sincronizados exitosamente" -ForegroundColor Green
        return $true
    } else {
        Write-Host "ERROR: Fallo en la sincronizacion" -ForegroundColor Red
        return $false
    }
}

# ============================================
# EJECUCION PRINCIPAL
# ============================================

# Crear ZIP
$ZipCreado = Crear-ZipProduccion

if (-not $ZipCreado) {
    Write-Host "ERROR: No se pudo crear el archivo ZIP" -ForegroundColor Red
    exit 1
}

if ($SoloPreparar) {
    Write-Host ""
    Write-Host "Archivo ZIP preparado: $ZipPath" -ForegroundColor Green
    Write-Host "Puedes subirlo manualmente usando WinSCP o SCP" -ForegroundColor Yellow
    exit 0
}

# Si se proporcionaron credenciales, subir automaticamente
if ($VPS_Usuario -and $VPS_IP) {
    Write-Host ""
    $Pregunta = "Como quieres subir? (1=SCP/ZIP, 2=Rsync, 3=Solo preparar ZIP) [1]"
    $Metodo = Read-Host $Pregunta
    
    if ([string]::IsNullOrWhiteSpace($Metodo)) {
        $Metodo = "1"
    }
    
    switch ($Metodo) {
        "1" {
            Subir-ViaSCP -ArchivoZip $ZipCreado -Usuario $VPS_Usuario -IP $VPS_IP -RutaDestino $VPS_Ruta
        }
        "2" {
            Subir-ViaRsync -Usuario $VPS_Usuario -IP $VPS_IP -RutaDestino $VPS_Ruta
        }
        "3" {
            Write-Host ""
            Write-Host "Archivo ZIP preparado: $ZipPath" -ForegroundColor Green
            Write-Host "Puedes subirlo manualmente usando WinSCP o SCP" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host ""
    Write-Host "Archivo ZIP preparado: $ZipPath" -ForegroundColor Green
    Write-Host ""
    Write-Host "Para subir automaticamente, ejecuta:" -ForegroundColor Yellow
    $ComandoEjemplo = "  .\subir_a_vps.ps1 -VPS_Usuario 'tu_usuario' -VPS_IP 'tu_ip' -VPS_Ruta '/var/www/mafit'"
    Write-Host $ComandoEjemplo -ForegroundColor White
    Write-Host ""
    Write-Host "O sube manualmente el archivo ZIP usando WinSCP o SCP" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Proceso completado" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
