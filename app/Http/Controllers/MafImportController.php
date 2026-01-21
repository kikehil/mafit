<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMafImport;
use App\Models\MafImportBatch;
use App\Services\MafImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MafImportController extends Controller
{
    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('import-maf');

        return view('maf.import');
    }

    public function store(Request $request, MafImportService $service)
    {
        \Illuminate\Support\Facades\Gate::authorize('import-maf');

        $validated = $request->validate([
            'period' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:51200'], // 50MB
        ]);

        // Guardar archivo
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('maf-imports', 'local');

        // Crear batch
        $batch = MafImportBatch::create([
            'period' => $validated['period'],
            'filename' => $filename,
            'uploaded_by' => auth()->id(),
            'status' => 'processing',
            'started_at' => now(),
        ]);

        // Procesar (síncrono por ahora, pero estructura lista para async)
        try {
            $fullPath = Storage::path($path);
            $service->importBatch($batch, $fullPath);
        } catch (\Exception $e) {
            return redirect()
                ->route('maf.batches.show', $batch)
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }

        return redirect()
            ->route('maf.batches.show', $batch)
            ->with('success', 'Archivo procesado correctamente');
    }
}






