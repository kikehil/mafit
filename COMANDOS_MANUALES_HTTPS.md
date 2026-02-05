# Comandos Manuales para Subir Corrección HTTPS

Si el script de PowerShell no funciona, puedes ejecutar estos comandos manualmente.

## Opción 1: Usar la ruta completa de SCP

En PowerShell, usa la ruta completa del ejecutable:

```powershell
# Usar la ruta completa para evitar alias de PowerShell
& "C:\Windows\System32\OpenSSH\scp.exe" app\Providers\AppServiceProvider.php root@147.93.118.121:/opt/mafit/app/Providers/
& "C:\Windows\System32\OpenSSH\scp.exe" app\Http\Middleware\ForceHttps.php root@147.93.118.121:/opt/mafit/app/Http/Middleware/
& "C:\Windows\System32\OpenSSH\scp.exe" resources\views\layouts\app.blade.php root@147.93.118.121:/opt/mafit/resources/views/layouts/
& "C:\Windows\System32\OpenSSH\scp.exe" corregir-urls-https.sh root@147.93.118.121:/opt/mafit/
```

## Opción 2: Usar el operador de llamada con scp.exe

```powershell
& scp.exe app\Providers\AppServiceProvider.php root@147.93.118.121:/opt/mafit/app/Providers/
& scp.exe app\Http\Middleware\ForceHttps.php root@147.93.118.121:/opt/mafit/app/Http/Middleware/
& scp.exe resources\views\layouts\app.blade.php root@147.93.118.121:/opt/mafit/resources/views/layouts/
& scp.exe corregir-urls-https.sh root@147.93.118.121:/opt/mafit/
```

## Opción 3: Usar WinSCP (Interfaz Gráfica)

1. Descarga WinSCP: https://winscp.net/
2. Conéctate a: `root@147.93.118.121`
3. Sube los archivos a las siguientes rutas:
   - `app\Providers\AppServiceProvider.php` → `/opt/mafit/app/Providers/`
   - `app\Http\Middleware\ForceHttps.php` → `/opt/mafit/app/Http/Middleware/`
   - `resources\views\layouts\app.blade.php` → `/opt/mafit/resources/views/layouts/`
   - `corregir-urls-https.sh` → `/opt/mafit/`

## Opción 4: Usar WSL (Windows Subsystem for Linux)

Si tienes WSL instalado:

```bash
# Desde WSL
scp app/Providers/AppServiceProvider.php root@147.93.118.121:/opt/mafit/app/Providers/
scp app/Http/Middleware/ForceHttps.php root@147.93.118.121:/opt/mafit/app/Http/Middleware/
scp resources/views/layouts/app.blade.php root@147.93.118.121:/opt/mafit/resources/views/layouts/
scp corregir-urls-https.sh root@147.93.118.121:/opt/mafit/
```

## Después de subir los archivos

Conéctate al servidor y ejecuta:

```bash
ssh root@147.93.118.121
cd /opt/mafit
chmod +x corregir-urls-https.sh
./corregir-urls-https.sh
```

O manualmente:

```bash
ssh root@147.93.118.121
cd /opt/mafit
sed -i 's|APP_URL=.*|APP_URL=https://mafit.regiontamaulipas.com.mx|g' .env
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
```

## Verificar que funciona

```bash
curl -I https://mafit.regiontamaulipas.com.mx
# Debe mostrar: Location: https://mafit.regiontamaulipas.com.mx/login
# (SIN :443)
```

