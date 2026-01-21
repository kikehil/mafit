#!/bin/bash
# Script Bash para preparar y subir archivos al VPS
# Uso: bash subir_a_vps.sh

set -e  # Salir si hay algún error

# Colores
CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Banner
echo ""
echo "========================================"
echo -e "${CYAN}  Script de Despliegue a VPS - MAFIT${NC}"
echo "========================================"
echo ""

# Obtener la ruta del proyecto
PROYECTO_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROYECTO_NOMBRE="$(basename "$PROYECTO_PATH")"
ZIP_PATH="$(dirname "$PROYECTO_PATH")/mafit_produccion.zip"

echo -e "${YELLOW}Proyecto: $PROYECTO_PATH${NC}"
echo ""

# Función para crear ZIP
crear_zip() {
    echo -e "${GREEN}Preparando archivos para produccion...${NC}"
    
    # Si existe un ZIP anterior, eliminarlo
    if [ -f "$ZIP_PATH" ]; then
        rm -f "$ZIP_PATH"
        echo -e "${YELLOW}Archivo ZIP anterior eliminado.${NC}"
    fi
    
    echo -e "${YELLOW}Comprimiendo archivos (esto puede tardar unos minutos)...${NC}"
    
    # Crear ZIP excluyendo archivos innecesarios
    # Usar zip si está disponible, sino usar tar
    if command -v zip &> /dev/null; then
        echo -e "${GREEN}Usando zip para compresion...${NC}"
        
        # Cambiar al directorio del proyecto
        cd "$PROYECTO_PATH"
        
        # Crear ZIP excluyendo carpetas y archivos específicos
        zip -r "$ZIP_PATH" . \
            -x "node_modules/*" \
            -x "vendor/*" \
            -x ".git/*" \
            -x "public/build/*" \
            -x "public/hot/*" \
            -x "public/storage/*" \
            -x ".env" \
            -x ".env.backup" \
            -x ".env.production" \
            -x "*.bat" \
            -x "mafit_backup.sql" \
            -x "query" \
            -x "tmp_check_headers.php" \
            -x "DB_HOST" \
            -x "DB_PASSWORD" \
            -x "DB_USERNAME" \
            -x ".phpunit.result.cache" \
            -x "Homestead.json" \
            -x "Homestead.yaml" \
            -x "auth.json" \
            -x "npm-debug.log" \
            -x "yarn-error.log" \
            -x ".fleet/*" \
            -x ".idea/*" \
            -x ".vscode/*" \
            -x "*.zip" \
            > /dev/null 2>&1
        
        cd - > /dev/null
        
        if [ -f "$ZIP_PATH" ]; then
            echo -e "${GREEN}ZIP creado exitosamente${NC}"
        else
            echo -e "${RED}ERROR: No se pudo crear el archivo ZIP${NC}"
            return 1
        fi
    elif command -v tar &> /dev/null; then
        echo -e "${GREEN}Usando tar para compresion...${NC}"
        
        cd "$PROYECTO_PATH"
        
        tar -czf "$ZIP_PATH" \
            --exclude="node_modules" \
            --exclude="vendor" \
            --exclude=".git" \
            --exclude="public/build" \
            --exclude="public/hot" \
            --exclude="public/storage" \
            --exclude=".env" \
            --exclude=".env.backup" \
            --exclude=".env.production" \
            --exclude="*.bat" \
            --exclude="mafit_backup.sql" \
            --exclude="query" \
            --exclude="tmp_check_headers.php" \
            --exclude="DB_HOST" \
            --exclude="DB_PASSWORD" \
            --exclude="DB_USERNAME" \
            --exclude=".phpunit.result.cache" \
            --exclude="Homestead.json" \
            --exclude="Homestead.yaml" \
            --exclude="auth.json" \
            --exclude="npm-debug.log" \
            --exclude="yarn-error.log" \
            --exclude=".fleet" \
            --exclude=".idea" \
            --exclude=".vscode" \
            --exclude="*.zip" \
            . > /dev/null 2>&1
        
        cd - > /dev/null
        
        if [ -f "$ZIP_PATH" ]; then
            echo -e "${GREEN}Archivo comprimido exitosamente (tar.gz)${NC}"
            # Renombrar si es necesario
            if [[ "$ZIP_PATH" != *.tar.gz ]]; then
                mv "$ZIP_PATH" "${ZIP_PATH%.zip}.tar.gz"
                ZIP_PATH="${ZIP_PATH%.zip}.tar.gz"
            fi
        else
            echo -e "${RED}ERROR: No se pudo crear el archivo comprimido${NC}"
            return 1
        fi
    else
        echo -e "${RED}ERROR: No se encontró zip ni tar. Instala uno de ellos.${NC}"
        echo "En Windows con Git Bash, zip debería estar disponible."
        return 1
    fi
    
    # Mostrar tamaño del archivo
    if [ -f "$ZIP_PATH" ]; then
        if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" ]]; then
            # Windows/Git Bash
            TAMANO_BYTES=$(stat -c%s "$ZIP_PATH" 2>/dev/null || stat -f%z "$ZIP_PATH" 2>/dev/null)
        else
            # Linux/Mac
            TAMANO_BYTES=$(stat -c%s "$ZIP_PATH" 2>/dev/null || stat -f%z "$ZIP_PATH" 2>/dev/null)
        fi
        
        if [ -n "$TAMANO_BYTES" ]; then
            TAMANO_MB=$(echo "scale=2; $TAMANO_BYTES / 1048576" | bc 2>/dev/null || awk "BEGIN {printf \"%.2f\", $TAMANO_BYTES / 1048576}")
            echo -e "${CYAN}Tamano del archivo: ${TAMANO_MB} MB${NC}"
        fi
    fi
    
    return 0
}

# Función para subir vía SCP
subir_via_scp() {
    local ARCHIVO_ZIP="$1"
    local USUARIO="$2"
    local IP="$3"
    local RUTA_DESTINO="$4"
    
    echo ""
    echo "========================================"
    echo -e "${CYAN}  Subiendo archivos al VPS...${NC}"
    echo "========================================"
    echo ""
    
    # Verificar que SCP esté disponible
    if ! command -v scp &> /dev/null; then
        echo -e "${RED}ERROR: SCP no está disponible.${NC}"
        echo -e "${YELLOW}Opciones:${NC}"
        echo "1. Usar WinSCP (interfaz gráfica): https://winscp.net/"
        echo "2. Usar el archivo ZIP manualmente: $ARCHIVO_ZIP"
        return 1
    fi
    
    local RUTA_TEMP="/tmp/mafit_upload"
    local DESTINO_COMPLETO="${USUARIO}@${IP}:${RUTA_TEMP}.zip"
    
    echo -e "${YELLOW}Subiendo archivo ZIP al servidor...${NC}"
    echo -e "${CYAN}Destino: $DESTINO_COMPLETO${NC}"
    
    # Subir ZIP
    scp "$ARCHIVO_ZIP" "$DESTINO_COMPLETO"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}Archivo subido exitosamente${NC}"
        
        echo ""
        echo "========================================"
        echo -e "${CYAN}  Próximos pasos en el servidor:${NC}"
        echo "========================================"
        echo ""
        echo -e "${YELLOW}1. Conéctate al servidor:${NC}"
        echo "   ssh ${USUARIO}@${IP}"
        echo ""
        echo -e "${YELLOW}2. Ejecuta estos comandos:${NC}"
        echo "   cd /tmp"
        echo "   unzip -q mafit_upload.zip -d mafit_temp"
        echo "   sudo rm -rf $RUTA_DESTINO"
        echo "   sudo mv mafit_temp/$PROYECTO_NOMBRE $RUTA_DESTINO"
        echo "   sudo chown -R www-data:www-data $RUTA_DESTINO"
        echo "   cd $RUTA_DESTINO"
        echo "   bash instalar_en_servidor.sh"
        echo ""
        
        return 0
    else
        echo -e "${RED}ERROR: Fallo al subir archivo${NC}"
        echo -e "${YELLOW}Verifica tus credenciales y conexión SSH${NC}"
        return 1
    fi
}

# Función para subir vía rsync
subir_via_rsync() {
    local USUARIO="$1"
    local IP="$2"
    local RUTA_DESTINO="$3"
    
    echo ""
    echo "========================================"
    echo -e "${CYAN}  Subiendo archivos vía Rsync...${NC}"
    echo "========================================"
    echo ""
    
    # Verificar que rsync esté disponible
    if ! command -v rsync &> /dev/null; then
        echo -e "${RED}ERROR: Rsync no está disponible.${NC}"
        echo -e "${YELLOW}Puedes usar el método ZIP en su lugar.${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}Sincronizando archivos (esto puede tardar)...${NC}"
    
    local DESTINO_RSYNC="${USUARIO}@${IP}:${RUTA_DESTINO}/"
    local ORIGEN_RSYNC="${PROYECTO_PATH}/"
    
    rsync -avz --progress \
        --exclude="node_modules" \
        --exclude="vendor" \
        --exclude=".git" \
        --exclude="public/build" \
        --exclude="public/hot" \
        --exclude=".env" \
        --exclude="*.bat" \
        --exclude="mafit_backup.sql" \
        "$ORIGEN_RSYNC" "$DESTINO_RSYNC"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}Archivos sincronizados exitosamente${NC}"
        return 0
    else
        echo -e "${RED}ERROR: Fallo en la sincronización${NC}"
        return 1
    fi
}

# ============================================
# EJECUCIÓN PRINCIPAL
# ============================================

# Verificar parámetros
VPS_USUARIO=""
VPS_IP=""
VPS_RUTA="/var/www/mafit"
SOLO_PREPARAR=false

# Parsear argumentos
while [[ $# -gt 0 ]]; do
    case $1 in
        -u|--usuario)
            VPS_USUARIO="$2"
            shift 2
            ;;
        -i|--ip)
            VPS_IP="$2"
            shift 2
            ;;
        -r|--ruta)
            VPS_RUTA="$2"
            shift 2
            ;;
        -s|--solo-preparar)
            SOLO_PREPARAR=true
            shift
            ;;
        *)
            echo -e "${YELLOW}Uso: $0 [-u usuario] [-i ip] [-r ruta] [-s]${NC}"
            echo "  -u, --usuario      Usuario del VPS"
            echo "  -i, --ip           IP del VPS"
            echo "  -r, --ruta         Ruta en el VPS (default: /var/www/mafit)"
            echo "  -s, --solo-preparar Solo crear ZIP sin subir"
            exit 1
            ;;
    esac
done

# Crear ZIP
if crear_zip; then
    echo ""
    echo -e "${GREEN}Archivo ZIP preparado: $ZIP_PATH${NC}"
else
    echo -e "${RED}ERROR: No se pudo crear el archivo ZIP${NC}"
    exit 1
fi

# Si solo preparar, salir
if [ "$SOLO_PREPARAR" = true ]; then
    echo -e "${YELLOW}Puedes subirlo manualmente usando WinSCP o SCP${NC}"
    exit 0
fi

# Si se proporcionaron credenciales, preguntar método
if [ -n "$VPS_USUARIO" ] && [ -n "$VPS_IP" ]; then
    echo ""
    echo -e "${YELLOW}¿Cómo quieres subir?${NC}"
    echo "1) SCP/ZIP (recomendado)"
    echo "2) Rsync"
    echo "3) Solo preparar ZIP (ya hecho)"
    echo ""
    read -p "Selecciona una opción [1]: " METODO
    
    METODO=${METODO:-1}
    
    case $METODO in
        1)
            subir_via_scp "$ZIP_PATH" "$VPS_USUARIO" "$VPS_IP" "$VPS_RUTA"
            ;;
        2)
            subir_via_rsync "$VPS_USUARIO" "$VPS_IP" "$VPS_RUTA"
            ;;
        3)
            echo -e "${GREEN}ZIP preparado: $ZIP_PATH${NC}"
            echo -e "${YELLOW}Puedes subirlo manualmente usando WinSCP o SCP${NC}"
            ;;
        *)
            echo -e "${RED}Opción inválida${NC}"
            exit 1
            ;;
    esac
else
    echo ""
    echo -e "${GREEN}Archivo ZIP preparado: $ZIP_PATH${NC}"
    echo ""
    echo -e "${YELLOW}Para subir automáticamente, ejecuta:${NC}"
    echo "  bash $0 -u tu_usuario -i tu_ip -r /var/www/mafit"
    echo ""
    echo -e "${YELLOW}O sube manualmente el archivo ZIP usando WinSCP o SCP${NC}"
fi

echo ""
echo "========================================"
echo -e "${CYAN}  Proceso completado${NC}"
echo "========================================"
echo ""

