<?php

namespace App\Http\Controllers;

use App\Models\MafImportBatch;
use App\Services\MafImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;

class MafBatchController extends Controller
{
    public function index()
    {
        // Autoriza vía Gate directamente para evitar dependencias de traits en controladores
        \Illuminate\Support\Facades\Gate::authorize('view-maf-batches');

        $batches = MafImportBatch::with('uploadedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('maf.batches.index', compact('batches'));
    }

    public function show(MafImportBatch $batch, MafImportService $service)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-maf-batches');

        try {
            $report = $service->reportForBatch($batch->id);
        } catch (\Exception $e) {
            \Log::error("Error al generar reporte para batch {$batch->id}: " . $e->getMessage());
            // Retornar reporte vacío en caso de error
            $report = [
                'conflicts' => [
                    'placa' => [],
                    'activo' => [],
                    'serie' => [],
                ],
                'duplicates' => [
                    'placa' => [],
                    'activo' => [],
                    'serie' => [],
                ],
            ];
        }

        try {
            $batch->load('uploadedBy');
        } catch (\Exception $e) {
            \Log::warning("Error al cargar relación uploadedBy para batch {$batch->id}: " . $e->getMessage());
            // Continuar sin la relación si falla
        }

        return view('maf.batches.show', [
            'batch' => $batch,
            'report' => $report,
        ]);
    }

    public function exportCsv(MafImportBatch $batch, MafImportService $service)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-maf-batches');

        $report = $service->reportForBatch($batch->id);

        $filename = "maf-report-{$batch->period}-{$batch->id}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');
            
            // BOM para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'Tipo',
                'Identificador',
                'Valor',
                'Filas',
                'Tiendas Distintas',
                'Plazas Distintas',
                'Fila Excel',
                'Plaza',
                'CR',
                'Tienda',
                'Descripción',
                'Marca',
                'Modelo',
            ]);

            // Conflictos
            foreach (['placa', 'activo', 'serie'] as $identifier) {
                foreach ($report['conflicts'][$identifier] as $item) {
                    foreach ($item['occurrences'] as $occurrence) {
                        fputcsv($file, [
                            'CONFLICTO',
                            strtoupper($identifier),
                            $item['value'],
                            $item['rows_count'],
                            $item['tiendas_distintas'],
                            $item['plazas_distintas'],
                            $occurrence->row_num,
                            $occurrence->plaza ?? '',
                            $occurrence->cr ?? '',
                            $occurrence->tienda ?? '',
                            $occurrence->descripcion ?? '',
                            $occurrence->marca ?? '',
                            $occurrence->modelo ?? '',
                        ]);
                    }
                }
            }

            // Duplicados
            foreach (['placa', 'activo', 'serie'] as $identifier) {
                foreach ($report['duplicates'][$identifier] as $item) {
                    foreach ($item['occurrences'] as $occurrence) {
                        fputcsv($file, [
                            'DUPLICADO',
                            strtoupper($identifier),
                            $item['value'],
                            $item['rows_count'],
                            $item['tiendas_distintas'],
                            $item['plazas_distintas'],
                            $occurrence->row_num,
                            $occurrence->plaza ?? '',
                            $occurrence->cr ?? '',
                            $occurrence->tienda ?? '',
                            $occurrence->descripcion ?? '',
                            $occurrence->marca ?? '',
                            $occurrence->modelo ?? '',
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Aplicar categorías a un lote
     */
    public function applyCategories(MafImportBatch $batch)
    {
        \Illuminate\Support\Facades\Gate::authorize('admin');

        try {
            Artisan::call('maf:categorize', ['batch_id' => $batch->id]);
            $output = Artisan::output();

            return redirect()->route('maf.batches.show', $batch)
                ->with('success', 'Categorías aplicadas exitosamente. ' . trim($output));
        } catch (\Exception $e) {
            return redirect()->route('maf.batches.show', $batch)
                ->with('error', 'Error al aplicar categorías: ' . $e->getMessage());
        }
    }
}






