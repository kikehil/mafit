<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlazaSeeder extends Seeder
{
    public function run(): void
    {
        $plazas = [
            ['plaza' => '32YXH', 'plaza_nom' => 'CD. VALLES'],
            ['plaza' => '32HJR', 'plaza_nom' => 'CD. VICTORIA'],
            ['plaza' => '32RNA', 'plaza_nom' => 'TAMPICO'],
            ['plaza' => '32WPF', 'plaza_nom' => 'MATAMOROS'],
            ['plaza' => '32IPM', 'plaza_nom' => 'REGION'],
        ];

        foreach ($plazas as $plaza) {
            DB::table('plazas')->insert([
                'plaza' => $plaza['plaza'],
                'plaza_nom' => $plaza['plaza_nom'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

















