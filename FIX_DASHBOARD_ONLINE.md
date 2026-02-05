# Solución: Dashboard no se muestra después del login (VPS)

## Problema
Después de hacer login en el sitio online (VPS), aparece un error 500 en lugar del dashboard.

## Causa
Los usuarios no tienen asignado el módulo "dashboard" en la base de datos, por lo que el middleware `CheckModulePermission` bloquea el acceso.

## Solución

### Opción 1: Ejecutar script en el VPS

1. Conectarse al VPS por SSH
2. Navegar al directorio del proyecto
3. Ejecutar el script:
```bash
bash fix_dashboard_vps.sh
```

### Opción 2: Ejecutar comandos manualmente

Conectarse al VPS y ejecutar:

```bash
# 1. Ejecutar seeder de módulos
docker-compose exec app php artisan db:seed --class=ModuleSeeder

# 2. Asignar módulo dashboard a todos los usuarios
docker-compose exec app php artisan tinker --execute="
\$dashboardModule = App\Models\Module::where('name', 'dashboard')->first();
if (\$dashboardModule) {
    \$users = App\Models\User::all();
    foreach (\$users as \$user) {
        if (!\$user->modules()->where('module_id', \$dashboardModule->id)->exists()) {
            \$user->modules()->attach(\$dashboardModule->id);
            echo 'Módulo dashboard asignado a: ' . \$user->name . PHP_EOL;
        }
    }
}
"

# 3. Limpiar caché
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Opción 3: Solución rápida (Remover middleware temporalmente)

Si necesitas acceso inmediato mientras investigas, puedes comentar temporalmente el middleware en `routes/web.php`:

Cambiar línea 24:
```php
// ANTES
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('module:dashboard')->name('dashboard');

// DESPUÉS (temporal)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
```

**NOTA:** Esta es una solución temporal. Lo correcto es asignar los módulos a los usuarios.

## Verificación

Después de aplicar la solución, intenta hacer login nuevamente. Deberías ver el dashboard sin problemas.

## Prevención futura

Para evitar este problema con nuevos usuarios, considera:

1. Crear un evento/listener que asigne módulos por defecto al crear usuarios
2. Modificar el seeder de usuarios para asignar módulos automáticamente
3. Agregar lógica en el registro de usuarios para asignar módulos según el rol
