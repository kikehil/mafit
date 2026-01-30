<?php
/**
 * Script para verificar que las categorías estén correctamente en el VPS
 * Ejecutar desde la línea de comandos: php verificar_categorias_vps.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Verificación de Categorías en VPS\n";
echo "========================================\n\n";

// 1. Verificar que la tabla maf existe y tiene la columna categoria
echo "1. Verificando estructura de tabla 'maf'...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM maf LIKE 'categoria'");
    if (empty($columns)) {
        echo "   ❌ ERROR: La columna 'categoria' no existe en la tabla 'maf'\n";
        echo "   Solución: Ejecutar migración: php artisan migrate\n";
    } else {
        echo "   ✓ Columna 'categoria' existe\n";
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Verificar que hay registros con categoría
echo "\n2. Verificando registros con categoría...\n";
$totalRegistros = DB::table('maf')->count();
$registrosConCategoria = DB::table('maf')
    ->whereNotNull('categoria')
    ->where('categoria', '!=', '')
    ->count();

echo "   Total de registros en 'maf': " . number_format($totalRegistros) . "\n";
echo "   Registros con categoría: " . number_format($registrosConCategoria) . "\n";

if ($registrosConCategoria == 0) {
    echo "   ⚠️  ADVERTENCIA: No hay registros con categoría asignada\n";
    echo "   Solución: Aplicar categorías con: php artisan maf:categorize\n";
} else {
    $porcentaje = ($registrosConCategoria / max($totalRegistros, 1)) * 100;
    echo "   Porcentaje con categoría: " . number_format($porcentaje, 2) . "%\n";
}

// 3. Verificar categorías únicas
echo "\n3. Verificando categorías disponibles...\n";
$categorias = DB::table('maf')
    ->whereNotNull('categoria')
    ->where('categoria', '!=', '')
    ->select('categoria')
    ->distinct()
    ->pluck('categoria')
    ->toArray();

if (empty($categorias)) {
    echo "   ❌ ERROR: No se encontraron categorías\n";
} else {
    echo "   ✓ Categorías encontradas (" . count($categorias) . "):\n";
    foreach ($categorias as $cat) {
        $count = DB::table('maf')->where('categoria', $cat)->count();
        echo "      - $cat (" . number_format($count) . " equipos)\n";
    }
}

// 4. Verificar tabla maf_categoria_map
echo "\n4. Verificando tabla 'maf_categoria_map'...\n";
try {
    $existeTabla = DB::select("SHOW TABLES LIKE 'maf_categoria_map'");
    if (empty($existeTabla)) {
        echo "   ⚠️  ADVERTENCIA: La tabla 'maf_categoria_map' no existe\n";
        echo "   Solución: Ejecutar migración: php artisan migrate\n";
    } else {
        $mapasActivos = DB::table('maf_categoria_map')
            ->where('activo', 1)
            ->count();
        echo "   ✓ Tabla existe con $mapasActivos mapeos activos\n";
    }
} catch (\Exception $e) {
    echo "   ⚠️  ADVERTENCIA: " . $e->getMessage() . "\n";
}

// 5. Verificar último batch procesado
echo "\n5. Verificando último batch procesado...\n";
$ultimoBatch = DB::table('maf_import_batches')
    ->where('status', 'done')
    ->orderBy('finished_at', 'desc')
    ->first();

if (!$ultimoBatch) {
    echo "   ⚠️  ADVERTENCIA: No hay batches procesados\n";
    echo "   Solución: Importar un archivo MAF desde la interfaz\n";
} else {
    echo "   ✓ Último batch: ID {$ultimoBatch->id}, Periodo: {$ultimoBatch->period}\n";
    echo "   Fecha: {$ultimoBatch->finished_at}\n";
    
    $equiposBatch = DB::table('maf')
        ->where('batch_id', $ultimoBatch->id)
        ->count();
    $equiposConCategoria = DB::table('maf')
        ->where('batch_id', $ultimoBatch->id)
        ->whereNotNull('categoria')
        ->where('categoria', '!=', '')
        ->count();
    
    echo "   Equipos en batch: " . number_format($equiposBatch) . "\n";
    echo "   Equipos con categoría: " . number_format($equiposConCategoria) . "\n";
}

// 6. Verificar una tienda de ejemplo
echo "\n6. Verificando ejemplo de tienda...\n";
$tiendaEjemplo = DB::table('maf')
    ->whereNotNull('categoria')
    ->where('categoria', '!=', '')
    ->whereNotNull('cr')
    ->first();

if (!$tiendaEjemplo) {
    echo "   ❌ ERROR: No se encontró ninguna tienda con categorías\n";
} else {
    echo "   ✓ Ejemplo encontrado:\n";
    echo "      CR: {$tiendaEjemplo->cr}\n";
    echo "      Tienda: {$tiendaEjemplo->tienda}\n";
    echo "      Categoría: {$tiendaEjemplo->categoria}\n";
    
    $equiposTienda = DB::table('maf')
        ->where('cr', $tiendaEjemplo->cr)
        ->whereNotNull('categoria')
        ->where('categoria', '!=', '')
        ->count();
    echo "      Equipos con categoría en esta tienda: $equiposTienda\n";
}

echo "\n========================================\n";
echo "Verificación completada\n";
echo "========================================\n";







