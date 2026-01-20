# Changelog

## [1.0.0] - 2024-01-01

### Implementado

#### Módulo de Carga del Archivo Maestro (MAF)

- **Pantalla de Importación** (`/maf/import`)
  - Selector de período (YYYY-MM)
  - Subida de archivo Excel (.xlsx)
  - Validación de formato y tamaño
  - Mensaje informativo sobre limpieza automática

- **Procesamiento de Importación**
  - Creación de lotes/batches con estado de procesamiento
  - Parseo de Excel con mapeo automático de columnas
  - Limpieza y normalización de datos:
    - Eliminación de caracteres invisibles (U+202D, U+202E, etc.)
    - Normalización Unicode NFKC
    - Limpieza de identificadores (placa, activo, serie)
    - Conversión segura de valores numéricos
  - Inserción en lotes para optimización
  - Manejo de errores con rollback automático

- **Listado de Lotes** (`/maf/batches`)
  - Tabla con todos los lotes importados
  - Información de estado, filas procesadas, usuario y fecha
  - Paginación

- **Detalle de Lote** (`/maf/batches/{id}`)
  - Resumen completo del lote
  - Reporte de conflictos graves (mismo identificador en 2+ tiendas)
  - Reporte de duplicados simples (mismo identificador en misma tienda)
  - Detalle expandible de ocurrencias
  - Separación por tipo de identificador (PLACA, ACTIVO, SERIE)

- **Exportación CSV** (`/maf/batches/{id}/report.csv`)
  - Exportación completa de conflictos y duplicados
  - Formato compatible con Excel (BOM UTF-8)

#### Infraestructura

- Docker Compose con app, nginx y mysql
- Configuración de colas (database driver)
- Autenticación con Laravel Breeze
- Sistema de autorización con Gates
- Estructura preparada para futuras fases (PDF, gráficos, notificaciones)

#### Base de Datos

- Tabla `plazas` con catálogo inicial (5 plazas)
- Tabla `maf_import_batches` para control de lotes
- Tabla `maf` para almacenamiento de datos importados
- Índices optimizados para consultas de reportes
- Migraciones y seeders completos

### Características Técnicas

- **Limpieza de Datos**:
  - Eliminación de BOM (U+FEFF) y NBSP (U+00A0)
  - Eliminación de caracteres de formato (\p{Cf}) y controles (\p{Cc})
  - Normalización de identificadores (solo A-Z, 0-9, guión)
  - Conversión segura de decimales con soporte para comas

- **Mapeo Inteligente**:
  - Normalización de encabezados (sin acentos, mayúsculas, espacios)
  - Soporte para variantes de nombres de columnas
  - Validación de columnas mínimas requeridas

- **Reportes**:
  - Detección automática de conflictos y duplicados
  - Agregación por tipo de identificador
  - Conteo de tiendas y plazas distintas
  - Detalle completo de ocurrencias

### Preparado para Futuras Fases

- Sistema de colas configurado
- Estructura para notificaciones
- Scaffolding para generación de PDF
- Preparado para visualizaciones y gráficos

















