#!/bin/bash

# 1. Crear usuario Admin (si no existe)
echo "Creando usuario Admin..."
docker-compose exec -u root app php artisan tinker --execute="
\$u = App\Models\User::where('email', 'admin@mafit.com')->first();
if (!\$u) {
    \$u = new App\Models\User();
    \$u->name = 'Admin';
    \$u->email = 'admin@mafit.com';
    \$u->password = Hash::make('password123');
    \$u->role = 'admin';
    \$u->save();
    echo 'Usuario creado: admin@mafit.com / password123';
} else {
    echo 'El usuario admin ya existe.';
}
"

# 2. Arreglar permisos de estilos
echo "Arreglando permisos..."
docker-compose exec -u root app chown -R www-data:www-data /var/www/public/build
docker-compose exec -u root app chown -R www-data:www-data /var/www/storage

# 3. Limpiar Todo
echo "Limpiando caches..."
docker-compose exec -u root app php artisan optimize:clear
docker-compose exec -u root app php artisan view:clear

echo "¡LISTO! Prueba entrar ahora."
