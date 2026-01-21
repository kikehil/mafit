# Configuración de Apache para MAFIT

## Problema: Error 500

Si estás obteniendo un error 500, puede ser un problema de configuración de Apache.

## Solución 1: Usar el directorio public directamente

En lugar de acceder a `http://localhost/MAFIT/public/`, configura Apache para que apunte directamente al directorio `public`.

### Opción A: VirtualHost (Recomendado)

Edita `C:\xampp\apache\conf\extra\httpd-vhosts.conf` y agrega:

```apache
<VirtualHost *:80>
    ServerName mafit.local
    DocumentRoot "C:/WEB/MAFIT/public"
    
    <Directory "C:/WEB/MAFIT/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "C:/xampp/apache/logs/mafit_error.log"
    CustomLog "C:/xampp/apache/logs/mafit_access.log" common
</VirtualHost>
```

Luego edita `C:\Windows\System32\drivers\etc\hosts` (como administrador) y agrega:
```
127.0.0.1    mafit.local
```

Reinicia Apache y accede a: `http://mafit.local`

### Opción B: Alias en httpd.conf

Edita `C:\xampp\apache\conf\httpd.conf` y agrega al final:

```apache
Alias /mafit "C:/WEB/MAFIT/public"

<Directory "C:/WEB/MAFIT/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Reinicia Apache y accede a: `http://localhost/mafit`

## Solución 2: Verificar módulos de Apache

Asegúrate de que estos módulos estén habilitados en `httpd.conf`:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule php_module "C:/xampp/php85/php8apache2_4.dll"
```

## Solución 3: Verificar permisos

Asegúrate de que Apache tenga permisos para leer y escribir en:
- `storage/`
- `bootstrap/cache/`

## Solución 4: Ver logs de Apache

Revisa los logs de Apache en:
- `C:\xampp\apache\logs\error.log`

## Solución 5: Probar PHP directamente

Crea un archivo `test.php` en `public/`:

```php
<?php
phpinfo();
```

Accede a `http://localhost/MAFIT/public/test.php` para verificar que PHP funciona.

## Solución 6: Verificar .htaccess

El archivo `public/.htaccess` debe existir y tener el contenido correcto para Laravel.






