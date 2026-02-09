# Configuración de Google Drive para MAFIT

## 📋 Pasos para configurar Google Drive

### 1. Crear un Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Nombre sugerido: "MAFIT Storage"

### 2. Habilitar Google Drive API

1. En el menú lateral, ve a **APIs & Services** > **Library**
2. Busca "Google Drive API"
3. Haz clic en **Enable**

### 3. Crear credenciales OAuth 2.0

1. Ve a **APIs & Services** > **Credentials**
2. Haz clic en **Create Credentials** > **OAuth client ID**
3. Tipo de aplicación: **Web application**
4. Nombre: `MAFIT Web Client`
5. URIs de redireccionamiento autorizados:
   - `http://localhost`
   - `https://tudominio.com` (tu dominio del VPS)
6. Haz clic en **Create**
7. **GUARDA** el Client ID y Client Secret que aparecen

### 4. Obtener Refresh Token

1. Abre el archivo `get-google-refresh-token.php`
2. Reemplaza `TU_CLIENT_ID_AQUI` con tu Client ID
3. Reemplaza `TU_CLIENT_SECRET_AQUI` con tu Client Secret
4. Ejecuta en terminal:
   ```bash
   php get-google-refresh-token.php
   ```
5. Sigue las instrucciones en pantalla
6. Autoriza la aplicación en tu navegador
7. Copia el código de autorización y pégalo en la terminal
8. **GUARDA** el Refresh Token que se genera

### 5. Compartir la carpeta de Google Drive

1. Ve a tu carpeta de Google Drive: https://drive.google.com/drive/folders/1nyAFo_ryS4lrYhhR9Gr0GRaJkE-V3O82
2. Haz clic derecho en la carpeta > **Compartir**
3. Agrega tu email de Google: **enrique.gilzara@gmail.com** (el que usaste para crear las credenciales)
4. Dale permisos de **Editor**
5. Haz clic en **Enviar**

### 6. Configurar Laravel

Agrega las siguientes variables al archivo `.env`:

```env
GOOGLE_DRIVE_CLIENT_ID="tu-client-id.apps.googleusercontent.com"
GOOGLE_DRIVE_CLIENT_SECRET="tu-client-secret"
GOOGLE_DRIVE_REFRESH_TOKEN="1//tu-refresh-token-muy-largo"
GOOGLE_DRIVE_FOLDER_ID="1nyAFo_ryS4lrYhhR9Gr0GRaJkE-V3O82"

# Cambiar el disco de almacenamiento predeterminado a Google Drive
FILESYSTEM_DISK=google
```

### 7. Reiniciar servicios

```bash
# En el VPS
cd /var/www/mafit
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan cache:clear
systemctl restart php7.4-fpm
```

## 🎯 Beneficios

✅ **Espacio ilimitado**: Google Drive ofrece mucho más espacio que el VPS
✅ **Respaldo automático**: Las fotos están respaldadas en la nube de Google
✅ **Acceso directo**: Puedes ver todas las fotos desde Google Drive
✅ **Menor carga en VPS**: El servidor no almacena las imágenes

## 📝 Notas importantes

- El Service Account debe tener acceso a la carpeta compartida
- La carpeta ID es: `12fFkkpdU16ha2t6Dannb_GvN2D79XI-_`
- Las fotos se subirán automáticamente a esta carpeta
- Puedes organizar las fotos en subcarpetas dentro de Drive

## 🔒 Seguridad

- **NUNCA** subas el archivo JSON a Git
- Agrega `storage/app/google/*.json` al `.gitignore`
- Guarda el archivo JSON en un lugar seguro (como un gestor de contraseñas)
