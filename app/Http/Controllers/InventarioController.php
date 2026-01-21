<?php

namespace App\Http\Controllers;

use App\Models\Inventariotda;
use App\Models\Maf;
use App\Models\Movimiento;
use App\Models\Tienda;
use App\Models\User;
use App\Mail\InventarioNotificacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InventarioController extends Controller
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
     * Mostrar formulario de captura de inventario
     */
    public function captura()
    {
        return view('inventario.captura');
    }

    /**
     * Buscar tienda por CR o nombre
     */
    public function buscarTienda(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        $query = trim($request->input('query'));

        // Buscar tiendas por CR o nombre
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
            ->take(10);

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
     * Obtener equipos de una tienda agrupados por categoría
     */
    public function obtenerEquipos(Request $request)
    {
        $request->validate([
            'cr' => 'required|string',
            'plaza' => 'nullable|string',
        ]);

        $cr = $request->input('cr');
        $plaza = $request->input('plaza');

        // Obtener el último batch "done"
        $lastBatch = DB::table('maf_import_batches')
            ->where('status', 'done')
            ->orderBy('finished_at', 'desc')
            ->first();

        if (!$lastBatch) {
            return response()->json([
                'success' => false,
                'message' => 'No hay lotes procesados disponibles.',
            ]);
        }

        // Obtener equipos de la tienda que tengan categoría
        $query = Maf::where('batch_id', $lastBatch->id)
            ->where('cr', $cr)
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '');

        if ($plaza) {
            $query->where('plaza', $plaza);
        }

        $equipos = $query->orderBy('descripcion')
            ->get();

        // Orden de categorías específico
        $ordenCategorias = [
            'PUNTO DE VENTA' => 1,
            'MOVILIDAD' => 2,
            'telco' => 3,
            'ENERGIA' => 4,
            'CCTV' => 5,
        ];

        // Agrupar por categoría
        $equiposPorCategoria = $equipos->groupBy('categoria');

        // Obtener información de la tienda
        $tiendaInfo = $equipos->first();

        // Obtener último inventario de la tienda
        $ultimoInventario = Inventariotda::where('cr', $cr)
            ->with('user')
            ->orderBy('fecha_inventario', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        // Obtener información de movimientos para los equipos
        $placasEquipos = $equipos->pluck('placa')->filter()->unique()->toArray();
        $seriesEquipos = $equipos->pluck('serie')->filter()->unique()->toArray();
        
        // Generar todas las variaciones de placas
        $todasVariacionesPlacas = [];
        $mapeoPlacas = []; // Mapear variaciones a placa original
        foreach ($placasEquipos as $placaOriginal) {
            $variaciones = $this->generarVariacionesPlaca($placaOriginal);
            $todasVariacionesPlacas = array_merge($todasVariacionesPlacas, $variaciones);
            foreach ($variaciones as $variacion) {
                $mapeoPlacas[$variacion] = $placaOriginal;
            }
        }
        $todasVariacionesPlacas = array_unique($todasVariacionesPlacas);
        
        // Buscar movimientos relacionados con estos equipos
        $movimientos = Movimiento::where(function ($q) use ($todasVariacionesPlacas, $seriesEquipos) {
                $q->whereIn('equipo_retirado_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_retirado_serie', $seriesEquipos)
                  ->orWhereIn('equipo_remplazo_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_remplazo_serie', $seriesEquipos)
                  ->orWhereIn('equipo_agregado_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_agregado_serie', $seriesEquipos);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Crear un mapa de movimientos por placa/serie (usando todas las variaciones)
        $movimientosPorEquipo = [];
        foreach ($movimientos as $movimiento) {
            // Equipo retirado
            if ($movimiento->equipo_retirado_placa) {
                $placaMovimiento = $movimiento->equipo_retirado_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                // Si fue remplazado, indicar por qué equipo
                $equipoRemplazo = null;
                if (!empty($movimiento->equipo_remplazo_placa)) {
                    $equipoRemplazo = $mapeoPlacas[$movimiento->equipo_remplazo_placa] ?? $movimiento->equipo_remplazo_placa;
                }
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => $movimiento->tipo_movimiento,
                    'seguimiento' => $movimiento->seguimiento,
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                    'tiene_remplazo' => !empty($movimiento->equipo_remplazo_placa),
                    'equipo_remplazo_placa' => $equipoRemplazo,
                    'fue_remplazado_por' => $equipoRemplazo, // Equipo que lo remplazó
                ];
            }
            // Equipo de remplazo - este equipo remplazó a otro
            if ($movimiento->equipo_remplazo_placa) {
                $placaMovimiento = $movimiento->equipo_remplazo_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                $equipoRetirado = $mapeoPlacas[$movimiento->equipo_retirado_placa] ?? $movimiento->equipo_retirado_placa;
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => 'remplazo_recibido',
                    'equipo_retirado_placa' => $equipoRetirado,
                    'remplazo_a' => $equipoRetirado, // Equipo que remplazó
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                ];
            }
            // Equipo agregado
            if ($movimiento->equipo_agregado_placa) {
                $placaMovimiento = $movimiento->equipo_agregado_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => 'agregado',
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                ];
            }
        }
        
        // Obtener información de seguimiento desde inventariotda
        $inventariosConSeguimiento = Inventariotda::whereIn('maf_id', $equipos->pluck('id'))
            ->where(function ($q) {
                $q->whereNotNull('seguimiento')
                  ->orWhere('en_garantia', true);
            })
            ->get()
            ->keyBy('maf_id');
        
        // Formatear equipos por categoría en el orden especificado
        $equiposFormateados = [];
        
        // Ordenar categorías según el orden especificado
        $categoriasOrdenadas = $equiposPorCategoria->keys()->sortBy(function ($categoria) use ($ordenCategorias) {
            return $ordenCategorias[strtoupper($categoria)] ?? 999;
        });
        
        foreach ($categoriasOrdenadas as $categoria) {
            $equiposGrupo = $equiposPorCategoria[$categoria];
            $equiposFormateados[] = [
                'categoria' => $categoria,
                'equipos' => $equiposGrupo->map(function ($equipo) use ($movimientosPorEquipo, $inventariosConSeguimiento) {
                    $placa = $equipo->placa;
                    $movimientosEquipo = $movimientosPorEquipo[$placa] ?? [];
                    $inventarioEquipo = $inventariosConSeguimiento->get($equipo->id);
                    
                    // Determinar el estado más reciente
                    $estadoMovimiento = null;
                    if ($inventarioEquipo) {
                        if ($inventarioEquipo->seguimiento) {
                            $estadoMovimiento = [
                                'tipo' => 'seguimiento',
                                'valor' => $inventarioEquipo->seguimiento,
                                'en_garantia' => $inventarioEquipo->en_garantia ?? false,
                            ];
                        } elseif ($inventarioEquipo->en_garantia) {
                            $estadoMovimiento = [
                                'tipo' => 'en_garantia',
                                'valor' => 'garantia',
                            ];
                        }
                    }
                    
                    // Si hay movimientos, usar el más reciente para el estado principal
                    if (!empty($movimientosEquipo)) {
                        $movimientoReciente = $movimientosEquipo[0];
                        if ($movimientoReciente['tipo'] === 'retiro' || $movimientoReciente['tipo'] === 'remplazo_dano') {
                            $estadoMovimiento = [
                                'tipo' => 'seguimiento',
                                'valor' => $movimientoReciente['seguimiento'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'remplazo_renovacion') {
                            $estadoMovimiento = [
                                'tipo' => 'remplazo',
                                'equipo_remplazo' => $movimientoReciente['equipo_remplazo_placa'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'remplazo_recibido') {
                            $estadoMovimiento = [
                                'tipo' => 'remplazo_recibido',
                                'equipo_retirado' => $movimientoReciente['equipo_retirado_placa'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'agregado') {
                            $estadoMovimiento = [
                                'tipo' => 'agregado',
                            ];
                        }
                    }
                    
                    return [
                        'id' => $equipo->id,
                        'descripcion' => $equipo->descripcion,
                        'placa' => $equipo->placa,
                        'marca' => $equipo->marca,
                        'modelo' => $equipo->modelo,
                        'serie' => $equipo->serie,
                        'categoria' => $equipo->categoria,
                        'estado_movimiento' => $estadoMovimiento,
                        'movimientos' => $movimientosEquipo,
                    ];
                })->values()->toArray(),
            ];
        }

        return response()->json([
            'success' => true,
            'tienda' => [
                'cr' => $cr,
                'tienda' => $tiendaInfo->tienda ?? '',
                'plaza' => $tiendaInfo->plaza ?? '',
            ],
            'ultimo_inventario' => $ultimoInventario ? [
                'fecha' => $ultimoInventario->fecha_inventario->format('d/m/Y H:i'),
                'usuario' => $ultimoInventario->user->name ?? 'N/A',
            ] : null,
            'categorias' => $equiposFormateados,
        ]);
    }

    /**
     * Guardar o actualizar inventario
     */
    public function guardarInventario(Request $request)
    {
        $request->validate([
            'cr' => 'required|string',
            'plaza' => 'nullable|string',
            'notas' => 'nullable|string|max:1000',
            'equipos' => 'required|array',
            'equipos.*.maf_id' => 'required|exists:maf,id',
            'equipos.*.estado' => 'required|in:check,x',
            'equipos.*.placa_editada' => 'nullable|string|max:100',
            'equipos.*.marca_editada' => 'nullable|string|max:100',
            'equipos.*.modelo_editado' => 'nullable|string|max:100',
            'equipos.*.serie_editada' => 'nullable|string|max:100',
        ]);

        $cr = $request->input('cr');
        $plaza = $request->input('plaza');
        $notas = $request->input('notas');
        $equipos = $request->input('equipos');
        $fechaInventario = now();

        // Obtener información de la tienda del primer equipo
        $primerEquipo = Maf::find($equipos[0]['maf_id']);
        $tiendaNombre = $primerEquipo->tienda ?? '';

        DB::beginTransaction();
        try {
            // Normalizar la fecha al inicio del día para evitar problemas con la restricción única
            $fechaInventarioNormalizada = $fechaInventario->copy()->startOfDay();
            
            foreach ($equipos as $equipoData) {
                Inventariotda::updateOrCreate(
                    [
                        'maf_id' => $equipoData['maf_id'],
                        'fecha_inventario' => $fechaInventarioNormalizada,
                    ],
                    [
                        'user_id' => auth()->id(),
                        'cr' => $cr,
                        'tienda' => $tiendaNombre,
                        'placa_editada' => $equipoData['placa_editada'] ?? null,
                        'marca_editada' => $equipoData['marca_editada'] ?? null,
                        'modelo_editado' => $equipoData['modelo_editado'] ?? null,
                        'serie_editada' => $equipoData['serie_editada'] ?? null,
                        'notas' => $notas,
                        'estado' => $equipoData['estado'],
                        'fecha_inventario' => $fechaInventarioNormalizada,
                    ]
                );
            }

            DB::commit();

            // Enviar notificaciones por correo a los usuarios asignados a la tienda
            try {
                $tienda = Tienda::where('cr', $cr)->first();
                
                if ($tienda && $tienda->users->count() > 0) {
                    $usuarioRealizo = auth()->user()->name;
                    
                    foreach ($tienda->users as $usuarioAsignado) {
                        if ($usuarioAsignado->email) {
                            Mail::to($usuarioAsignado->email)
                                ->send(new InventarioNotificacionMail(
                                    $tiendaNombre,
                                    $usuarioRealizo,
                                    $notas
                                ));
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log del error pero no fallar el guardado del inventario
                \Log::error('Error al enviar notificaciones por correo: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Inventario guardado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el inventario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar consulta de inventarios
     */
    public function consulta()
    {
        return view('inventario.consulta');
    }

    /**
     * Buscar tienda por CR o nombre desde inventariotda
     */
    public function buscarTiendaConsulta(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        $query = trim($request->input('query'));

        // Buscar tiendas por CR o nombre desde inventariotda
        $inventarios = Inventariotda::whereNotNull('cr')
            ->where(function ($q) use ($query) {
                $q->where('cr', 'like', "%{$query}%")
                  ->orWhere('tienda', 'like', "%{$query}%");
            })
            ->with('maf')
            ->get()
            ->unique(function ($item) {
                return $item->cr;
            })
            ->take(10)
            ->map(function ($inventario) {
                return [
                    'cr' => $inventario->cr,
                    'tienda' => $inventario->tienda,
                    'plaza' => $inventario->maf->plaza ?? null,
                ];
            });

        if ($inventarios->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron tiendas con ese criterio.',
            ]);
        }

        return response()->json([
            'success' => true,
            'tiendas' => $inventarios->values(),
        ]);
    }

    /**
     * Obtener equipos de una tienda desde inventariotda agrupados por categoría
     */
    public function obtenerEquiposConsulta(Request $request)
    {
        $request->validate([
            'cr' => 'required|string',
            'plaza' => 'nullable|string',
            'categoria' => 'nullable|string',
        ]);

        $cr = $request->input('cr');
        $plaza = $request->input('plaza');
        $categoriaFiltro = $request->input('categoria');

        // Obtener equipos desde inventariotda con relación a maf
        $query = Inventariotda::where('cr', $cr)
            ->with(['maf' => function ($q) {
                $q->whereNotNull('categoria')
                  ->where('categoria', '!=', '');
            }])
            ->whereHas('maf', function ($q) {
                $q->whereNotNull('categoria')
                  ->where('categoria', '!=', '');
            });

        // Filtrar por plaza si se proporciona
        if ($plaza) {
            $query->whereHas('maf', function ($q) use ($plaza) {
                $q->where('plaza', $plaza);
            });
        }

        // Obtener el último inventario de cada equipo (última fecha_inventario)
        $inventarios = $query->get()
            ->groupBy('maf_id')
            ->map(function ($grupo) {
                return $grupo->sortByDesc('fecha_inventario')->first();
            });

        // Obtener equipos únicos con su información de maf
        $equipos = $inventarios->map(function ($inventario) {
            $maf = $inventario->maf;
            if (!$maf) {
                return null;
            }
            
            return (object) [
                'id' => $maf->id,
                'inventario_id' => $inventario->id,
                'descripcion' => $maf->descripcion,
                'placa' => $inventario->placa_editada ?? $maf->placa,
                'marca' => $inventario->marca_editada ?? $maf->marca,
                'modelo' => $inventario->modelo_editado ?? $maf->modelo,
                'serie' => $inventario->serie_editada ?? $maf->serie,
                'categoria' => $maf->categoria,
                'estado' => $inventario->estado,
                'seguimiento' => $inventario->seguimiento,
                'en_garantia' => $inventario->en_garantia ?? false,
            ];
        })->filter();
        
        // Obtener información de movimientos para los equipos
        $placasEquipos = $equipos->pluck('placa')->filter()->unique()->toArray();
        $seriesEquipos = $equipos->pluck('serie')->filter()->unique()->toArray();
        
        // Generar todas las variaciones de placas
        $todasVariacionesPlacas = [];
        $mapeoPlacas = []; // Mapear variaciones a placa original
        foreach ($placasEquipos as $placaOriginal) {
            $variaciones = $this->generarVariacionesPlaca($placaOriginal);
            $todasVariacionesPlacas = array_merge($todasVariacionesPlacas, $variaciones);
            foreach ($variaciones as $variacion) {
                $mapeoPlacas[$variacion] = $placaOriginal;
            }
        }
        $todasVariacionesPlacas = array_unique($todasVariacionesPlacas);
        
        // Buscar movimientos relacionados con estos equipos
        $movimientos = Movimiento::where(function ($q) use ($todasVariacionesPlacas, $seriesEquipos) {
                $q->whereIn('equipo_retirado_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_retirado_serie', $seriesEquipos)
                  ->orWhereIn('equipo_remplazo_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_remplazo_serie', $seriesEquipos)
                  ->orWhereIn('equipo_agregado_placa', $todasVariacionesPlacas)
                  ->orWhereIn('equipo_agregado_serie', $seriesEquipos);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Crear un mapa de movimientos por placa/serie (usando todas las variaciones)
        $movimientosPorEquipo = [];
        foreach ($movimientos as $movimiento) {
            // Equipo retirado
            if ($movimiento->equipo_retirado_placa) {
                $placaMovimiento = $movimiento->equipo_retirado_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                // Si fue remplazado, indicar por qué equipo
                $equipoRemplazo = null;
                if (!empty($movimiento->equipo_remplazo_placa)) {
                    $equipoRemplazo = $mapeoPlacas[$movimiento->equipo_remplazo_placa] ?? $movimiento->equipo_remplazo_placa;
                }
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => $movimiento->tipo_movimiento,
                    'seguimiento' => $movimiento->seguimiento,
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                    'tiene_remplazo' => !empty($movimiento->equipo_remplazo_placa),
                    'equipo_remplazo_placa' => $equipoRemplazo,
                    'fue_remplazado_por' => $equipoRemplazo, // Equipo que lo remplazó
                ];
            }
            // Equipo de remplazo - este equipo remplazó a otro
            if ($movimiento->equipo_remplazo_placa) {
                $placaMovimiento = $movimiento->equipo_remplazo_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                $equipoRetirado = $mapeoPlacas[$movimiento->equipo_retirado_placa] ?? $movimiento->equipo_retirado_placa;
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => 'remplazo_recibido',
                    'equipo_retirado_placa' => $equipoRetirado,
                    'remplazo_a' => $equipoRetirado, // Equipo que remplazó
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                ];
            }
            // Equipo agregado
            if ($movimiento->equipo_agregado_placa) {
                $placaMovimiento = $movimiento->equipo_agregado_placa;
                $placaOriginal = $mapeoPlacas[$placaMovimiento] ?? $placaMovimiento;
                if (!isset($movimientosPorEquipo[$placaOriginal])) {
                    $movimientosPorEquipo[$placaOriginal] = [];
                }
                $movimientosPorEquipo[$placaOriginal][] = [
                    'tipo' => 'agregado',
                    'fecha' => $movimiento->created_at->format('d/m/Y'),
                ];
            }
        }

        // Filtrar por categoría si se proporciona
        if ($categoriaFiltro && $categoriaFiltro !== '') {
            $equipos = $equipos->filter(function ($equipo) use ($categoriaFiltro) {
                return strtoupper($equipo->categoria) === strtoupper($categoriaFiltro);
            });
        }

        // Orden de categorías específico
        $ordenCategorias = [
            'PUNTO DE VENTA' => 1,
            'MOVILIDAD' => 2,
            'telco' => 3,
            'ENERGIA' => 4,
            'CCTV' => 5,
        ];

        // Agrupar por categoría
        $equiposPorCategoria = $equipos->groupBy('categoria');

        // Obtener información de la tienda
        $primerInventario = $inventarios->first();
        $tiendaInfo = $primerInventario ? [
            'cr' => $primerInventario->cr,
            'tienda' => $primerInventario->tienda ?? '',
            'plaza' => $primerInventario->maf->plaza ?? '',
        ] : [
            'cr' => $cr,
            'tienda' => '',
            'plaza' => $plaza ?? '',
        ];

        // Obtener todas las categorías disponibles para el filtro (de todos los inventarios de la tienda, no solo los filtrados)
        $queryCategorias = Inventariotda::where('cr', $cr)
            ->with('maf')
            ->whereHas('maf', function ($q) {
                $q->whereNotNull('categoria')
                  ->where('categoria', '!=', '');
            });
        
        if ($plaza) {
            $queryCategorias->whereHas('maf', function ($q) use ($plaza) {
                $q->where('plaza', $plaza);
            });
        }
        
        $categoriasDisponibles = $queryCategorias->get()
            ->pluck('maf.categoria')
            ->filter()
            ->unique()
            ->values();

        // Formatear equipos por categoría en el orden especificado
        $equiposFormateados = [];
        
        // Ordenar categorías según el orden especificado
        $categoriasOrdenadas = $equiposPorCategoria->keys()->sortBy(function ($categoria) use ($ordenCategorias) {
            return $ordenCategorias[strtoupper($categoria)] ?? 999;
        });
        
        foreach ($categoriasOrdenadas as $categoria) {
            $equiposGrupo = $equiposPorCategoria[$categoria];
            $equiposFormateados[] = [
                'categoria' => $categoria,
                'equipos' => $equiposGrupo->map(function ($equipo) use ($movimientosPorEquipo) {
                    $placa = $equipo->placa;
                    $movimientosEquipo = $movimientosPorEquipo[$placa] ?? [];
                    
                    // Determinar el estado más reciente
                    $estadoMovimiento = null;
                    if ($equipo->seguimiento) {
                        $estadoMovimiento = [
                            'tipo' => 'seguimiento',
                            'valor' => $equipo->seguimiento,
                            'en_garantia' => $equipo->en_garantia ?? false,
                        ];
                    } elseif ($equipo->en_garantia) {
                        $estadoMovimiento = [
                            'tipo' => 'en_garantia',
                            'valor' => 'garantia',
                        ];
                    }
                    
                    // Si hay movimientos, usar el más reciente para el estado principal
                    if (!empty($movimientosEquipo)) {
                        $movimientoReciente = $movimientosEquipo[0];
                        if ($movimientoReciente['tipo'] === 'retiro' || $movimientoReciente['tipo'] === 'remplazo_dano') {
                            $estadoMovimiento = [
                                'tipo' => 'seguimiento',
                                'valor' => $movimientoReciente['seguimiento'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'remplazo_renovacion') {
                            $estadoMovimiento = [
                                'tipo' => 'remplazo',
                                'equipo_remplazo' => $movimientoReciente['equipo_remplazo_placa'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'remplazo_recibido') {
                            $estadoMovimiento = [
                                'tipo' => 'remplazo_recibido',
                                'equipo_retirado' => $movimientoReciente['equipo_retirado_placa'] ?? null,
                            ];
                        } elseif ($movimientoReciente['tipo'] === 'agregado') {
                            $estadoMovimiento = [
                                'tipo' => 'agregado',
                            ];
                        }
                    }
                    
                    return [
                        'id' => $equipo->id,
                        'inventario_id' => $equipo->inventario_id,
                        'descripcion' => $equipo->descripcion,
                        'placa' => $equipo->placa,
                        'marca' => $equipo->marca,
                        'modelo' => $equipo->modelo,
                        'serie' => $equipo->serie,
                        'categoria' => $equipo->categoria,
                        'estado' => $equipo->estado,
                        'estado_movimiento' => $estadoMovimiento,
                        'movimientos' => $movimientosEquipo,
                    ];
                })->values()->toArray(),
            ];
        }

        return response()->json([
            'success' => true,
            'tienda' => $tiendaInfo,
            'categorias' => $equiposFormateados,
            'categorias_disponibles' => $categoriasDisponibles,
        ]);
    }

    /**
     * Mostrar vista de inventarios realizados
     */
    public function realizados()
    {
        return view('inventario.realizados');
    }

    /**
     * Obtener tiendas con o sin inventario de la plaza del usuario
     */
    public function obtenerTiendasRealizados(Request $request)
    {
        $request->validate([
            'filtro' => 'required|in:con_inventario,sin_inventario',
        ]);

        $filtro = $request->input('filtro');
        $usuario = auth()->user();

        if (!$usuario->plaza) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene una plaza asignada.',
            ]);
        }

        // Obtener todas las tiendas de la plaza del usuario que comienzan con "50"
        $tiendas = Tienda::where('plaza', $usuario->plaza)
            ->where('cr', 'like', '50%')
            ->orderBy('cr')
            ->get();

        // Obtener CRs que tienen inventario (último inventario del día actual o más reciente)
        $crsConInventario = Inventariotda::whereIn('cr', $tiendas->pluck('cr'))
            ->select('cr')
            ->distinct()
            ->pluck('cr')
            ->toArray();

        // Filtrar según el tipo seleccionado
        if ($filtro === 'con_inventario') {
            $tiendasFiltradas = $tiendas->filter(function ($tienda) use ($crsConInventario) {
                return in_array($tienda->cr, $crsConInventario);
            });
        } else {
            // sin_inventario
            $tiendasFiltradas = $tiendas->filter(function ($tienda) use ($crsConInventario) {
                return !in_array($tienda->cr, $crsConInventario);
            });
        }

        // Obtener información del último inventario para cada tienda
        $tiendasConInfo = $tiendasFiltradas->map(function ($tienda) {
            $ultimoInventario = Inventariotda::where('cr', $tienda->cr)
                ->with('user')
                ->orderBy('fecha_inventario', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            return [
                'id' => $tienda->id,
                'cr' => $tienda->cr,
                'tienda' => $tienda->tienda,
                'plaza' => $tienda->plaza,
                'ultimo_inventario' => $ultimoInventario ? [
                    'fecha' => $ultimoInventario->fecha_inventario->format('d/m/Y H:i'),
                    'usuario' => $ultimoInventario->user->name ?? 'N/A',
                ] : null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'tiendas' => $tiendasConInfo,
            'total' => $tiendasConInfo->count(),
        ]);
    }
}
