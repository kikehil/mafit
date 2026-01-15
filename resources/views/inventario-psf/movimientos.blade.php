@extends('layouts.app')

@section('title', 'Movimientos Inventario PSF')
@section('page-title', 'Movimientos Inventario PSF - ' . $plazaUsuario)

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Historial de Movimientos</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Movimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($movimientos as $movimiento)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">{{ $movimiento->inventarioPSF->placa ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $movimiento->inventarioPSF->marca ?? '' }} {{ $movimiento->inventarioPSF->modelo ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($movimiento->tipo_movimiento === 'cambio_ubicacion') bg-blue-100 text-blue-800
                                    @elseif($movimiento->tipo_movimiento === 'eliminacion') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $movimiento->tipo_movimiento)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($movimiento->tipo_movimiento === 'cambio_ubicacion')
                                    <div>De: <strong>{{ $movimiento->ubicacion_anterior ?? '-' }}</strong></div>
                                    <div>A: <strong>{{ $movimiento->ubicacion_nueva ?? '-' }}</strong></div>
                                @elseif($movimiento->notas)
                                    <div class="max-w-md">{{ $movimiento->notas }}</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No hay movimientos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200">
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
</div>

<script>
if (typeof mostrarBotonVolverArriba === 'function') {
    mostrarBotonVolverArriba();
}
</script>
@endsection

