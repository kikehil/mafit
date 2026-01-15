<?php

namespace App\Http\Controllers;

use App\Models\Maf;
use App\Models\MafImportBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MafSearchController extends Controller
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
     * Muestra el formulario de búsqueda
     */
    public function index()
    {
        // Obtener el último lote "done" por defecto
        $lastBatch = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->first();

        return view('maf.search.index', [
            'lastBatch' => $lastBatch,
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
            
            return view('maf.search.index', [
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

        $results = $mafQuery->with('batch')
            ->orderBy('cr')
            ->orderBy('placa')
            ->get();

        // Obtener todos los lotes "done" para el selector
        $batches = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->get();

        // Obtener el batch actual
        $currentBatch = $batchId ? MafImportBatch::find($batchId) : null;

        return view('maf.search.index', [
            'results' => $results,
            'query' => $query,
            'batches' => $batches,
            'currentBatch' => $currentBatch,
            'lastBatch' => $batches->first(),
        ]);
    }
}
