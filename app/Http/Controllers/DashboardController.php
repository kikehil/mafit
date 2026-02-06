<?php

namespace App\Http\Controllers;

use App\Helpers\TextNorm;
use App\Models\Maf;
use App\Models\MafCategoriaMap;
use App\Models\MafImportBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Generar variaciones de placa con y sin ceros iniciales
     */
    private function generarVariacionesPlaca($placa)
    {
        $variaciones = [$placa];
        
        // Si la placa comienza con cero, agregar versión sin cero
        if (preg_match('/^0+/', $placa)) {
            $sinCeros = ltrim($placa, '0');
            if ($sinCeros !== '') {
                $variaciones[] = $sinCeros;
            }
        } else {
            // Si no comienza con cero, agregar versiones con ceros iniciales (hasta 3 ceros)
            for ($i = 1; $i <= 3; $i++) {
                $variaciones[] = str_repeat('0', $i) . $placa;
            }
        }
        
        return array_unique($variaciones);
    }

    /**
     * Muestra el dashboard principal (buscador tipo Google)
     */
    public function index()
    {
        // Obtener el último lote "done" por defecto
        $lastBatch = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->first();

        // Obtener todos los lotes "done" para el selector
        $batches = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->get();

        return view('dashboard.index', [
            'lastBatch' => $lastBatch,
            'batches' => $batches,
        ]);
    }

    /**
     * Realiza la búsqueda por PLACA o SERIE
     * Lógica mejorada: Busca globalmente si no se especifica lote.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
            'batch_id' => 'nullable|exists:maf_import_batches,id',
        ]);

        $query = trim($request->input('query'));
        $batchId = $request->input('batch_id');

        // Obtener historial de lotes para el selector
        $batches = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->get();

        // 1. Configurar consulta base
        $mafQuery = Maf::query();

        // 2. Filtrar por lote SOLO si se especificó explícitamente
        if ($batchId) {
            $mafQuery->where('batch_id', $batchId);
            $currentBatch = MafImportBatch::find($batchId);
        } else {
            // Búsqueda GLOBAL (ignoramos batch_id)
            $currentBatch = null;
        }

        // 3. Preparar términos de búsqueda
        $cleanQuery = strtoupper(preg_replace('/\s+/', '', $query));
        $variacionesPlaca = $this->generarVariacionesPlaca($cleanQuery);
        
        // 4. Aplicar filtros de Placa/Serie usando la lógica de Movimientos
        $mafQuery->where(function ($q) use ($cleanQuery, $variacionesPlaca) {
            // Coincidencia exacta en lista de variaciones
            $q->whereIn('placa', $variacionesPlaca)
              ->orWhere('serie', $cleanQuery);

            // Búsqueda flexible (LIKE) para parciales
            foreach ($variacionesPlaca as $variacion) {
                $q->orWhere('placa', 'LIKE', '%' . $variacion . '%');
            }
            $q->orWhere('serie', 'LIKE', '%' . $cleanQuery . '%');
        });

        // 5. Ejecutar consulta
        $results = $mafQuery->with(['batch', 'plazaRelation'])
            ->orderBy('imported_at', 'desc') // Priorizar lo más reciente
            ->orderBy('placa')
            ->limit(50)
            ->get();

        // 6. Calcular categorías faltantes
        foreach ($results as $result) {
            if (empty($result->categoria) && !empty($result->descripcion)) {
                $result->categoria = MafCategoriaMap::buscarCategoria($result->descripcion);
            }
        }

        return view('dashboard.index', [
            'results' => $results,
            'query' => $query,
            'batches' => $batches,
            'currentBatch' => $currentBatch,
            'lastBatch' => $batches->first(),
        ]);
    }
}
