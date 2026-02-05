# Solución: Error 400 Bad Request - Puerto :443 en URLs

## Problema

El navegador está intentando acceder a `mafit.regiontamaulipas.com.mx:443/login` pero el servidor devuelve un error 400 Bad Request que dice:

> "You're speaking plain HTTP to an SSL-enabled server port. Instead use the HTTPS scheme to access this URL, please."

Esto ocurre porque Laravel está generando URLs con el puerto `:443` incluido, y el navegador intenta hacer una petición HTTP (no cifrada) al puerto 443, que está configurado para SSL.

## Causa

1. Laravel detecta que el servidor está en el puerto 443
2. Laravel incluye el puerto `:443` en las URLs generadas (ej: `https://mafit.regiontamaulipas.com.mx:443/login`)
3. El navegador interpreta esto como una URL HTTP con puerto 443, no como HTTPS
4. El servidor rechaza la petición porque está recibiendo HTTP en un puerto SSL

## Solución

Se han modificado tres archivos para corregir este problema:

### 1. `app/Providers/AppServiceProvider.php`
- Fuerza el esquema HTTPS
- Configura la URL base sin puerto
- Remueve el puerto `:443` de APP_URL

### 2. `app/Http/Middleware/ForceHttps.php`
- Detecta HTTPS correctamente
- Fuerza el esquema y URL base sin puerto
- Reemplaza URLs HTTP por HTTPS en el contenido HTML

### 3. `resources/views/layouts/app.blade.php`
- Aplica la misma lógica antes de que Vite genere las URLs
- Asegura que las URLs de assets también usen HTTPS sin puerto

## Pasos para Aplicar la Solución

### En el Servidor VPS

1. **Subir los archivos modificados al servidor:**

**Opción A: Usar el script de PowerShell (Recomendado para Windows):**

```powershell
# Desde PowerShell en Windows
.\subir-correccion-https.ps1
```

O con parámetros personalizados:

```powershell
.\subir-correccion-https.ps1 -VPS_IP "147.93.118.121" -VPS_Usuario "root" -VPS_Ruta "/opt/mafit"
```

**Opción B: Usar SCP manualmente (con la IP del servidor):**

```bash
# Desde tu máquina local (usa la IP, no el hostname)
scp app/Providers/AppServiceProvider.php root@147.93.118.121:/opt/mafit/app/Providers/
scp app/Http/Middleware/ForceHttps.php root@147.93.118.121:/opt/mafit/app/Http/Middleware/
scp resources/views/layouts/app.blade.php root@147.93.118.121:/opt/mafit/resources/views/layouts/
scp corregir-urls-https.sh root@147.93.118.121:/opt/mafit/
```

**Nota:** Si `srv786045` no se resuelve, usa la IP del servidor directamente: `147.93.118.121`

2. **Ejecutar el script de corrección:**

```bash
# En el servidor
cd /opt/mafit
chmod +x corregir-urls-https.sh
./corregir-urls-https.sh
```

O manualmente:

```bash
cd /opt/mafit

# Verificar y corregir APP_URL en .env
sed -i 's|APP_URL=.*|APP_URL=https://mafit.regiontamaulipas.com.mx|g' .env

# Limpiar caché
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerar caché
php artisan config:cache

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
```

3. **Verificar la configuración de nginx:**

Asegúrate de que nginx esté configurado para pasar los headers necesarios. El archivo de configuración debe incluir:

```nginx
server {
    listen 443 ssl http2;
    server_name mafit.regiontamaulipas.com.mx;
    
    # ... configuración SSL ...
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        
        # Pasar headers para que Laravel detecte HTTPS
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header Host $host;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Headers para detectar HTTPS
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_X_FORWARDED_PROTO https;
    }
}
```

4. **Verificar que funciona:**

```bash
# Probar con curl
curl -I https://mafit.regiontamaulipas.com.mx

# Debe redirigir a /login sin :443
# Location: https://mafit.regiontamaulipas.com.mx/login
```

## Verificación

Después de aplicar los cambios, verifica:

1. **APP_URL en .env:**
```bash
grep APP_URL /opt/mafit/.env
# Debe ser: APP_URL=https://mafit.regiontamaulipas.com.mx
# NO debe ser: APP_URL=https://mafit.regiontamaulipas.com.mx:443
```

2. **URLs generadas por Laravel:**
```bash
cd /opt/mafit
php artisan tinker
>>> route('login')
# Debe mostrar: https://mafit.regiontamaulipas.com.mx/login
# NO debe mostrar: https://mafit.regiontamaulipas.com.mx:443/login
```

3. **En el navegador:**
- Accede a `https://mafit.regiontamaulipas.com.mx`
- Debe redirigir a `https://mafit.regiontamaulipas.com.mx/login` (sin :443)
- No debe mostrar el error 400 Bad Request

## Problemas Comunes

### El problema persiste después de aplicar los cambios

1. **Limpiar caché del navegador:**
   - Presiona Ctrl+Shift+Delete
   - Limpia caché y cookies

2. **Verificar que PHP-FPM se reinició:**
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl status php8.3-fpm
```

3. **Verificar logs de Laravel:**
```bash
tail -f /opt/mafit/storage/logs/laravel.log
```

4. **Verificar logs de nginx:**
```bash
sudo tail -f /var/log/nginx/error.log
```

### Las URLs aún incluyen :443

1. Verifica que los archivos se subieron correctamente
2. Asegúrate de que el caché se limpió completamente
3. Verifica que APP_URL en .env no tiene :443
4. Reinicia PHP-FPM y nginx

## Notas Adicionales

- El puerto 443 es el puerto por defecto para HTTPS, por lo que no debe aparecer en las URLs
- Laravel usa `URL::forceRootUrl()` para establecer la URL base sin puerto
- El middleware `ForceHttps` se ejecuta antes de generar las respuestas para asegurar que todas las URLs usen HTTPS

