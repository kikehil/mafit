<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "VERIFICAR/CREAR USUARIO ADMINISTRADOR\n";
echo "========================================\n\n";

// Verificar si ya existe un usuario
$existingUser = User::where('email', 'admin@example.com')->first();

if ($existingUser) {
    echo "✓ Usuario administrador ya existe:\n";
    echo "  Email: {$existingUser->email}\n";
    echo "  Nombre: {$existingUser->name}\n";
    echo "\n";
    echo "CREDENCIALES DE LOGIN:\n";
    echo "  Email: admin@example.com\n";
    echo "  Password: password\n";
} else {
    echo "Creando usuario administrador...\n";
    
    try {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        
        echo "✓ Usuario creado exitosamente!\n\n";
        echo "CREDENCIALES DE LOGIN:\n";
        echo "  Email: admin@example.com\n";
        echo "  Password: password\n";
    } catch (\Exception $e) {
        echo "✗ Error al crear usuario: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";




