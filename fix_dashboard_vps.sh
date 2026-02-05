#!/bin/bash

echo "=== Solucionando problema de Dashboard en VPS ==="

# 1. Ejecutar migraciones para crear tablas modules y user_modules
echo "1. Ejecutando migraciones..."
docker-compose exec app php artisan migrate --force

# 2. Ejecutar el seeder de módulos
echo "2. Ejecutando ModuleSeeder..."
docker-compose exec app php artisan db:seed --class=ModuleSeeder --force

# 3. Asignar módulo dashboard a todos los usuarios
echo "3. Asignando módulo dashboard a todos los usuarios..."
docker-compose exec app php artisan tinker --execute="
\$dashboardModule = App\Models\Module::where('name', 'dashboard')->first();
if (\$dashboardModule) {
    \$users = App\Models\User::all();
    foreach (\$users as \$user) {
        if (!\$user->modules()->where('module_id', \$dashboardModule->id)->exists()) {
            \$user->modules()->attach(\$dashboardModule->id);
            echo \"Módulo dashboard asignado a: {\$user->name}\n\";
        } else {
            echo \"Usuario {\$user->name} ya tiene acceso al dashboard\n\";
        }
    }
    echo \"Proceso completado!\n\";
} else {
    echo \"ERROR: Módulo dashboard no encontrado. Ejecute primero el ModuleSeeder.\n\";
}
"

# 4. Limpiar caché
echo "4. Limpiando caché..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo "=== Proceso completado ==="
echo "Ahora intente hacer login nuevamente"
