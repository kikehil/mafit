# Solución: Virtualización no detectada en Docker Desktop

## Problema
Docker Desktop muestra el error: "Virtualization support not detected"

## Soluciones (en orden de prioridad)

### Opción 1: Habilitar Virtualización en Windows (Recomendado)

1. **Abrir PowerShell como Administrador:**
   - Presiona `Win + X`
   - Selecciona "Windows PowerShell (Administrador)" o "Terminal (Administrador)"

2. **Habilitar características de Windows necesarias:**
   ```powershell
   # Habilitar Hyper-V (si está disponible en tu versión de Windows)
   Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Hyper-V -All
   
   # Habilitar Virtual Machine Platform
   Enable-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform -All
   
   # Habilitar Windows Subsystem for Linux
   Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux -All
   ```

3. **Reiniciar el equipo** (obligatorio después de habilitar estas características)

4. **Configurar WSL2 como versión predeterminada:**
   ```powershell
   wsl --set-default-version 2
   ```

5. **Reiniciar Docker Desktop**

### Opción 2: Habilitar Virtualización en el BIOS/UEFI

Si la Opción 1 no funciona, necesitas habilitar la virtualización en el BIOS:

1. **Reiniciar el equipo y entrar al BIOS/UEFI:**
   - Durante el arranque, presiona la tecla indicada (comúnmente: F2, F10, F12, Del, o Esc)
   - La tecla varía según el fabricante

2. **Buscar opciones de virtualización:**
   - Busca "Virtualization Technology" o "VT-x" (Intel)
   - O "AMD-V" (AMD)
   - O "SVM Mode" (algunos BIOS)
   - Puede estar en: Advanced > CPU Configuration, o Security, o System Configuration

3. **Habilitar la opción:**
   - Cambia de "Disabled" a "Enabled"
   - Guarda y sal del BIOS (generalmente F10)

4. **Reiniciar Windows**

5. **Verificar que está habilitado:**
   ```powershell
   systeminfo | Select-String "Hyper-V"
   ```
   Debe mostrar: "Extensiones de modo de monitor de VM: Sí"

### Opción 3: Usar Docker sin Docker Desktop (Alternativa)

Si no puedes habilitar la virtualización, puedes usar Docker directamente con WSL2:

1. **Instalar Docker Engine en WSL2:**
   ```powershell
   wsl
   ```
   Luego dentro de WSL:
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sh get-docker.sh
   sudo usermod -aG docker $USER
   ```

2. **O usar Docker Toolbox** (versión antigua que usa VirtualBox)

## Verificación

Después de aplicar cualquiera de las soluciones:

1. **Verificar WSL2:**
   ```powershell
   wsl --status
   ```
   Debe mostrar: "Versión predeterminada: 2"

2. **Verificar virtualización:**
   ```powershell
   systeminfo | Select-String "Hyper-V"
   ```

3. **Reiniciar Docker Desktop**

4. **Verificar Docker:**
   ```powershell
   docker ps
   ```

## Si nada funciona

Considera usar una alternativa sin Docker:
- Instalar PHP, MySQL y Nginx directamente en Windows
- Usar XAMPP o Laragon
- Usar una máquina virtual con Linux

## Continuar con la instalación

Una vez que Docker Desktop esté funcionando, continúa con:

```powershell
# 1. Verificar que Docker funciona
docker ps

# 2. Iniciar contenedores
docker compose up -d

# 3. Instalar dependencias PHP
docker compose exec app composer install

# 4. Generar clave
docker compose exec app php artisan key:generate

# 5. Migrar base de datos
docker compose exec app php artisan migrate --seed

# 6. Instalar y compilar assets
npm install
npm run build
```
















