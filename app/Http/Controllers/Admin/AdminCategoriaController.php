<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\TextNorm;
use App\Models\MafCategoriaMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MafCategoriaMap::query();

        // Búsqueda por descripcion_raw o categoria
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('descripcion_raw', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        // Filtro por activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        $categorias = $query->orderBy('categoria')
            ->orderBy('descripcion_raw')
            ->paginate(20);

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_raw' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:80'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $descripcionKey = TextNorm::key($validated['descripcion_raw']);

        // Buscar si ya existe
        $existing = MafCategoriaMap::where('descripcion_key', $descripcionKey)->first();

        if ($existing) {
            // Actualizar registro existente
            $existing->update([
                'descripcion_raw' => $validated['descripcion_raw'],
                'categoria' => $validated['categoria'],
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()->route('admin.categorias.index')
                ->with('success', 'Categoría actualizada exitosamente (ya existía con la misma clave normalizada).');
        }

        // Crear nuevo registro
        MafCategoriaMap::create([
            'descripcion_key' => $descripcionKey,
            'descripcion_raw' => $validated['descripcion_raw'],
            'categoria' => $validated['categoria'],
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MafCategoriaMap $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MafCategoriaMap $categoria)
    {
        $validated = $request->validate([
            'descripcion_raw' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:80'],
            'activo' => ['nullable', 'boolean'],
        ]);

        // Si cambió descripcion_raw, recalcular descripcion_key
        if ($categoria->descripcion_raw !== $validated['descripcion_raw']) {
            $newKey = TextNorm::key($validated['descripcion_raw']);
            
            // Verificar si la nueva clave ya existe en otro registro
            $existing = MafCategoriaMap::where('descripcion_key', $newKey)
                ->where('id', '!=', $categoria->id)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe otra categoría con la misma descripción normalizada.');
            }

            $categoria->descripcion_key = $newKey;
        }

        $categoria->descripcion_raw = $validated['descripcion_raw'];
        $categoria->categoria = $validated['categoria'];
        $categoria->activo = $request->has('activo') ? 1 : 0;
        $categoria->save();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MafCategoriaMap $categoria)
    {
        // Soft delete: marcar como inactivo en lugar de borrar
        $categoria->activo = 0;
        $categoria->save();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría desactivada exitosamente.');
    }

    /**
     * Mostrar formulario de importación
     */
    public function showImport()
    {
        return view('admin.categorias.import');
    }

    /**
     * Procesar importación desde Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // 10MB max
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return redirect()->back()
                    ->with('error', 'El archivo está vacío o no tiene datos.');
            }

            // Asumir primera fila como encabezados
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            $descripcionIndex = null;
            $categoriaIndex = null;

            foreach ($headers as $index => $header) {
                if (in_array($header, ['descripcion', 'descripción', 'description'])) {
                    $descripcionIndex = $index;
                }
                if (in_array($header, ['categoria', 'categoría', 'category'])) {
                    $categoriaIndex = $index;
                }
            }

            if ($descripcionIndex === null || $categoriaIndex === null) {
                return redirect()->back()
                    ->with('error', 'No se encontraron las columnas "descripcion" y "categoria" en el archivo.');
            }

            $inserted = 0;
            $updated = 0;
            $rejected = 0;

            DB::beginTransaction();
            try {
                // Procesar filas (saltar encabezado)
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $descripcion = trim($row[$descripcionIndex] ?? '');
                    $categoria = trim($row[$categoriaIndex] ?? '');

                    // Rechazar si están vacíos
                    if (empty($descripcion) || empty($categoria)) {
                        $rejected++;
                        continue;
                    }

                    $descripcionKey = TextNorm::key($descripcion);

                    // Buscar si ya existe
                    $existing = MafCategoriaMap::where('descripcion_key', $descripcionKey)->first();

                    if ($existing) {
                        $existing->update([
                            'descripcion_raw' => $descripcion,
                            'categoria' => $categoria,
                            'activo' => 1,
                        ]);
                        $updated++;
                    } else {
                        MafCategoriaMap::create([
                            'descripcion_key' => $descripcionKey,
                            'descripcion_raw' => $descripcion,
                            'categoria' => $categoria,
                            'activo' => 1,
                        ]);
                        $inserted++;
                    }
                }

                DB::commit();

                return redirect()->route('admin.categorias.index')
                    ->with('success', "Importación completada: {$inserted} insertados, {$updated} actualizados, {$rejected} rechazados.");

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }
    }
}
