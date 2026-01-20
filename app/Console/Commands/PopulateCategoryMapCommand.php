<?php

namespace App\Console\Commands;

use App\Helpers\TextNorm;
use App\Models\MafCategoriaMap;
use Illuminate\Console\Command;

class PopulateCategoryMapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maf:populate-category-map 
                            {--force : Forzar actualización de registros existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pobla la tabla maf_categoria_map con los mapeos de categorías por defecto';

    /**
     * Mapeo de descripciones a categorías
     */
    protected array $descripcionMap = [
        "CAMARA CCTV" => "CCTV",
        "CAMARA CCTV GIRATORIA" => "CCTV",
        "DISCO DURO EXTERNO" => "CCTV",
        "GRABADOR CCTV" => "CCTV",
        "INSTALACION CCTV" => "CCTV",
        "MICROFONO CCTV" => "CCTV",
        "REGULADOR" => "ENERGIA",
        "REGULADOR DE ENERGIA" => "ENERGIA",
        "REGULADOR/UPS/BATERIA" => "ENERGIA",
        "UPS P/ RESPALDO DE ENERGIA" => "ENERGIA",
        "HAND HELD P/PROCESOS" => "MOVILIDAD",
        "IMPRESORA P/PROCESOS" => "MOVILIDAD",
        "TABLETA ELECTRONICA P/PROCESOS" => "MOVILIDAD",
        "ESCANER - LECTOR" => "PUNTO DE VENTA",
        "ESCANER DE MANO" => "PUNTO DE VENTA",
        "ESCANER PARA ID" => "PUNTO DE VENTA",
        "IMPRESORA P/PUNTO DE VENTA" => "PUNTO DE VENTA",
        "MONITOR P/PUNTO DE VENTA (HP)" => "PUNTO DE VENTA",
        "PUNTO DE VENTA" => "PUNTO DE VENTA",
        "TELEFONO" => "PUNTO DE VENTA",
        "ACCESS POINT" => "TELCO",
        "GABINETE P/EQ. COMUNICACION TIENDA" => "TELCO",
        "RUTEADOR DE COMUNICACION" => "TELCO",
        "SWITCH RED LOCAL" => "TELCO",
        "TELECOMUNICACIONES TIENDA" => "TELCO",
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('Poblando tabla maf_categoria_map...');
        $this->newLine();

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($this->descripcionMap as $descripcionRaw => $categoria) {
            $descripcionKey = TextNorm::key($descripcionRaw);
            
            $existing = MafCategoriaMap::where('descripcion_key', $descripcionKey)->first();
            
            if ($existing) {
                if ($force) {
                    $existing->update([
                        'descripcion_raw' => $descripcionRaw,
                        'categoria' => $categoria,
                        'activo' => 1,
                    ]);
                    $updated++;
                    $this->line("  ✓ Actualizado: {$descripcionRaw} → {$categoria}");
                } else {
                    $skipped++;
                    $this->line("  - Omitido (ya existe): {$descripcionRaw}");
                }
            } else {
                MafCategoriaMap::create([
                    'descripcion_key' => $descripcionKey,
                    'descripcion_raw' => $descripcionRaw,
                    'categoria' => $categoria,
                    'activo' => 1,
                ]);
                $inserted++;
                $this->line("  + Insertado: {$descripcionRaw} → {$categoria}");
            }
        }

        $this->newLine();
        $this->info('Resumen:');
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Insertados', number_format($inserted)],
                ['Actualizados', number_format($updated)],
                ['Omitidos', number_format($skipped)],
                ['Total procesados', number_format(count($this->descripcionMap))],
            ]
        );

        $this->info('✓ Tabla maf_categoria_map poblada exitosamente.');
        
        return 0;
    }
}



