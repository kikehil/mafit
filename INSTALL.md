# Guía de Instalación Rápida

> **¿No quieres usar Docker?** Revisa el archivo `INSTALL_SIN_DOCKER.md` para instalación con Laragon, XAMPP o manual.

## Pasos de Instalación (con Docker)

### 1. Configurar Variables de Entorno

```bash
cp .env.example .env
```

Editar `.env` y verificar:
- `DB_HOST=mysql`
- `DB_DATABASE=mafit`
- `DB_USERNAME=mafit`
- `DB_PASSWORD=root`

### 2. Iniciar Contenedores

```bash
docker compose up -d
```

### 3. Instalar Dependencias PHP

```bash
docker compose exec app composer install
```

### 4. Generar Clave de Aplicación

```bash
docker compose exec app php artisan key:generate
```

### 5. Ejecutar Migraciones y Seeders

```bash
docker compose exec app php artisan migrate --seed
```

### 6. Instalar y Compilar Assets

```bash
npm install
npm run build
```

### 7. Configurar Permisos

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### 8. Crear Usuario Administrador

Opción A - Desde Tinker:
```bash
docker compose exec app php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

Opción B - Registrarse desde la web y luego actualizar:
```bash
docker compose exec app php artisan tinker
```

```php
$user = App\Models\User::where('email', 'tu-email@example.com')->first();
$user->role = 'admin';
$user->save();
```

## Acceso

- **URL**: http://localhost:8080
- **Login**: Usa las credenciales del usuario administrador

## Comandos Útiles

### Ver Logs
```bash
docker compose exec app tail -f storage/logs/laravel.log
```

### Acceder a la Base de Datos
```bash
docker compose exec mysql mysql -u mafit -proot mafit
```

### Limpiar Cache
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

### Reiniciar Contenedores
```bash
docker compose restart
```

### Detener Contenedores
```bash
docker compose down
```

## Solución de Problemas

### Error: "Class 'Normalizer' not found"
- La extensión intl está incluida en el Dockerfile
- Reconstruir contenedores: `docker compose down && docker compose up -d --build`

### Error de Permisos
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Error de Conexión a Base de Datos
- Verificar que el contenedor mysql esté corriendo: `docker compose ps`
- Verificar variables en `.env`



