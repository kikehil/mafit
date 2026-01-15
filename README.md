# MAFIT - Sistema de Control de Activos Fijos

Sistema interno para control de activos fijos basado en un Excel maestro (MAF). Este proyecto implementa el módulo de "Carga del Archivo Maestro (MAF)" para escritorio.

## Stack Tecnológico

- **Backend**: Laravel 11 (PHP 8.3)
- **Base de Datos**: MySQL 8
- **Frontend**: Blade + TailwindCSS
- **Autenticación**: Laravel Breeze (Blade)
- **Excel Import**: PhpSpreadsheet
- **Queue**: Database driver (preparado para futuras notificaciones)

## Requisitos Previos

- Docker y Docker Compose
- Composer
- Node.js y npm

## Instalación

### 1. Clonar y configurar el proyecto

```bash
cd C:\WEB\MAFIT
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar `.env` y verificar las credenciales de base de datos:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=mafit
DB_PASSWORD=root
```

### 3. Iniciar contenedores Docker

```bash
docker compose up -d
```

### 4. Instalar dependencias PHP

```bash
docker compose exec app composer install
```

### 5. Generar clave de aplicación

```bash
docker compose exec app php artisan key:generate
```

### 6. Ejecutar migraciones y seeders

```bash
docker compose exec app php artisan migrate --seed
```

### 7. Instalar dependencias Node.js y compilar assets

```bash
npm install
npm run build
```

### 8. Configurar permisos de almacenamiento

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### 9. Iniciar worker de colas (opcional, para procesamiento asíncrono)

```bash
docker compose exec app php artisan queue:work
```

## Crear Usuario Administrador

### Opción 1: Crear un nuevo usuario admin desde Tinker

Para crear un usuario administrador, ejecuta:

```bash
docker compose exec app php artisan tinker
```

Luego en la consola de Tinker:

```php
$user = App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'plaza' => '32YXH' // Código de plaza existente
]);
```

### Opción 2: Convertir un usuario existente en admin

Si ya tienes un usuario registrado y quieres convertirlo en administrador:

```bash
docker compose exec app php artisan tinker
```

Luego en la consola de Tinker:

```php
$user = App\Models\User::where('email', 'tu-email@example.com')->first();
$user->role = 'admin';
$user->plaza = '32YXH'; // Asignar una plaza si no tiene
$user->save();
```

### Opción 3: Usar el módulo de Gestión de Usuarios (requiere ser admin)

Una vez que tengas un usuario admin, puedes crear y gestionar usuarios desde la interfaz web en `/admin/users`.

## Acceso a la Aplicación

- **URL**: http://localhost:8080
- **Login**: Usa las credenciales del usuario administrador creado

## Estructura del Proyecto

```
MAFIT/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/          # Controladores de autenticación
│   │   ├── MafImportController.php
│   │   └── MafBatchController.php
│   ├── Jobs/
│   │   └── ProcessMafImport.php
│   ├── Models/
│   │   ├── Maf.php
│   │   ├── MafImportBatch.php
│   │   ├── Plaza.php
│   │   └── User.php
│   ├── Services/
│   │   └── MafImportService.php  # Lógica de importación y limpieza
│   └── Providers/
│       └── AuthServiceProvider.php
├── database/
│   ├── migrations/        # Migraciones de base de datos
│   └── seeders/           # Seeders (plazas iniciales)
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── maf/           # Vistas del módulo MAF
│   │   └── auth/          # Vistas de autenticación
│   ├── css/
│   └── js/
└── routes/
    ├── web.php            # Rutas web
    └── auth.php           # Rutas de autenticación
```

## Funcionalidades Implementadas

### 1. Carga de Archivo MAF

- **Ruta**: `/maf/import`
- Permite subir archivos Excel (.xlsx)
- Selección de período (YYYY-MM)
- Limpieza automática de caracteres invisibles
- Normalización de identificadores (placa, activo, serie)

### 2. Listado de Lotes

- **Ruta**: `/maf/batches`
- Muestra todos los lotes de importación
- Filtrado por estado (procesando, completado, fallido)
- Información de filas procesadas y usuario

### 3. Detalle de Lote

- **Ruta**: `/maf/batches/{id}`
- Resumen del lote
- Reporte de conflictos graves (mismo identificador en 2+ tiendas)
- Reporte de duplicados simples (mismo identificador en misma tienda)
- Detalle de ocurrencias con información completa

### 4. Exportación CSV

- **Ruta**: `/maf/batches/{id}/report.csv`
- Exporta reporte completo de conflictos y duplicados
- Formato compatible con Excel

## Mapeo de Columnas Excel → Base de Datos

El sistema mapea automáticamente las siguientes columnas:

| Excel | Base de Datos |
|-------|---------------|
| Distrito | plaza |
| Cr | cr |
| Cr Desc | tienda |
| Codigo Barras | placa |
| No Activo | activo |
| Mes Adq | mescompra |
| Anio Adq / Año Adq | anocompra |
| Costo | costo |
| Valor Neto | valor_neto |
| Remantente | remanente |
| Descripcion | descripcion |
| Marca | marca |
| Modelo | modelo |
| Serie | serie |

## Normalización y Limpieza

El sistema realiza las siguientes operaciones de limpieza:

1. **Limpieza de texto general**:
   - Validación UTF-8
   - Normalización Unicode NFKC
   - Eliminación de BOM (U+FEFF) y NBSP (U+00A0)
   - Eliminación de caracteres de formato (\p{Cf}) y controles (\p{Cc})
   - Trim y colapso de espacios

2. **Limpieza de identificadores** (placa, activo, serie):
   - Aplicación de limpieza general
   - Conversión a mayúsculas
   - Eliminación de espacios internos
   - Permite solo caracteres A-Z, 0-9 y guión (-)

3. **Conversión de valores numéricos**:
   - Soporte para comas como separador de miles
   - Eliminación de símbolos de moneda
   - Conversión segura a decimal (12,2)

## Reglas de Negocio

### Conflictos Graves

Un mismo identificador (placa, activo o serie) aparece en 2 o más tiendas distintas (CR diferentes). Esto se considera un conflicto grave que debe corregirse en el MAF.

### Duplicados Simples

Un mismo identificador aparece más de una vez, pero en la misma tienda (mismo CR). Esto puede ser normal, pero debe verificarse.

## Desarrollo

### Ejecutar tests (cuando se implementen)

```bash
docker compose exec app php artisan test
```

### Ver logs

```bash
docker compose exec app tail -f storage/logs/laravel.log
```

### Acceder a la base de datos

```bash
docker compose exec mysql mysql -u mafit -proot mafit
```

## Preparación para Futuras Fases

El proyecto está preparado con scaffolding para:

- **Notificaciones**: Sistema de colas configurado (database driver)
- **PDF**: Estructura lista para implementar generación de reportes PDF
- **Gráficos**: Preparado para visualizaciones de datos

## Solución de Problemas

### Error de permisos

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Reconstruir contenedores

```bash
docker compose down
docker compose up -d --build
```

### Limpiar cache

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

## Licencia

Proyecto interno - Todos los derechos reservados.









