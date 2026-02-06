<?php

namespace App\Http\Controllers;

use App\Models\MafImportBatch;
use App\Models\MafCategoriaMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MafBatchController extends Controller
{
    public function index()
    {
        $batches = MafImportBatch::orderBy('created_at', 'desc')->paginate(10);
        return view('maf.batches.index', compact('batches'));
    }

    public function show(MafImportBatch $batch)
    {
        $batch->load(['mafs' => function($query) {
            $query->limit(100); // Mostrar solo muestra para no saturar
        }]);
        
        return view('maf.batches.show', compact('batch'));
    }

    public function exportCsv(MafImportBatch $batch)
    {
        $filename = 'reporte-lote-' . $batch->id . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($batch) {
            $file = fopen('php://output', 'w');
            
            // Encabezados del CSV (incluyendo categoría)
            fputcsv($file, [
                'ID', 'PLACA', 'SERIE', 'DESCRIPCION', 'MARCA', 'MODELO', 
                'CATEGORIA_SUGERIDA', 'ESTADO', 'ERROR', 'FECHA'
            ]);

            // Procesar en chunks para memoria
            $batch->mafs()->chunk(500, function($mafs) use ($file) {
                foreach ($mafs as $maf) {
                    // Buscar categoría si no la tiene
                    $categoria = $maf->categoria;
                    if (empty($categoria) && !empty($maf->descripcion)) {
                        $categoria = MafCategoriaMap::buscarCategoria($maf->descripcion);
                    }

                    fputcsv($file, [
                        $maf->id,
                        $maf->placa,
                        $maf->serie,
                        $maf->descripcion,
                        $maf->marca,
                        $maf->modelo,
                        $categoria, // Categoría
                        $maf->status,
                        $maf->error_message,
                        $maf->created_at
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Aplica categorías a todos los ítems del lote basado en la descripción
     */
    public function applyCategories(MafImportBatch $batch)
    {
        $count = 0;
        
        $batch->mafs()->chunk(500, function($mafs) use (&$count) {
            foreach ($mafs as $maf) {
                if (empty($maf->categoria) && !empty($maf->descripcion)) {
                    $categoria = MafCategoriaMap::buscarCategoria($maf->descripcion);
                    if ($categoria) {
                        $maf->categoria = $categoria;
                        $maf->save();
                        $count++;
                    }
                }
            }
        });
        
        return back()->with('success', "Se aplicaron categorías a {$count} registros correctamente.");
    }
}
