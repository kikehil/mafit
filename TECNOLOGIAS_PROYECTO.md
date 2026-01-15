# Tecnologías Utilizadas en MAFIT

Este documento detalla todas las tecnologías, frameworks, librerías y herramientas utilizadas en el proyecto MAFIT.

## 🎯 Stack Principal

### Backend
- **Laravel 11** - Framework PHP moderno y robusto
- **PHP 8.3** - Lenguaje de programación del lado del servidor
- **MySQL 8** - Sistema de gestión de bases de datos relacional
- **Eloquent ORM** - ORM incluido en Laravel para manejo de base de datos

### Frontend
- **Blade** - Motor de plantillas de Laravel
- **TailwindCSS 3.4.1** - Framework CSS utility-first
- **Vite 5.0** - Build tool y bundler moderno
- **Axios 1.7.4** - Cliente HTTP para JavaScript
- **@tailwindcss/forms 0.5.7** - Plugin de Tailwind para estilos de formularios

### Herramientas de Build
- **PostCSS** - Procesador de CSS
- **Autoprefixer 10.4.23** - Plugin de PostCSS para prefijos CSS automáticos
- **Laravel Vite Plugin 1.0** - Integración de Vite con Laravel

## 📦 Dependencias PHP (Composer)

### Core Framework
- **laravel/framework ^11.0** - Framework Laravel completo
- **laravel/breeze ^2.0** - Kit de autenticación (Blade)
- **laravel/sanctum ^4.0** - Autenticación API con tokens
- **laravel/tinker ^2.9** - REPL interactivo para Laravel

### Procesamiento de Archivos
- **phpoffice/phpspreadsheet ^1.30** - Librería para leer/escribir archivos Excel
- **maatwebsite/excel ^3.1** - Wrapper de Laravel para PhpSpreadsheet

### HTTP y Comunicación
- **guzzlehttp/guzzle ^7.2** - Cliente HTTP para PHP

### Desarrollo y Testing
- **phpunit/phpunit ^11.0.1** - Framework de testing
- **fakerphp/faker ^1.23** - Generador de datos falsos para testing
- **mockery/mockery ^1.6** - Framework de mocking para tests
- **nunomaduro/collision ^8.0** - Manejo mejorado de errores en CLI
- **laravel/pint ^1.13** - Code style fixer para Laravel
- **laravel/sail ^1.26** - Entorno Docker para Laravel

## 🗄️ Base de Datos

- **MySQL 8** - Base de datos relacional
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **ORM**: Eloquent (incluido en Laravel)

### Características de Base de Datos
- Migraciones de Laravel para control de versiones
- Seeders para datos iniciales
- Índices optimizados para consultas de reportes
- Sistema de colas usando driver de base de datos

## 🔐 Autenticación y Autorización

- **Laravel Breeze** - Sistema de autenticación completo
  - Login/Logout
  - Registro de usuarios
  - Recuperación de contraseña
  - Verificación de email
- **Laravel Sanctum** - Autenticación API con tokens
- **Gates y Policies** - Sistema de autorización de Laravel
- **Middleware personalizado** - `CheckModulePermission` para control de acceso

## 📧 Sistema de Correo

- **Laravel Mail** - Sistema de envío de correos
- Clases de Mail implementadas:
  - `InventarioNotificacionMail`
  - `MovimientoEquipoMail`
  - `MovimientoPSFMail`

## ⚙️ Procesamiento Asíncrono

- **Laravel Queues** - Sistema de colas
- **Database Driver** - Driver de colas usando base de datos
- **Jobs** - `ProcessMafImport` para procesamiento asíncrono de importaciones

## 🐳 Contenedores y DevOps

### Docker
- **Docker Compose** - Orquestación de contenedores
- **Contenedores configurados**:
  - App (PHP 8.3 + Laravel)
  - Nginx (Servidor web)
  - MySQL 8 (Base de datos)

### Scripts de Despliegue
- **PowerShell** - Scripts para Windows (`subir_a_vps.ps1`)
- **Bash** - Scripts para Linux/Mac (`subir_a_vps.sh`, `instalar_en_servidor.sh`)
- **Batch** - Scripts simples para Windows (`.bat`)

## 🛠️ Herramientas de Desarrollo

### Gestión de Código
- **Git** - Control de versiones
- **Composer** - Gestor de dependencias PHP
- **npm** - Gestor de paquetes Node.js

### Entornos de Desarrollo Soportados
- **Docker/Docker Compose** - Entorno containerizado (recomendado)
- **XAMPP** - Entorno local Windows
- **Laragon** - Entorno local Windows alternativo
- **Laravel Sail** - Entorno Docker simplificado

## 📊 Funcionalidades Específicas del Proyecto

### Procesamiento de Excel
- **PhpSpreadsheet** - Lectura y escritura de archivos Excel (.xlsx)
- **Mapeo automático** de columnas Excel a base de datos
- **Limpieza y normalización** de datos:
  - Eliminación de caracteres invisibles
  - Normalización Unicode NFKC
  - Limpieza de identificadores
  - Conversión segura de valores numéricos

### Reportes y Exportación
- **Exportación CSV** - Generación de reportes en formato CSV
- **Formato compatible con Excel** - BOM UTF-8 para compatibilidad

### Búsqueda y Filtrado
- Búsqueda avanzada de activos
- Filtrado por múltiples criterios
- Paginación de resultados

## 🎨 UI/UX

### Estilos
- **TailwindCSS** - Framework CSS utility-first
- **Figtree** - Fuente sans-serif por defecto
- **Responsive Design** - Diseño adaptable a diferentes tamaños de pantalla

### Componentes
- Formularios estilizados con `@tailwindcss/forms`
- Tablas responsivas
- Modales y alertas
- Navegación con menús desplegables

## 🔍 Características Técnicas Avanzadas

### Normalización de Texto
- Clase helper `TextNorm` para limpieza de texto
- Eliminación de caracteres de control
- Normalización Unicode
- Limpieza de identificadores (placa, activo, serie)

### Optimización
- **Caché de configuración** - `php artisan config:cache`
- **Caché de rutas** - `php artisan route:cache`
- **Caché de vistas** - `php artisan view:cache`
- **Autoloader optimizado** - `composer install --optimize-autoloader`

### Seguridad
- **CSRF Protection** - Protección contra ataques CSRF
- **XSS Protection** - Escapado automático en Blade
- **SQL Injection Protection** - Uso de consultas preparadas (Eloquent)
- **Password Hashing** - Bcrypt para contraseñas
- **Sanitización de inputs** - Validación y limpieza de datos

## 📱 Compatibilidad

### Navegadores
- Chrome/Edge (recomendado)
- Firefox
- Safari
- Opera

### Sistemas Operativos
- **Desarrollo**: Windows 10/11, Linux, macOS
- **Producción**: Linux (Ubuntu/Debian recomendado)

### Servidores Web
- **Nginx** (recomendado para producción)
- **Apache** (soportado)

## 🔮 Preparado para Futuras Fases

El proyecto está estructurado para soportar:

- **Generación de PDFs** - Estructura lista para implementar
- **Gráficos y visualizaciones** - Preparado para librerías de gráficos
- **Notificaciones en tiempo real** - Sistema de colas configurado
- **API REST** - Laravel Sanctum ya incluido
- **Exportación avanzada** - Base para múltiples formatos

## 📋 Resumen de Versiones

| Tecnología | Versión |
|------------|---------|
| PHP | 8.3 |
| Laravel | 11.0 |
| MySQL | 8 |
| Node.js | (requerido para build) |
| TailwindCSS | 3.4.1 |
| Vite | 5.0 |
| PhpSpreadsheet | 1.30 |
| Axios | 1.7.4 |

## 🚀 Comandos Útiles

### Desarrollo
```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Compilar assets para desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

### Producción
```bash
# Optimizar autoloader
composer install --optimize-autoloader --no-dev

# Caché de configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Rollback migraciones
php artisan migrate:rollback
```

---

**Última actualización**: Enero 2025

