# Resumen: Integración de Google Drive para MAFIT

## ✅ Lo que YA está hecho (configuración de Laravel):

1. **Paquete instalado**: `masbug/flysystem-google-drive-ext` (en proceso)
2. **Service Provider creado**: `GoogleDriveServiceProvider.php`
3. **Provider registrado**: En `config/app.php`
4. **Disco configurado**: En `config/filesystems.php`
5. **Controlador actualizado**: `InventarioController.php` ahora usa el disco configurado
6. **Método helper**: `generarUrlFoto()` para URLs compatibles con Google Drive
7. **Carpeta actualizada**: ID = `1nyAFo_ryS4lrYhhR9Gr0GRaJkE-V3O82`
8. **Email configurado**: `enrique.gilzara@gmail.com`

## 📋 Lo que FALTA hacer:

### 1. Obtener Credenciales (Client ID y Secret)

1.  Ve a la **[Google Cloud Console](https://console.cloud.google.com/)**.
2.  Crea un nuevo proyecto (ej. "MAFIT Inventarios").
3.  Habilita la **Google Drive API**.
4.  Configura la pantalla de consentimiento OAuth (Interna o Externa).
5.  Crea credenciales **OAuth client ID** (Aplicación web).
    *   **URI de redirección:** `https://developers.google.com/oauthplayground` (para pruebas iniciales) o la URL de tu app.
6.  Copia y guarda:
    *   **Client ID:** `TU_CLIENT_ID`
    *   **Client Secret:** `TU_CLIENT_SECRET`

### 2. Obtener Refresh Token (¡CRUCIAL!)

El `Access Token` expira cada hora. Necesitamos un **Refresh Token** para que el sistema funcione siempre.

1.  Ve al **[OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)**.
2.  En "Select & authorize APIs", busca `Drive API v3` y selecciona `https://www.googleapis.com/auth/drive.file`.
3.  Haz clic en el engrane (configuración) arriba a la derecha:
    *   Marca "Use your own OAuth credentials".
    *   Pega tu **Client ID** y **Client Secret**.
4.  Haz clic en "Authorize APIs".
5.  Inicia sesión y da permisos.
6.  Haz clic en "Exchange authorization code for tokens".
7.  Copia el **Refresh Token** (empieza con `1//...`).

### 3. Configurar el VPS

Una vez que tengas el Refresh Token, agregar al `.env` del VPS:

```env
GOOGLE_DRIVE_CLIENT_ID="TU_CLIENT_ID"
GOOGLE_DRIVE_CLIENT_SECRET="TU_CLIENT_SECRET"
GOOGLE_DRIVE_REFRESH_TOKEN="[EL TOKEN QUE OBTENGAS]"
GOOGLE_DRIVE_FOLDER_ID="1nyAFo_ryS4lrYhhR9Gr0GRaJkE-V3O82"
FILESYSTEM_DISK=google
```

### 3. Subir cambios al VPS

```bash
git add .
git commit -m "Integración con Google Drive para almacenamiento de fotos"
git push origin main

# En el VPS
cd /var/www/mafit
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan cache:clear
systemctl restart php7.4-fpm
```

## 🎯 Beneficios de usar Google Drive:

✅ **Espacio ilimitado** (comparado con el VPS)
✅ **Respaldo automático** en la nube de Google
✅ **Acceso directo** a las fotos desde Google Drive
✅ **Menor carga** en el servidor VPS
✅ **URLs públicas** generadas automáticamente

## 🔄 Cómo funciona:

1. Usuario sube foto en el inventario
2. Laravel guarda la foto en Google Drive (carpeta `1nyAFo_ryS4lrYhhR9Gr0GRaJkE-V3O82`)
3. Se genera una URL pública de Google Drive
4. La URL se guarda en la base de datos
5. Al consultar el inventario, se muestra la foto desde Google Drive

## 📝 Notas importantes:

- La carpeta de Google Drive debe estar compartida con `enrique.gilzara@gmail.com`
- El Refresh Token no expira (a menos que lo revoques manualmente)
- Puedes cambiar entre almacenamiento local y Google Drive cambiando `FILESYSTEM_DISK` en `.env`
- Las fotos existentes en el VPS NO se migran automáticamente (solo las nuevas irán a Drive)

## 🚨 Próximo paso INMEDIATO:

Ejecutar el script para obtener el Refresh Token:
```bash
php get-google-refresh-token.php
```
