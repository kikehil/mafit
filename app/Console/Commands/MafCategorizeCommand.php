<?php

namespace App\Console\Commands;

use App\Helpers\TextNorm;
use App\Models\Maf;
use App\Models\MafCategoriaMap;
use App\Models\MafImportBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MafCategorizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maf:categorize {batch_id : ID del lote a categorizar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aplica categorías a los registros MAF de un lote basándose en el catálogo de categorías';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchId = $this->argument('batch_id');

        // Verificar que el lote existe
        $batch = MafImportBatch::find($batchId);
        if (!$batch) {
            $this->error("No se encontró el lote con ID: {$batchId}");
            return 1;
        }

        $this->info("Categorizando lote: {$batch->period} (ID: {$batchId})");

        // Cargar mapa de categorías en memoria (solo activos)
        $this->info("Cargando catálogo de categorías...");
        $categoriaMap = MafCategoriaMap::where('activo', 1)
            ->pluck('categoria', 'descripcion_key')
            ->toArray();

        $this->info("Catálogo cargado: " . count($categoriaMap) . " categorías activas");

        if (empty($categoriaMap)) {
            $this->warn("No hay categorías activas en el catálogo. No se puede categorizar.");
            return 1;
        }

        // Procesar registros MAF del lote en chunks
        $this->info("Procesando registros MAF...");
        $processed = 0;
        $categorized = 0;
        $notFound = 0;

        Maf::where('batch_id', $batchId)
            ->whereNotNull('descripcion')
            ->chunk(500, function ($mafs) use (&$processed, &$categorized, &$notFound, $categoriaMap) {
                foreach ($mafs as $maf) {
                    $processed++;
                    
                    // Normalizar descripción
                    $descripcionKey = TextNorm::key($maf->descripcion);
                    
                    // Buscar categoría en el mapa
                    if (isset($categoriaMap[$descripcionKey])) {
                        $maf->categoria = $categoriaMap[$descripcionKey];
                        $maf->save();
                        $categorized++;
                    } else {
                        $notFound++;
                    }
                }
            });

        $this->info("Procesamiento completado:");
        $this->info("  - Total procesados: {$processed}");
        $this->info("  - Categorizados: {$categorized}");
        $this->info("  - Sin categoría encontrada: {$notFound}");

        return 0;
    }
}
