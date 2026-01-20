<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tienda;

class TiendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Extraer tiendas únicas de la tabla maf
        $tiendas = DB::table('maf')
            ->select('plaza', 'cr', 'tienda')
            ->whereNotNull('plaza')
            ->whereNotNull('cr')
            ->where('cr', '!=', '')
            ->distinct()
            ->get();

        foreach ($tiendas as $tienda) {
            // Usar updateOrCreate para evitar duplicados
            Tienda::updateOrCreate(
                [
                    'plaza' => $tienda->plaza,
                    'cr' => $tienda->cr,
                ],
                [
                    'tienda' => $tienda->tienda,
                ]
            );
        }

        $this->command->info("Se crearon/actualizaron " . Tienda::count() . " tiendas.");
    }
}
