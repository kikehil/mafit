<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['name' => 'dashboard', 'display_name' => 'Dashboard', 'route_name' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 12v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'order' => 1],
            ['name' => 'inventario.captura', 'display_name' => 'Captura Inventario', 'route_name' => 'inventario.captura', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'order' => 2],
            ['name' => 'inventario.consulta', 'display_name' => 'Consulta Inventario', 'route_name' => 'inventario.consulta', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'order' => 3],
            ['name' => 'inventario.realizados', 'display_name' => 'Inventarios Realizados', 'route_name' => 'inventario.realizados', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'order' => 4],
            ['name' => 'inventario-psf.captura', 'display_name' => 'Inventario PSF', 'route_name' => 'inventario-psf.captura', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'order' => 5],
            ['name' => 'inventario-psf.consulta', 'display_name' => 'Consulta INV PS', 'route_name' => 'inventario-psf.consulta', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'order' => 6],
            ['name' => 'inventario-psf.movimientos', 'display_name' => 'Movimientos PS', 'route_name' => 'inventario-psf.movimientos', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'order' => 7],
            ['name' => 'movimientos.index', 'display_name' => 'Movimiento de Equipos', 'route_name' => 'movimientos.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'order' => 8],
            ['name' => 'movimientos.consulta', 'display_name' => 'Consulta Movimientos', 'route_name' => 'movimientos.consulta', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'order' => 9],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module['name']],
                $module
            );
        }
    }
}
