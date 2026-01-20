# Solución Error 404 - Proyecto en C:\WEB\MAFIT

## Problema

El proyecto está en `C:\WEB\MAFIT` pero Apache por defecto busca en `C:\xampp\htdocs`. Por eso obtienes un error 404.

## Soluciones

### Solución 1: VirtualHost (Recomendado)

Esta es la mejor solución. Configura un VirtualHost para que Apache apunte directamente a tu proyecto.

#### Paso 1: Ejecutar script de configuración

Ejecuta `configurar_apache_xampp.bat` como **Administrador**.

#### Paso 2: Agregar entrada en hosts

Edita `C:\Windows\System32\drivers\etc\hosts` (como Administrador) y agrega:

```
127.0.0.1    mafit.local
```

#### Paso 3: Verificar mod_rewrite

Abre `C:\xampp\apache\conf\httpd.conf` y verifica que esta línea NO esté comentada:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Si está comentada (con `#`), descoméntala.

#### Paso 4: Verificar que httpd-vhosts.conf esté incluido

En el mismo `httpd.conf`, busca y descomenta:

```apache
Include conf/extra/httpd-vhosts.conf
```

#### Paso 5: Reiniciar Apache

En XAMPP Control Panel, detén y reinicia Apache.

#### Paso 6: Acceder a la aplicación

Abre tu navegador y accede a: **http://mafit.local**

---

### Solución 2: Alias en httpd.conf (Alternativa)

Si no quieres usar VirtualHost, puedes agregar un Alias.

#### Paso 1: Editar httpd.conf

Abre `C:\xampp\apache\conf\httpd.conf` y agrega al final:

```apache
# MAFIT Alias
Alias /mafit "C:/WEB/MAFIT/public"

<Directory "C:/WEB/MAFIT/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### Paso 2: Reiniciar Apache

En XAMPP Control Panel, reinicia Apache.

#### Paso 3: Acceder a la aplicación

Abre tu navegador y accede a: **http://localhost/mafit**

---

### Solución 3: Mover proyecto a htdocs (No recomendado)

Si prefieres usar la ubicación por defecto, puedes mover el proyecto:

```cmd
xcopy /E /I C:\WEB\MAFIT C:\xampp\htdocs\MAFIT
```

Luego accede a: **http://localhost/MAFIT/public**

**Nota:** Esta solución no es recomendada porque cambia la ubicación del proyecto.

---

## Verificación

Después de configurar, verifica:

1. ✅ Apache está corriendo en XAMPP
2. ✅ MySQL está corriendo en XAMPP
3. ✅ Puedes acceder a http://mafit.local (Solución 1) o http://localhost/mafit (Solución 2)
4. ✅ Ves la página de login de Laravel

## Si aún no funciona

1. Revisa los logs de Apache: `C:\xampp\apache\logs\error.log`
2. Verifica que `mod_rewrite` esté habilitado
3. Verifica que el archivo `public/.htaccess` exista
4. Ejecuta `diagnostico_completo.bat` para verificar la configuración












