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
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
            'batch_id' => 'nullable|exists:maf_import_batches,id',
        ]);

        $query = trim($request->input('query'));
        $batchId = $request->input('batch_id');

        // Si no se especifica batch, usar el último "done"
        if (!$batchId) {
            $lastBatch = MafImportBatch::where('status', 'done')
                ->orderBy('finished_at', 'desc')
                ->first();
            $batchId = $lastBatch?->id;
        }

        // Si no hay batch disponible, retornar sin resultados
        if (!$batchId) {
            $batches = MafImportBatch::where('status', 'done')
                ->orderBy('finished_at', 'desc')
                ->get();
            
            return view('dashboard.index', [
                'results' => collect([]),
                'query' => $query,
                'batches' => $batches,
                'currentBatch' => null,
                'lastBatch' => $batches->first(),
            ]);
        }

        // Construir la consulta
        $mafQuery = Maf::query();
        $mafQuery->where('batch_id', $batchId);

        // Buscar por PLACA o SERIE (case-insensitive, sin espacios)
        $cleanQuery = strtoupper(preg_replace('/\s+/', '', $query));
        
        // Generar variaciones de placa con y sin ceros iniciales
        $variacionesPlaca = $this->generarVariacionesPlaca($cleanQuery);
        
        $mafQuery->where(function ($q) use ($cleanQuery, $variacionesPlaca) {
            // Buscar por placa con variaciones
            foreach ($variacionesPlaca as $variacion) {
                $q->orWhereRaw("UPPER(REPLACE(placa, ' ', '')) LIKE ?", ["%{$variacion}%"]);
            }
            // Buscar por serie
            $q->orWhereRaw("UPPER(REPLACE(serie, ' ', '')) LIKE ?", ["%{$cleanQuery}%"]);
        });

        $results = $mafQuery->with(['batch', 'plazaRelation'])
            ->orderBy('cr')
            ->orderBy('placa')
            ->get();

        // Calcular categoría on the fly si no está guardada (modo A)
        foreach ($results as $result) {
            if (empty($result->categoria) && !empty($result->descripcion)) {
                $result->categoria = MafCategoriaMap::buscarCategoria($result->descripcion);
            }
        }

        // Obtener todos los lotes "done" para el selector
        $batches = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->get();

        // Obtener el batch actual
        $currentBatch = $batchId ? MafImportBatch::find($batchId) : null;

        return view('dashboard.index', [
            'results' => $results,
            'query' => $query,
            'batches' => $batches,
            'currentBatch' => $currentBatch,
            'lastBatch' => $batches->first(),
        ]);
    }
}
