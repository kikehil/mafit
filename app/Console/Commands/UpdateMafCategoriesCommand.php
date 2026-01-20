<?php

namespace App\Console\Commands;

use App\Helpers\TextNorm;
use App\Models\Maf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMafCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maf:update-categories 
                            {--dry-run : Ejecutar sin hacer cambios, solo mostrar lo que se haría}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la columna categoria en maf basándose en el mapeo de descripciones';

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
     * Mapeo normalizado (descripcion_key => categoria)
     */
    protected array $normalizedMap = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODO DRY-RUN: No se realizarán cambios en la base de datos.');
        }

        // Normalizar el mapeo
        $this->info('Normalizando mapeo de descripciones...');
        foreach ($this->descripcionMap as $descripcion => $categoria) {
            $key = TextNorm::key($descripcion);
            $this->normalizedMap[$key] = $categoria;
        }

        $this->info('Mapeo normalizado: ' . count($this->normalizedMap) . ' entradas');
        $this->newLine();

        // Contadores
        $total = 0;
        $updated = 0;
        $skipped = 0;
        $notFound = 0;

        // Procesar registros en chunks
        $this->info('Procesando registros MAF...');
        $bar = $this->output->createProgressBar();
        $bar->start();

        $normalizedMap = $this->normalizedMap;
        
        Maf::whereNotNull('descripcion')
            ->chunk(500, function ($mafs) use (&$total, &$updated, &$skipped, &$notFound, $dryRun, $bar, $normalizedMap) {
                foreach ($mafs as $maf) {
                    $total++;
                    
                    // Normalizar descripción del registro
                    $descripcionKey = TextNorm::key($maf->descripcion);
                    
                    // Buscar en el mapeo normalizado
                    if (isset($normalizedMap[$descripcionKey])) {
                        $categoria = $normalizedMap[$descripcionKey];
                        
                        // Solo actualizar si es diferente
                        if ($maf->categoria !== $categoria) {
                            if (!$dryRun) {
                                $maf->categoria = $categoria;
                                $maf->save();
                            }
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        $notFound++;
                    }
                    
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->info('Resumen:');
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Total procesados', number_format($total)],
                ['Actualizados', number_format($updated)],
                ['Ya tenían categoría correcta', number_format($skipped)],
                ['No encontrados en mapeo', number_format($notFound)],
            ]
        );

        if ($dryRun) {
            $this->warn('Este fue un DRY-RUN. Ejecuta sin --dry-run para aplicar los cambios.');
        } else {
            $this->info('✓ Actualización completada exitosamente.');
        }

        return 0;
    }
}
