<?php
/**
 * Script de diagnóstico para problemas de login en VPS
 * Ejecutar desde la raíz del proyecto: php diagnosticar-login-vps.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNÓSTICO DE LOGIN EN VPS ===\n\n";

// 1. Verificar configuración de sesiones
echo "1. CONFIGURACIÓN DE SESIONES:\n";
echo "   - Driver: " . config('session.driver') . "\n";
echo "   - Cookie: " . config('session.cookie') . "\n";
echo "   - Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "   - SameSite: " . config('session.same_site') . "\n";
echo "   - Domain: " . (config('session.domain') ?: 'null') . "\n";
echo "   - Path: " . config('session.path') . "\n";
echo "   - Lifetime: " . config('session.lifetime') . " minutos\n\n";

// 2. Verificar tabla de sesiones
echo "2. TABLA DE SESIONES:\n";
try {
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('sessions');
    echo "   - Tabla 'sessions' existe: " . ($tableExists ? 'SÍ' : 'NO') . "\n";
    
    if ($tableExists) {
        $sessionCount = \Illuminate\Support\Facades\DB::table('sessions')->count();
        echo "   - Sesiones activas: $sessionCount\n";
    } else {
        echo "   ⚠️  ERROR: La tabla 'sessions' no existe. Ejecuta: php artisan migrate\n";
    }
} catch (\Exception $e) {
    echo "   ⚠️  ERROR al verificar tabla: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Verificar conexión a base de datos
echo "3. CONEXIÓN A BASE DE DATOS:\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "   - Conexión: OK\n";
    echo "   - Base de datos: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "   ⚠️  ERROR de conexión: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Verificar permisos de storage
echo "4. PERMISOS DE STORAGE:\n";
$sessionPath = storage_path('framework/sessions');
if (is_dir($sessionPath)) {
    $writable = is_writable($sessionPath);
    echo "   - Directorio existe: SÍ\n";
    echo "   - Permisos de escritura: " . ($writable ? 'OK' : 'NO') . "\n";
    if (!$writable) {
        echo "   ⚠️  ERROR: El directorio no tiene permisos de escritura.\n";
        echo "      Ejecuta: chmod -R 775 storage/framework/sessions\n";
    }
} else {
    echo "   ⚠️  ERROR: El directorio no existe: $sessionPath\n";
    echo "      Ejecuta: mkdir -p storage/framework/sessions && chmod -R 775 storage/framework/sessions\n";
}
echo "\n";

// 5. Verificar configuración de APP_URL
echo "5. CONFIGURACIÓN DE APP:\n";
echo "   - APP_URL: " . config('app.url') . "\n";
echo "   - APP_ENV: " . config('app.env') . "\n";
echo "   - APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "\n";

// 6. Verificar detección de HTTPS
echo "6. DETECCIÓN DE HTTPS:\n";
$isSecure = false;
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) {
    $isSecure = true;
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $isSecure = true;
}
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
}
echo "   - Detectado como HTTPS: " . ($isSecure ? 'SÍ' : 'NO') . "\n";
echo "   - SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'no definido') . "\n";
echo "   - HTTPS: " . ($_SERVER['HTTPS'] ?? 'no definido') . "\n";
echo "   - X-Forwarded-Proto: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'no definido') . "\n";
echo "\n";

// 7. Verificar usuarios en base de datos
echo "7. USUARIOS EN BASE DE DATOS:\n";
try {
    $userCount = \App\Models\User::count();
    echo "   - Total de usuarios: $userCount\n";
    if ($userCount > 0) {
        $sampleUser = \App\Models\User::first();
        echo "   - Usuario de ejemplo: " . $sampleUser->email . "\n";
    }
} catch (\Exception $e) {
    echo "   ⚠️  ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Recomendaciones
echo "=== RECOMENDACIONES ===\n";
if (config('session.driver') === 'database') {
    echo "✓ El driver de sesiones está configurado como 'database'\n";
} else {
    echo "⚠️  Considera usar 'database' como driver de sesiones en producción\n";
}

if (config('session.secure') && !$isSecure) {
    echo "⚠️  ADVERTENCIA: SESSION_SECURE_COOKIE está en true pero no se detecta HTTPS\n";
    echo "   Esto puede impedir que las cookies funcionen. Verifica tu configuración.\n";
}

if (!$isSecure && config('session.secure')) {
    echo "⚠️  ADVERTENCIA: Las cookies están configuradas como 'secure' pero el sitio no usa HTTPS\n";
    echo "   Esto impedirá que las cookies funcionen. Configura SESSION_SECURE_COOKIE=false o habilita HTTPS\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";

