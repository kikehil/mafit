#!/usr/bin/env bash
# Script para solucionar error 403 Forbidden en Apache VPS
# Ejecutar en el VPS: bash solucionar-403-apache-vps.sh

set -e

APP_DIR="/var/www/html/mafit"
WEB_USER="www-data"
DOMAIN="mafit.regiontamaulipas.com.mx"

echo "=========================================="
echo "  Solucionar Error 403 Forbidden"
echo "=========================================="
echo ""
echo "Directorio: $APP_DIR"
echo "Usuario web: $WEB_USER"
echo "Dominio: $DOMAIN"
echo ""

# Verificar que el directorio existe
if [ ! -d "$APP_DIR" ]; then
    echo "[ERROR] El directorio $APP_DIR no existe"
    exit 1
fi

cd "$APP_DIR"

# Paso 1: Verificar permisos actuales
echo "[1/6] Verificando permisos actuales..."
echo "  Permisos del directorio raiz:"
ls -la "$APP_DIR" | head -n 5
echo ""

# Paso 2: Corregir permisos de archivos y directorios
echo "[2/6] Corrigiendo permisos de archivos y directorios..."
echo "  Estableciendo permisos correctos..."

# Permisos para archivos (644 = rw-r--r--)
find "$APP_DIR" -type f -exec chmod 644 {} \;

# Permisos para directorios (755 = rwxr-xr-x)
find "$APP_DIR" -type d -exec chmod 755 {} \;

# Permisos especiales para storage y bootstrap/cache
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

echo "[OK] Permisos de archivos corregidos"
echo ""

# Paso 3: Corregir propietario
echo "[3/6] Corrigiendo propietario de archivos..."
chown -R $WEB_USER:$WEB_USER "$APP_DIR"
echo "[OK] Propietario corregido a $WEB_USER:$WEB_USER"
echo ""

# Paso 4: Verificar que el directorio public existe
echo "[4/6] Verificando directorio public..."
if [ ! -d "$APP_DIR/public" ]; then
    echo "[ERROR] El directorio public no existe en $APP_DIR"
    exit 1
fi

if [ ! -f "$APP_DIR/public/index.php" ]; then
    echo "[ERROR] El archivo public/index.php no existe"
    exit 1
fi

echo "[OK] Directorio public existe y tiene index.php"
echo ""

# Paso 5: Verificar configuración de Apache
echo "[5/6] Verificando configuración de Apache..."
APACHE_SITES="/etc/apache2/sites-available"
APACHE_ENABLED="/etc/apache2/sites-enabled"

if [ -d "$APACHE_SITES" ]; then
    echo "  Buscando configuración de sitio para $DOMAIN..."
    
    # Buscar archivo de configuración
    SITE_CONFIG=""
    if [ -f "$APACHE_SITES/$DOMAIN.conf" ]; then
        SITE_CONFIG="$APACHE_SITES/$DOMAIN.conf"
    elif [ -f "$APACHE_SITES/mafit.conf" ]; then
        SITE_CONFIG="$APACHE_SITES/mafit.conf"
    elif [ -f "$APACHE_SITES/000-default.conf" ]; then
        SITE_CONFIG="$APACHE_SITES/000-default.conf"
    fi
    
    if [ -n "$SITE_CONFIG" ]; then
        echo "  Archivo de configuración encontrado: $SITE_CONFIG"
        echo "  Verificando DocumentRoot..."
        
        DOCUMENT_ROOT=$(grep -i "DocumentRoot" "$SITE_CONFIG" | head -n 1 | awk '{print $2}' | tr -d '"')
        
        if [ -n "$DOCUMENT_ROOT" ]; then
            echo "  DocumentRoot actual: $DOCUMENT_ROOT"
            
            if [ "$DOCUMENT_ROOT" != "$APP_DIR/public" ] && [ "$DOCUMENT_ROOT" != "${APP_DIR}/public" ]; then
                echo "[ADVERTENCIA] DocumentRoot no apunta a $APP_DIR/public"
                echo "  Debería ser: $APP_DIR/public"
                echo ""
                echo "  ¿Deseas crear/actualizar la configuración de Apache? (S/N)"
                read -r respuesta
                
                if [ "$respuesta" = "S" ] || [ "$respuesta" = "s" ]; then
                    echo "  Creando configuración de Apache..."
                    
                    cat > "$APACHE_SITES/mafit.conf" <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $APP_DIR/public
    
    <Directory $APP_DIR/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/mafit_error.log
    CustomLog \${APACHE_LOG_DIR}/mafit_access.log combined
</VirtualHost>
EOF
                    
                    # Habilitar sitio
                    a2ensite mafit.conf 2>/dev/null || true
                    
                    # Deshabilitar sitio por defecto si existe
                    a2dissite 000-default.conf 2>/dev/null || true
                    
                    echo "[OK] Configuración de Apache creada"
                    echo "  Reinicia Apache con: systemctl restart apache2"
                fi
            else
                echo "[OK] DocumentRoot está correcto"
            fi
        else
            echo "[ADVERTENCIA] No se pudo encontrar DocumentRoot en la configuración"
        fi
        
        # Verificar AllowOverride
        if grep -q "AllowOverride All" "$SITE_CONFIG"; then
            echo "[OK] AllowOverride All está configurado"
        else
            echo "[ADVERTENCIA] AllowOverride All no está configurado"
            echo "  Necesitas agregar 'AllowOverride All' en la sección <Directory>"
        fi
    else
        echo "[ADVERTENCIA] No se encontró archivo de configuración de Apache"
        echo "  Creando configuración básica..."
        
        cat > "$APACHE_SITES/mafit.conf" <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $APP_DIR/public
    
    <Directory $APP_DIR/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/mafit_error.log
    CustomLog \${APACHE_LOG_DIR}/mafit_access.log combined
</VirtualHost>
EOF
        
        a2ensite mafit.conf 2>/dev/null || true
        a2dissite 000-default.conf 2>/dev/null || true
        
        echo "[OK] Configuración creada. Reinicia Apache: systemctl restart apache2"
    fi
else
    echo "[ADVERTENCIA] No se encontró directorio de configuración de Apache"
    echo "  Puede que Apache no esté instalado o use otra ubicación"
fi

echo ""

# Paso 6: Verificar módulos de Apache
echo "[6/6] Verificando módulos de Apache necesarios..."
REQUIRED_MODULES=("rewrite" "php")

for module in "${REQUIRED_MODULES[@]}"; do
    if a2enmod "$module" 2>/dev/null; then
        echo "[OK] Módulo $module habilitado"
    else
        echo "[ADVERTENCIA] No se pudo habilitar módulo $module"
    fi
done

echo ""
echo "=========================================="
echo "  Resumen"
echo "=========================================="
echo ""
echo "Acciones completadas:"
echo "  ✓ Permisos de archivos corregidos"
echo "  ✓ Propietario establecido a $WEB_USER"
echo "  ✓ Verificada configuración de Apache"
echo ""
echo "Próximos pasos:"
echo "1. Reiniciar Apache:"
echo "   sudo systemctl restart apache2"
echo ""
echo "2. Verificar logs si persiste el error:"
echo "   sudo tail -f /var/log/apache2/error.log"
echo "   sudo tail -f /var/log/apache2/mafit_error.log"
echo ""
echo "3. Verificar que el sitio esté habilitado:"
echo "   sudo a2ensite mafit.conf"
echo "   sudo systemctl reload apache2"
echo ""






