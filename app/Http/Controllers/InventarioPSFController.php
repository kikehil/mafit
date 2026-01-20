<?php

namespace App\Http\Controllers;

use App\Models\InventarioPSF;
use App\Models\Maf;
use App\Models\MafImportBatch;
use App\Models\Plaza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioPSFController extends Controller
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
     * Mostrar formulario de captura PFS
     */
    public function captura()
    {
        return view('inventario-pfs.captura');
    }

    /**
     * Buscar equipo por placa o serie en MAF
     */
    public function buscarEquipo(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        $query = trim($request->input('query'));

        // Obtener el último batch "done"
        $lastBatch = MafImportBatch::where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->first();

        if (!$lastBatch) {
            return response()->json([
                'success' => false,
                'message' => 'No hay lotes procesados disponibles.',
            ]);
        }

        // Buscar por PLACA o SERIE (case-insensitive, sin espacios)
        $cleanQuery = strtoupper(preg_replace('/\s+/', '', $query));
        
        // Generar variaciones de placa con y sin ceros iniciales
        $variacionesPlaca = $this->generarVariacionesPlaca($cleanQuery);
        
        $mafQuery = Maf::where('batch_id', $lastBatch->id)
            ->where(function ($q) use ($cleanQuery, $variacionesPlaca) {
                // Buscar por placa con variaciones
                foreach ($variacionesPlaca as $variacion) {
                    $q->orWhereRaw("UPPER(REPLACE(placa, ' ', '')) LIKE ?", ["%{$variacion}%"]);
                }
                // Buscar por serie
                $q->orWhereRaw("UPPER(REPLACE(serie, ' ', '')) LIKE ?", ["%{$cleanQuery}%"]);
            });

        $equipo = $mafQuery->with(['plazaRelation'])->first();

        if ($equipo) {
            // Cargar la relación de plaza si no está cargada
            if (!$equipo->relationLoaded('plazaRelation') && $equipo->plaza) {
                $equipo->load('plazaRelation');
            }
            
            // Obtener nombre de plaza
            $nombrePlaza = '';
            if ($equipo->plazaRelation) {
                $nombrePlaza = $equipo->plazaRelation->plaza_nom ?? '';
            } elseif ($equipo->plaza) {
                // Si no hay relación, intentar obtener directamente
                $plaza = Plaza::where('plaza', $equipo->plaza)->first();
                $nombrePlaza = $plaza->plaza_nom ?? '';
            }
            
            return response()->json([
                'success' => true,
                'encontrado' => true,
                'equipo' => [
                    'maf_id' => $equipo->id,
                    'plaza' => $equipo->plaza ?? '',
                    'nombre_plaza' => $nombrePlaza,
                    'cr' => $equipo->cr ?? '',
                    'nombre_tienda' => $equipo->tienda ?? '',
                    'placa' => $equipo->placa ?? '',
                    'marca' => $equipo->marca ?? '',
                    'modelo' => $equipo->modelo ?? '',
                    'serie' => $equipo->serie ?? '',
                    'activo' => $equipo->activo ?? '',
                    'anocompra' => $equipo->anocompra ?? null,
                    'valor_neto' => $equipo->valor_neto ?? null,
                    'remanente' => $equipo->remanente ?? null,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'encontrado' => false,
            'message' => 'Equipo no encontrado en MAF. Puede capturar los datos manualmente.',
        ]);
    }

    /**
     * Guardar inventario PFS
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'maf_id' => 'nullable|exists:maf,id',
            'placa' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serie' => 'nullable|string|max:100',
            'activo' => 'nullable|string|max:100',
            'anocompra' => 'nullable|integer|min:1900|max:' . date('Y'),
            'valor_neto' => 'nullable|numeric|min:0',
            'remanente' => 'nullable|numeric|min:0',
            'ubicacion' => 'required|string|in:BODEGA,PS ERICK,PS OSCAR,PS ALFREDO,PS ROBERTO1,PS ROBERTO 2,PS SAMUEL,OFF. VALLES,OFF TAMPICO',
        ]);

        try {
            $data = $request->only([
                'maf_id', 'placa', 'marca', 'modelo', 'serie', 'activo',
                'anocompra', 'valor_neto', 'remanente', 'ubicacion'
            ]);

            // Si tiene maf_id, obtener datos adicionales
            if ($data['maf_id']) {
                $maf = Maf::with('plazaRelation')->find($data['maf_id']);
                if ($maf) {
                    $data['plaza'] = $maf->plaza;
                    $data['nombre_plaza'] = $maf->plazaRelation?->plaza_nom ?? '';
                    $data['cr'] = $maf->cr;
                    $data['nombre_tienda'] = $maf->tienda ?? '';
                    $data['encontrado_en_maf'] = true;
                    
                    // Si no se proporcionaron, usar valores de MAF
                    $data['placa'] = $data['placa'] ?? $maf->placa;
                    $data['marca'] = $data['marca'] ?? $maf->marca;
                    $data['modelo'] = $data['modelo'] ?? $maf->modelo;
                    $data['serie'] = $data['serie'] ?? $maf->serie;
                    $data['activo'] = $data['activo'] ?? $maf->activo;
                    $data['anocompra'] = $data['anocompra'] ?? $maf->anocompra;
                    $data['valor_neto'] = $data['valor_neto'] ?? $maf->valor_neto;
                    $data['remanente'] = $data['remanente'] ?? $maf->remanente;
                }
            } else {
                $data['encontrado_en_maf'] = false;
            }

            $data['user_id'] = auth()->id();

            InventarioPSF::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Inventario PFS guardado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar consulta de inventario PFS
     */
    public function consulta()
    {
        $equipos = InventarioPSF::with('user')
            ->where('activo_registro', true)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('inventario-pfs.consulta', compact('equipos'));
    }

    /**
     * Obtener equipos inventariados (AJAX)
     */
    public function obtenerEquipos(Request $request)
    {
        $ubicacion = $request->input('ubicacion');
        
        $query = InventarioPSF::with('user')
            ->where('activo_registro', true)
            ->orderBy('created_at', 'desc');
        
        if ($ubicacion) {
            $query->where('ubicacion', $ubicacion);
        }
        
        $equipos = $query->paginate(50);
        
        // Convertir a array para JSON
        $equiposArray = $equipos->map(function($equipo) {
            return [
                'id' => $equipo->id,
                'placa' => $equipo->placa,
                'marca' => $equipo->marca,
                'modelo' => $equipo->modelo,
                'serie' => $equipo->serie,
                'activo' => $equipo->activo,
                'ubicacion' => $equipo->ubicacion,
                'created_at' => $equipo->created_at ? $equipo->created_at->toDateTimeString() : null,
                'user' => $equipo->user ? ['name' => $equipo->user->name] : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'equipos' => $equiposArray,
            'pagination' => [
                'current_page' => $equipos->currentPage(),
                'last_page' => $equipos->lastPage(),
                'per_page' => $equipos->perPage(),
                'total' => $equipos->total(),
            ],
        ]);
    }

    /**
     * Actualizar ubicación de un equipo
     */
    public function actualizarUbicacion(Request $request, InventarioPSF $inventario)
    {
        $request->validate([
            'ubicacion' => 'required|string|in:BODEGA,PS ERICK,PS OSCAR,PS ALFREDO,PS ROBERTO1,PS ROBERTO 2,PS SAMUEL,OFF. VALLES,OFF TAMPICO',
        ]);

        try {
            $inventario->ubicacion = $request->ubicacion;
            $inventario->save();

            return response()->json([
                'success' => true,
                'message' => 'Ubicación actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar ubicación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dar de baja un equipo
     */
    public function darDeBaja(Request $request, InventarioPSF $inventario)
    {
        $request->validate([
            'notas' => 'required|string|max:1000',
        ]);

        try {
            $inventario->activo_registro = false;
            $inventario->notas = $request->notas;
            $inventario->save();

            return response()->json([
                'success' => true,
                'message' => 'Equipo dado de baja correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de baja: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar movimientos de inventario PFS
     */
    public function movimientos()
    {
        return view('inventario-pfs.movimientos');
    }
}
