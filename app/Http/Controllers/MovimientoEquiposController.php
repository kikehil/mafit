<?php

namespace App\Http\Controllers;

use App\Models\Inventariotda;
use App\Models\Maf;
use App\Models\Movimiento;
use App\Models\Plaza;
use App\Models\Tienda;
use App\Mail\MovimientoEquipoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MovimientoEquiposController extends Controller
{
    /**
     * Mostrar vista principal de movimientos de equipos
     */
    public function index()
    {
        return view('movimientos.index');
    }

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
     * Buscar equipo por placa o serie
     * Busca primero en inventariotda, luego en maf
     */
    public function buscarEquipo(Request $request)
    {
        $request->validate([
            'placa_serie' => 'required|string|min:1|max:100',
        ]);

        $placaSerie = trim($request->input('placa_serie'));
        $variacionesPlaca = $this->generarVariacionesPlaca($placaSerie);

        // Buscar primero en inventariotda
        $inventario = Inventariotda::where(function ($q) use ($placaSerie, $variacionesPlaca) {
                $q->whereIn('placa_editada', $variacionesPlaca)
                  ->orWhereIn('serie_editada', $variacionesPlaca)
                  ->orWhere('serie_editada', $placaSerie);
            })
            ->with('maf')
            ->latest('fecha_inventario')
            ->first();

        if ($inventario) {
            $maf = $inventario->maf;
            return response()->json([
                'success' => true,
                'encontrado_en' => 'inventariotda',
                'inventariotda_id' => $inventario->id,
                'maf_id' => $maf ? $maf->id : null,
                'equipo' => [
                    'descripcion' => $inventario->placa_editada ? ($maf->descripcion ?? $inventario->marca_editada) : ($maf->descripcion ?? ''),
                    'marca' => $inventario->marca_editada ?? ($maf->marca ?? ''),
                    'modelo' => $inventario->modelo_editado ?? ($maf->modelo ?? ''),
                    'serie' => $inventario->serie_editada ?? ($maf->serie ?? ''),
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'placa' => $inventario->placa_editada ?? ($maf->placa ?? ''),
                    'cr' => $inventario->cr ?? ($maf->cr ?? ''),
                    'tienda' => $inventario->tienda ?? ($maf->tienda ?? ''),
                ],
            ]);
        }

        // Si no está en inventariotda, buscar en maf
        $maf = Maf::where(function ($q) use ($placaSerie, $variacionesPlaca) {
                $q->whereIn('placa', $variacionesPlaca)
                  ->orWhere('serie', $placaSerie);
            })
            ->latest('imported_at')
            ->first();

        if ($maf) {
            return response()->json([
                'success' => true,
                'encontrado_en' => 'maf',
                'inventariotda_id' => null,
                'maf_id' => $maf->id,
                'equipo' => [
                    'descripcion' => $maf->descripcion ?? '',
                    'marca' => $maf->marca ?? '',
                    'modelo' => $maf->modelo ?? '',
                    'serie' => $maf->serie ?? '',
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'placa' => $maf->placa ?? '',
                    'cr' => $maf->cr ?? '',
                    'tienda' => $maf->tienda ?? '',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se encontró el equipo con esa placa o serie.',
        ]);
    }

    /**
     * Buscar tienda por CR o nombre para agregar equipo
     */
    public function buscarTienda(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        $query = trim($request->input('query'));

        // Buscar en maf
        $tiendas = Maf::whereNotNull('cr')
            ->where(function ($q) use ($query) {
                $q->where('cr', 'like', "%{$query}%")
                  ->orWhere('tienda', 'like', "%{$query}%");
            })
            ->select('cr', 'tienda', 'plaza')
            ->distinct()
            ->get()
            ->unique(function ($item) {
                return $item->cr . $item->plaza;
            })
            ->take(10)
            ->map(function ($item) {
                $plaza = Plaza::where('plaza', $item->plaza)->first();
                return [
                    'cr' => $item->cr,
                    'tienda' => $item->tienda,
                    'plaza' => $item->plaza,
                    'nombre_plaza' => $plaza ? $plaza->plaza_nom : null,
                ];
            });

        if ($tiendas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron tiendas con ese criterio.',
            ]);
        }

        return response()->json([
            'success' => true,
            'tiendas' => $tiendas->values(),
        ]);
    }

    /**
     * Buscar equipo de remplazo por placa o serie
     */
    public function buscarEquipoRemplazo(Request $request)
    {
        $request->validate([
            'placa_serie' => 'required|string|min:1|max:100',
        ]);

        $placaSerie = trim($request->input('placa_serie'));
        $variacionesPlaca = $this->generarVariacionesPlaca($placaSerie);

        // Buscar primero en inventariotda
        $inventario = Inventariotda::where(function ($q) use ($placaSerie, $variacionesPlaca) {
                $q->whereIn('placa_editada', $variacionesPlaca)
                  ->orWhereIn('serie_editada', $variacionesPlaca)
                  ->orWhere('serie_editada', $placaSerie);
            })
            ->with('maf')
            ->latest('fecha_inventario')
            ->first();

        if ($inventario) {
            $maf = $inventario->maf;
            return response()->json([
                'success' => true,
                'encontrado_en' => 'inventariotda',
                'inventariotda_id' => $inventario->id,
                'maf_id' => $maf ? $maf->id : null,
                'equipo' => [
                    'descripcion' => $inventario->placa_editada ? ($maf->descripcion ?? $inventario->marca_editada) : ($maf->descripcion ?? ''),
                    'marca' => $inventario->marca_editada ?? ($maf->marca ?? ''),
                    'modelo' => $inventario->modelo_editado ?? ($maf->modelo ?? ''),
                    'serie' => $inventario->serie_editada ?? ($maf->serie ?? ''),
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'placa' => $inventario->placa_editada ?? ($maf->placa ?? ''),
                ],
            ]);
        }

        // Si no está en inventariotda, buscar en maf
        $maf = Maf::where(function ($q) use ($placaSerie, $variacionesPlaca) {
                $q->whereIn('placa', $variacionesPlaca)
                  ->orWhere('serie', $placaSerie);
            })
            ->latest('imported_at')
            ->first();

        if ($maf) {
            return response()->json([
                'success' => true,
                'encontrado_en' => 'maf',
                'inventariotda_id' => null,
                'maf_id' => $maf->id,
                'equipo' => [
                    'descripcion' => $maf->descripcion ?? '',
                    'marca' => $maf->marca ?? '',
                    'modelo' => $maf->modelo ?? '',
                    'serie' => $maf->serie ?? '',
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'placa' => $maf->placa ?? '',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se encontró el equipo con esa placa o serie.',
        ]);
    }

    /**
     * Validar si la tienda tiene inventario reciente (< 6 meses)
     */
    public function validarInventario(Request $request)
    {
        $request->validate([
            'cr' => 'required|string',
        ]);

        $cr = $request->input('cr');

        $ultimoInventario = Inventariotda::where('cr', $cr)
            ->latest('fecha_inventario')
            ->first();

        if (!$ultimoInventario) {
            return response()->json([
                'success' => true,
                'tiene_inventario_reciente' => false,
                'mensaje' => "No se ha realizado el inventario de equipo a la tienda.",
            ]);
        }

        $fechaInventario = Carbon::parse($ultimoInventario->fecha_inventario);
        $hace6Meses = Carbon::now()->subMonths(6);

        if ($fechaInventario->lt($hace6Meses)) {
            $nombreTienda = $ultimoInventario->tienda ?? $cr;
            return response()->json([
                'success' => true,
                'tiene_inventario_reciente' => false,
                'mensaje' => "No se ha realizado el inventario de equipo a la tienda ({$nombreTienda}) en los últimos 6 meses.",
                'ultimo_inventario' => $fechaInventario->format('d/m/Y'),
            ]);
        }

        return response()->json([
            'success' => true,
            'tiene_inventario_reciente' => true,
            'ultimo_inventario' => $fechaInventario->format('d/m/Y'),
        ]);
    }

    /**
     * Guardar movimiento de equipo
     */
    public function guardarMovimiento(Request $request)
    {
        $request->validate([
            'tipo_movimiento' => 'required|in:retiro,remplazo_dano,remplazo_renovacion,agregar,reingreso_garantia',
            'equipo_retirado_placa' => 'required_if:tipo_movimiento,retiro,remplazo_dano,remplazo_renovacion,reingreso_garantia|string|max:100',
            'equipo_retirado_serie' => 'nullable|string|max:100',
            'equipo_retirado_inventariotda_id' => 'nullable|exists:inventariotda,id',
            'equipo_retirado_maf_id' => 'nullable|exists:maf,id',
            'motivo' => 'required_if:tipo_movimiento,retiro,remplazo_dano,remplazo_renovacion|string',
            'seguimiento' => 'required_if:tipo_movimiento,retiro,remplazo_dano|in:baja,garantia',
            'equipo_remplazo_placa' => 'required_if:tipo_movimiento,remplazo_dano,remplazo_renovacion|string|max:100',
            'equipo_remplazo_serie' => 'nullable|string|max:100',
            'equipo_remplazo_inventariotda_id' => 'nullable|exists:inventariotda,id',
            'equipo_remplazo_maf_id' => 'nullable|exists:maf,id',
            'equipo_remplazo_descripcion' => 'required_if:tipo_movimiento,remplazo_dano,remplazo_renovacion|string|max:500',
            'equipo_remplazo_marca' => 'required_if:tipo_movimiento,remplazo_dano,remplazo_renovacion|string|max:100',
            'equipo_remplazo_modelo' => 'required_if:tipo_movimiento,remplazo_dano,remplazo_renovacion|string|max:100',
            'equipo_remplazo_activo' => 'nullable|string|max:100',
            'equipo_remplazo_remanente' => 'nullable|numeric',
            'cr' => 'required_if:tipo_movimiento,agregar|string|max:50',
            'tienda' => 'nullable|string|max:255',
            'plaza' => 'required_if:tipo_movimiento,agregar|string|max:20',
            'nombre_plaza' => 'nullable|string|max:255',
            'equipo_agregado_placa' => 'required_if:tipo_movimiento,agregar|string|max:100',
            'equipo_agregado_serie' => 'nullable|string|max:100',
            'equipo_agregado_descripcion' => 'required_if:tipo_movimiento,agregar|string|max:500',
            'equipo_agregado_marca' => 'required_if:tipo_movimiento,agregar|string|max:100',
            'equipo_agregado_modelo' => 'required_if:tipo_movimiento,agregar|string|max:100',
            'equipo_agregado_activo' => 'nullable|string|max:100',
            'equipo_agregado_remanente' => 'nullable|numeric',
            'comentarios' => 'nullable|string',
            'realizo_inventario' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $tipoMovimiento = $request->input('tipo_movimiento');
            $movimientoData = [
                'user_id' => $user->id,
                'tipo_movimiento' => $tipoMovimiento,
                'realizo_inventario' => $request->input('realizo_inventario', false),
            ];

            // Procesar según el tipo de movimiento
            if (in_array($tipoMovimiento, ['retiro', 'remplazo_dano', 'remplazo_renovacion', 'reingreso_garantia'])) {
                // Obtener datos del equipo retirado
                $equipoRetirado = $this->obtenerDatosEquipo(
                    $request->input('equipo_retirado_inventariotda_id'),
                    $request->input('equipo_retirado_maf_id')
                );

                $movimientoData = array_merge($movimientoData, [
                    'equipo_retirado_placa' => $equipoRetirado['placa'] ?? $request->input('equipo_retirado_placa'),
                    'equipo_retirado_serie' => $equipoRetirado['serie'] ?? $request->input('equipo_retirado_serie'),
                    'equipo_retirado_descripcion' => $equipoRetirado['descripcion'] ?? '',
                    'equipo_retirado_marca' => $equipoRetirado['marca'] ?? '',
                    'equipo_retirado_modelo' => $equipoRetirado['modelo'] ?? '',
                    'equipo_retirado_activo' => $equipoRetirado['activo'] ?? '',
                    'equipo_retirado_remanente' => $equipoRetirado['remanente'] ?? 0,
                    'equipo_retirado_inventariotda_id' => $request->input('equipo_retirado_inventariotda_id'),
                    'equipo_retirado_maf_id' => $request->input('equipo_retirado_maf_id'),
                    'cr' => $equipoRetirado['cr'] ?? '',
                    'tienda' => $equipoRetirado['tienda'] ?? '',
                    'motivo' => $request->input('motivo'),
                ]);

                // Para retiro y remplazo por daño, agregar seguimiento
                if (in_array($tipoMovimiento, ['retiro', 'remplazo_dano'])) {
                    $movimientoData['seguimiento'] = $request->input('seguimiento');
                    
                    // Actualizar etiqueta en inventariotda si existe
                    if ($request->input('equipo_retirado_inventariotda_id')) {
                        Inventariotda::where('id', $request->input('equipo_retirado_inventariotda_id'))
                            ->update([
                                'seguimiento' => $request->input('seguimiento'),
                            ]);
                    }
                }

                // Para reingreso por garantía, retirar etiqueta
                if ($tipoMovimiento === 'reingreso_garantia') {
                    $movimientoData['comentarios'] = $request->input('comentarios');
                    
                    if ($request->input('equipo_retirado_inventariotda_id')) {
                        Inventariotda::where('id', $request->input('equipo_retirado_inventariotda_id'))
                            ->update([
                                'en_garantia' => false,
                            ]);
                    }
                }

                // Para remplazo, agregar datos del equipo de remplazo
                if (in_array($tipoMovimiento, ['remplazo_dano', 'remplazo_renovacion'])) {
                    $equipoRemplazo = $this->obtenerDatosEquipo(
                        $request->input('equipo_remplazo_inventariotda_id'),
                        $request->input('equipo_remplazo_maf_id')
                    );

                    $movimientoData = array_merge($movimientoData, [
                        'equipo_remplazo_placa' => $request->input('equipo_remplazo_placa'),
                        'equipo_remplazo_serie' => $request->input('equipo_remplazo_serie'),
                        'equipo_remplazo_descripcion' => $request->input('equipo_remplazo_descripcion'),
                        'equipo_remplazo_marca' => $request->input('equipo_remplazo_marca'),
                        'equipo_remplazo_modelo' => $request->input('equipo_remplazo_modelo'),
                        'equipo_remplazo_activo' => $request->input('equipo_remplazo_activo'),
                        'equipo_remplazo_remanente' => $request->input('equipo_remplazo_remanente', 0),
                        'equipo_remplazo_inventariotda_id' => $request->input('equipo_remplazo_inventariotda_id'),
                        'equipo_remplazo_maf_id' => $request->input('equipo_remplazo_maf_id'),
                    ]);
                }
            } elseif ($tipoMovimiento === 'agregar') {
                // Obtener último batch de maf
                $lastBatch = DB::table('maf_import_batches')
                    ->where('status', 'done')
                    ->orderBy('finished_at', 'desc')
                    ->first();

                // Buscar o crear maf_id para el equipo agregado
                $placaAgregada = $request->input('equipo_agregado_placa');
                $variacionesPlacaAgregada = $this->generarVariacionesPlaca($placaAgregada);
                $maf = Maf::whereIn('placa', $variacionesPlacaAgregada)
                    ->where('batch_id', $lastBatch->id ?? null)
                    ->first();

                if (!$maf && $lastBatch) {
                    // Crear registro en maf si no existe
                    $maf = Maf::create([
                        'batch_id' => $lastBatch->id,
                        'row_num' => 0,
                        'plaza' => $request->input('plaza'),
                        'cr' => $request->input('cr'),
                        'tienda' => $request->input('tienda'),
                        'placa' => $request->input('equipo_agregado_placa'),
                        'serie' => $request->input('equipo_agregado_serie'),
                        'descripcion' => $request->input('equipo_agregado_descripcion'),
                        'marca' => $request->input('equipo_agregado_marca'),
                        'modelo' => $request->input('equipo_agregado_modelo'),
                        'activo' => $request->input('equipo_agregado_activo'),
                        'remanente' => $request->input('equipo_agregado_remanente', 0),
                        'imported_at' => now(),
                    ]);
                }

                // Crear registro en inventariotda
                $inventario = Inventariotda::create([
                    'maf_id' => $maf ? $maf->id : null,
                    'user_id' => $user->id,
                    'cr' => $request->input('cr'),
                    'tienda' => $request->input('tienda'),
                    'placa_editada' => $request->input('equipo_agregado_placa'),
                    'marca_editada' => $request->input('equipo_agregado_marca'),
                    'modelo_editado' => $request->input('equipo_agregado_modelo'),
                    'serie_editada' => $request->input('equipo_agregado_serie'),
                    'estado' => 'check',
                    'fecha_inventario' => now(),
                ]);

                $movimientoData = array_merge($movimientoData, [
                    'cr' => $request->input('cr'),
                    'tienda' => $request->input('tienda'),
                    'plaza' => $request->input('plaza'),
                    'nombre_plaza' => $request->input('nombre_plaza'),
                    'equipo_agregado_placa' => $request->input('equipo_agregado_placa'),
                    'equipo_agregado_serie' => $request->input('equipo_agregado_serie'),
                    'equipo_agregado_descripcion' => $request->input('equipo_agregado_descripcion'),
                    'equipo_agregado_marca' => $request->input('equipo_agregado_marca'),
                    'equipo_agregado_modelo' => $request->input('equipo_agregado_modelo'),
                    'equipo_agregado_activo' => $request->input('equipo_agregado_activo'),
                    'equipo_agregado_remanente' => $request->input('equipo_agregado_remanente', 0),
                    'equipo_agregado_inventariotda_id' => $inventario->id,
                ]);
            }

            // Crear movimiento
            $movimiento = Movimiento::create($movimientoData);

            // Enviar notificación por correo
            try {
                Mail::to($user->email)->send(new MovimientoEquipoMail($movimiento));
            } catch (\Exception $e) {
                \Log::error('Error al enviar correo de movimiento: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento registrado correctamente.',
                'movimiento_id' => $movimiento->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al guardar movimiento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el movimiento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener datos del equipo desde inventariotda o maf
     */
    private function obtenerDatosEquipo($inventariotdaId = null, $mafId = null)
    {
        $datos = [
            'placa' => '',
            'serie' => '',
            'descripcion' => '',
            'marca' => '',
            'modelo' => '',
            'activo' => '',
            'remanente' => 0,
            'cr' => '',
            'tienda' => '',
        ];

        if ($inventariotdaId) {
            $inventario = Inventariotda::with('maf')->find($inventariotdaId);
            if ($inventario) {
                $maf = $inventario->maf;
                $datos = [
                    'placa' => $inventario->placa_editada ?? ($maf->placa ?? ''),
                    'serie' => $inventario->serie_editada ?? ($maf->serie ?? ''),
                    'descripcion' => $maf->descripcion ?? '',
                    'marca' => $inventario->marca_editada ?? ($maf->marca ?? ''),
                    'modelo' => $inventario->modelo_editado ?? ($maf->modelo ?? ''),
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'cr' => $inventario->cr ?? ($maf->cr ?? ''),
                    'tienda' => $inventario->tienda ?? ($maf->tienda ?? ''),
                ];
            }
        } elseif ($mafId) {
            $maf = Maf::find($mafId);
            if ($maf) {
                $datos = [
                    'placa' => $maf->placa ?? '',
                    'serie' => $maf->serie ?? '',
                    'descripcion' => $maf->descripcion ?? '',
                    'marca' => $maf->marca ?? '',
                    'modelo' => $maf->modelo ?? '',
                    'activo' => $maf->activo ?? '',
                    'remanente' => $maf->remanente ?? 0,
                    'cr' => $maf->cr ?? '',
                    'tienda' => $maf->tienda ?? '',
                ];
            }
        }

        return $datos;
    }

    /**
     * Consultar movimientos
     */
    public function consulta()
    {
        $movimientos = Movimiento::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('movimientos.consulta', compact('movimientos'));
    }

    /**
     * Obtener detalle de un movimiento
     */
    public function detalle($id)
    {
        $movimiento = Movimiento::with('user')->findOrFail($id);
        
        $tipos = [
            'retiro' => 'Retiro de Equipo',
            'remplazo_dano' => 'Remplazo de Equipo por Daño',
            'remplazo_renovacion' => 'Remplazo de Equipo por Renovación',
            'agregar' => 'Agregar Equipo',
            'reingreso_garantia' => 'Reingreso por Garantía',
        ];

        $html = view('movimientos.detalle', compact('movimiento', 'tipos'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }
}
