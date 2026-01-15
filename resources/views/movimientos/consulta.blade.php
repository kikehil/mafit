@extends('layouts.app')

@section('title', 'Consulta de Movimientos')
@section('page-title', 'Consulta de Movimientos')

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Historial de Movimientos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CR/Tienda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($movimientos as $movimiento)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $tipos = [
                                        'retiro' => 'Retiro',
                                        'remplazo_dano' => 'Remplazo por Daño',
                                        'remplazo_renovacion' => 'Remplazo por Renovación',
                                        'agregar' => 'Agregar',
                                        'reingreso_garantia' => 'Reingreso Garantía',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($movimiento->tipo_movimiento === 'retiro') bg-red-100 text-red-800
                                    @elseif(in_array($movimiento->tipo_movimiento, ['remplazo_dano', 'remplazo_renovacion'])) bg-yellow-100 text-yellow-800
                                    @elseif($movimiento->tipo_movimiento === 'agregar') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ $tipos[$movimiento->tipo_movimiento] ?? $movimiento->tipo_movimiento }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $movimiento->cr ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $movimiento->tienda ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if(in_array($movimiento->tipo_movimiento, ['retiro', 'remplazo_dano', 'remplazo_renovacion', 'reingreso_garantia']))
                                    <div class="font-medium">{{ $movimiento->equipo_retirado_placa ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $movimiento->equipo_retirado_descripcion ?? '' }}</div>
                                @elseif($movimiento->tipo_movimiento === 'agregar')
                                    <div class="font-medium">{{ $movimiento->equipo_agregado_placa ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $movimiento->equipo_agregado_descripcion ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button 
                                    onclick="verDetalle({{ $movimiento->id }})"
                                    class="text-blue-600 hover:text-blue-900 font-medium"
                                >
                                    Ver Detalle
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay movimientos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movimientos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $movimientos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalle -->
<div id="modalDetalle" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="cerrarModalDetalle()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalle del Movimiento</h3>
            <div id="contenidoDetalle"></div>
            <button 
                onclick="cerrarModalDetalle()"
                class="mt-4 w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium"
            >
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
async function verDetalle(movimientoId) {
    try {
        const response = await fetch(`{{ url('/movimientos') }}/${movimientoId}/detalle`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('contenidoDetalle').innerHTML = data.html;
            document.getElementById('modalDetalle').classList.remove('hidden');
        } else {
            mostrarModal('Error', 'Error al cargar el detalle', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al cargar el detalle', 'error');
    }
}

function cerrarModalDetalle() {
    document.getElementById('modalDetalle').classList.add('hidden');
}
</script>
@endsection

