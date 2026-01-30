# Guía de Migración de Base de Datos al VPS

Esta guía te ayudará a migrar la base de datos local al VPS y asegurar que las categorías funcionen correctamente.

## Paso 1: Exportar Base de Datos Local

### Opción A: Usando el script automatizado (Windows)

1. Abre PowerShell o CMD en la carpeta del proyecto
2. Ejecuta:
   ```cmd
   exportar_bd_local.bat
   ```
3. El archivo SQL se guardará en la carpeta `backups/`

### Opción B: Manualmente

```cmd
"C:\xampp\mysql\bin\mysqldump.exe" -u root -p mafit > backup_mafit.sql
```

## Paso 2: Subir Archivo al VPS

1. Usa SCP, SFTP o el método que prefieras para subir el archivo SQL al VPS
2. Ejemplo con SCP:
   ```bash
   scp backups/mafit_export_YYYYMMDD_HHMMSS.sql usuario@147.93.118.121:/ruta/destino/
   ```

## Paso 3: Importar Base de Datos en el VPS

### Opción A: Usando el script automatizado

1. Conecta al VPS por SSH
2. Navega a la carpeta del proyecto
3. Copia el script `importar_bd_vps.sh` al VPS
4. Dale permisos de ejecución:
   ```bash
   chmod +x importar_bd_vps.sh
   ```
5. Ejecuta:
   ```bash
   ./importar_bd_vps.sh ruta/al/archivo.sql
   ```

### Opción B: Manualmente

```bash
mysql -u usuario -p nombre_base_datos < archivo.sql
```

## Paso 4: Ejecutar Migraciones en el VPS

Asegúrate de que todas las migraciones estén ejecutadas:

```bash
cd /ruta/del/proyecto
php artisan migrate
```

## Paso 5: Verificar Categorías

1. Copia el script `verificar_categorias_vps.php` al VPS
2. Ejecuta:
   ```bash
   php verificar_categorias_vps.php
   ```

Este script verificará:
- ✅ Que la columna `categoria` existe en la tabla `maf`
- ✅ Que hay registros con categoría asignada
- ✅ Qué categorías están disponibles
- ✅ Que la tabla `maf_categoria_map` existe
- ✅ Que hay un batch procesado recientemente

## Paso 6: Aplicar Categorías (si es necesario)

Si las categorías no están aplicadas, ejecuta:

```bash
php artisan maf:categorize
```

O desde la interfaz web:
1. Ve a `/maf/batches`
2. Selecciona el batch más reciente
3. Haz clic en "Aplicar Categorías"

## Paso 7: Limpiar Caché

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Verificación Final

1. Accede a `http://147.93.118.121:8085/inventario/captura`
2. Busca una tienda (ejemplo: "tui")
3. Verifica que el dropdown de categorías muestre las opciones:
   - PUNTO DE VENTA
   - MOVILIDAD
   - ENERGIA
   - CCTV
   - TELCO

## Solución de Problemas

### No se muestran categorías

1. Verifica que hay equipos con categoría:
   ```sql
   SELECT COUNT(*) FROM maf WHERE categoria IS NOT NULL AND categoria != '';
   ```

2. Verifica que hay un batch con status 'done':
   ```sql
   SELECT * FROM maf_import_batches WHERE status = 'done' ORDER BY finished_at DESC LIMIT 1;
   ```

3. Verifica que los equipos tienen el batch_id correcto:
   ```sql
   SELECT COUNT(*) FROM maf WHERE batch_id = [ID_DEL_ULTIMO_BATCH];
   ```

### Error al importar

- Verifica que la base de datos existe en el VPS
- Verifica que el usuario tiene permisos
- Verifica que el archivo SQL no está corrupto
- Revisa los logs de MySQL

### Categorías vacías

Si las categorías están vacías después de importar:

1. Verifica que la tabla `maf_categoria_map` tiene datos:
   ```sql
   SELECT * FROM maf_categoria_map WHERE activo = 1 LIMIT 10;
   ```

2. Si está vacía, importa los mapeos desde el local o ejecuta:
   ```bash
   php artisan db:seed --class=MafCategoriaMapSeeder
   ```

3. Luego aplica las categorías:
   ```bash
   php artisan maf:categorize
   ```

## Comandos Útiles

```bash
# Ver estructura de tabla
DESCRIBE maf;

# Ver categorías únicas
SELECT DISTINCT categoria FROM maf WHERE categoria IS NOT NULL;

# Contar equipos por categoría
SELECT categoria, COUNT(*) as total FROM maf WHERE categoria IS NOT NULL GROUP BY categoria;

# Ver último batch
SELECT * FROM maf_import_batches ORDER BY finished_at DESC LIMIT 1;
```







