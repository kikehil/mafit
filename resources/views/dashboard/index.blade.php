@extends('layouts.app')

@section('title', 'Inicio')
@section('page-title', 'Inicio')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full {{ isset($results) ? 'max-w-7xl' : 'max-w-2xl' }} px-4 md:px-4">
        <!-- Logo/Título -->
        <div class="text-center mb-8">
            <p class="text-gray-600 text-lg">Sistema de Consulta de Activos Fijos</p>
        </div>

        <!-- Formulario de Búsqueda -->
        <form action="{{ route('dashboard.search') }}" method="POST" class="mb-6">
            @csrf
            <div class="relative">
                <div class="flex items-center border-2 border-gray-300 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-200 focus-within:border-blue-500 focus-within:shadow-xl">
                    <!-- Icono de búsqueda -->
                    <div class="pl-6 pr-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Campo de búsqueda -->
                    <input 
                        type="text" 
                        name="query" 
                        value="{{ old('query', $query ?? '') }}"
                        placeholder="Buscar por PLACA o SERIE..."
                        autofocus
                        class="flex-1 py-4 px-2 text-lg outline-none bg-transparent text-gray-900 placeholder-gray-400"
                        required
                    >
                    
                    <!-- Botón de búsqueda -->
                    <button 
                        type="submit" 
                        class="mr-2 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
                    >
                        Buscar
                    </button>
                </div>
            </div>
        </form>

        <!-- Resultados -->
        @if(isset($results))
            @if($results->isEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8 w-full">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Resultados de la búsqueda
                            </h3>
                            <span class="text-sm text-gray-600">
                                {{ $results->count() }} {{ $results->count() === 1 ? 'equipo encontrado' : 'equipos encontrados' }}
                            </span>
                        </div>
                        @if($currentBatch)
                            <p class="text-sm text-gray-500 mt-1">
                                Lote: <span class="font-medium">{{ $currentBatch->period }}</span>
                                ({{ $currentBatch->finished_at?->format('d/m/Y H:i') }})
                            </p>
                        @endif
                    </div>
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No se encontraron equipos con "{{ $query }}"</p>
                        <p class="text-gray-400 text-sm mt-2">Intente con otra PLACA o SERIE</p>
                    </div>
                </div>
            @else
                <!-- Vista móvil: Cards con grid de 3 columnas -->
                <div class="block md:hidden mt-8 space-y-4 px-4">
                    @foreach($results as $equipo)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-3">
                        <div class="grid grid-cols-3 gap-2 border border-slate-200 rounded-lg overflow-hidden">
                            <!-- FILA 1: NOMBRE plaza | NOMBRE tienda | placa -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">NOMBRE plaza</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->plazaRelation?->plaza_nom ?? $equipo->plaza ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">NOMBRE tienda</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->tienda ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">placa</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->placa ?? '-' }}</div>
                            </div>
                            
                            <!-- FILA 2: descripcion | marca | modelo -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">descripcion</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->descripcion ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">marca</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->marca ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">modelo</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->modelo ?? '-' }}</div>
                            </div>
                            
                            <!-- FILA 3: serie | anocompra | valor_neto -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">serie</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->serie ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">anocompra</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->anocompra ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">valor_neto</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    @if($equipo->valor_neto)
                                        ${{ number_format($equipo->valor_neto, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            
                            <!-- FILA 4: remanente (ocupa 3 columnas) -->
                            <div class="p-3 bg-slate-50 col-span-3">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">remanente</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    @if($equipo->remanente)
                                        ${{ number_format($equipo->remanente, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">serie</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->serie ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">anocompra</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">{{ $equipo->anocompra ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">valor_neto</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    @if($equipo->valor_neto)
                                        ${{ number_format($equipo->valor_neto, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            
                            <!-- FILA 4: remanente (ocupa 3 columnas) -->
                            <div class="p-3 bg-slate-50 col-span-3">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">remanente</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    @if($equipo->remanente)
                                        ${{ number_format($equipo->remanente, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Vista desktop: Tabla con encabezado -->
                <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 mt-8 w-full">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Resultados de la búsqueda
                            </h3>
                            <span class="text-sm text-gray-600">
                                {{ $results->count() }} {{ $results->count() === 1 ? 'equipo encontrado' : 'equipos encontrados' }}
                            </span>
                        </div>
                        @if($currentBatch)
                            <p class="text-sm text-gray-500 mt-1">
                                Lote: <span class="font-medium">{{ $currentBatch->period }}</span>
                                ({{ $currentBatch->finished_at?->format('d/m/Y H:i') }})
                            </p>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PLACA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SERIE</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIVO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TIENDA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PLAZA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DESCRIPCIÓN</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CATEGORÍA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MARCA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MODELO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VALOR NETO</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $equipo)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $equipo->placa ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->serie ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->activo ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->cr ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ $equipo->tienda ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->plaza ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ $equipo->descripcion ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($equipo->categoria)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $equipo->categoria }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->marca ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $equipo->modelo ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">
                                            @if($equipo->valor_neto)
                                                ${{ number_format($equipo->valor_neto, 2, '.', ',') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    // Animación de entrada suave
    // Mostrar botón flotante cuando hay resultados
    document.addEventListener('DOMContentLoaded', function() {
        // Si hay resultados, mostrar el botón
        @if(isset($results) && $results->isNotEmpty())
            if (typeof mostrarBotonVolverArriba === 'function') {
                mostrarBotonVolverArriba();
            }
        @endif
        
        // Mostrar botón cuando se envía el formulario de búsqueda
        const searchForm = document.querySelector('form[action="{{ route("dashboard.search") }}"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                setTimeout(() => {
                    if (typeof mostrarBotonVolverArriba === 'function') {
                        mostrarBotonVolverArriba();
                    }
                }, 500);
            });
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            form.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    });
</script>

<style>
    /* Efecto de enfoque suave en el input */
    input:focus {
        outline: none;
    }
    
    /* Animación sutil en el hover del formulario */
    form .border-gray-300:hover {
        border-color: #9CA3AF;
    }
</style>
@endsection
 MLA