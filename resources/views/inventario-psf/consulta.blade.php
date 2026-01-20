@extends('layouts.app')

@section('title', 'Consulta Inventario PSF')
@section('page-title', 'Consulta Inventario PSF - ' . $plazaUsuario)

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Alerta de Remanente Alto -->
        @if($equiposRemanenteAlto->count() > 0)
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg animate-pulse">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-bold text-lg">ALERTA: {{ $equiposRemanenteAlto->count() }} equipos con remanente mayor a $5</p>
                    <p class="text-sm">Se requiere atención inmediata</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Dashboard de Tarjetas -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Resumen por Tipo de Equipo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($equiposPorTipo as $tipo)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Tipo de Equipo</p>
                            <p class="font-semibold text-gray-900">{{ $tipo['descripcion'] ?: 'Sin descripción' }}</p>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">{{ $tipo['cantidad'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Filtros y Listado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Listado de Equipos</h2>
                    <input 
                        type="text" 
                        id="filtroBusqueda"
                        placeholder="Buscar por placa, marca, modelo..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full md:w-64"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marca</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remanente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEquipos" class="bg-white divide-y divide-gray-200">
                        @foreach($equipos as $equipo)
                        <tr data-placa="{{ strtolower($equipo->placa ?? '') }}" 
                            data-marca="{{ strtolower($equipo->marca ?? '') }}" 
                            data-modelo="{{ strtolower($equipo->modelo ?? '') }}"
                            class="{{ $equipo->remanente && $equipo->remanente > 5 ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $equipo->placa ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $equipo->marca ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $equipo->modelo ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $equipo->serie ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($equipo->remanente)
                                    ${{ number_format($equipo->remanente, 2) }}
                                    @if($equipo->remanente > 5)
                                        <span class="ml-2 text-red-600 font-bold">⚠</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select 
                                    class="ubicacion-select text-sm border border-gray-300 rounded px-2 py-1"
                                    data-id="{{ $equipo->id }}"
                                    onchange="actualizarUbicacion({{ $equipo->id }}, this.value)"
                                >
                                    <option value="BODEGA" {{ $equipo->ubicacion === 'BODEGA' ? 'selected' : '' }}>BODEGA</option>
                                    <option value="PS ERICK" {{ $equipo->ubicacion === 'PS ERICK' ? 'selected' : '' }}>PS ERICK</option>
                                    <option value="PS OSCAR" {{ $equipo->ubicacion === 'PS OSCAR' ? 'selected' : '' }}>PS OSCAR</option>
                                    <option value="PS ALFREDO" {{ $equipo->ubicacion === 'PS ALFREDO' ? 'selected' : '' }}>PS ALFREDO</option>
                                    <option value="PS ROBERTO1" {{ $equipo->ubicacion === 'PS ROBERTO1' ? 'selected' : '' }}>PS ROBERTO1</option>
                                    <option value="PS ROBERTO 2" {{ $equipo->ubicacion === 'PS ROBERTO 2' ? 'selected' : '' }}>PS ROBERTO 2</option>
                                    <option value="PS SAMUEL" {{ $equipo->ubicacion === 'PS SAMUEL' ? 'selected' : '' }}>PS SAMUEL</option>
                                    <option value="OFF. VALLES" {{ $equipo->ubicacion === 'OFF. VALLES' ? 'selected' : '' }}>OFF. VALLES</option>
                                    <option value="OFF TAMPICO" {{ $equipo->ubicacion === 'OFF TAMPICO' ? 'selected' : '' }}>OFF TAMPICO</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button 
                                    onclick="mostrarNotas({{ $equipo->id }}, '{{ addslashes($equipo->notas ?? '') }}')"
                                    class="text-blue-600 hover:text-blue-800 mr-3"
                                    title="Notas"
                                >
                                    📝
                                </button>
                                <button 
                                    onclick="eliminarEquipo({{ $equipo->id }})"
                                    class="text-red-600 hover:text-red-800"
                                    title="Eliminar"
                                >
                                    🗑️
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Notas -->
<div id="modalNotas" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="cerrarModalNotas()"></div>
        <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10">
            <h3 class="text-lg font-semibold mb-4">Notas del Equipo</h3>
            <textarea 
                id="notasInput"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4"
                placeholder="Ingrese notas sobre el equipo..."
            ></textarea>
            <div class="flex justify-end gap-3">
                <button onclick="cerrarModalNotas()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button onclick="guardarNotas()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Eliminación -->
<div id="modalEliminar" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="cerrarModalEliminar()"></div>
        <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Eliminar Equipo del Inventario</h3>
            <p class="mb-4 text-gray-600">¿Está seguro de eliminar este equipo? Esta acción requiere una nota obligatoria.</p>
            <textarea 
                id="notaEliminar"
                rows="4"
                class="w-full px-3 py-2 border border-red-300 rounded-lg mb-4"
                placeholder="Motivo de eliminación (obligatorio)..."
                required
            ></textarea>
            <div class="flex justify-end gap-3">
                <button onclick="cerrarModalEliminar()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button onclick="confirmarEliminar()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let equipoIdActual = null;

// Filtro de búsqueda
document.getElementById('filtroBusqueda').addEventListener('input', function(e) {
    const filtro = e.target.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaEquipos tr');
    
    filas.forEach(fila => {
        const placa = fila.dataset.placa || '';
        const marca = fila.dataset.marca || '';
        const modelo = fila.dataset.modelo || '';
        
        if (placa.includes(filtro) || marca.includes(filtro) || modelo.includes(filtro)) {
            fila.classList.remove('hidden');
        } else {
            fila.classList.add('hidden');
        }
    });
});

function actualizarUbicacion(id, ubicacion) {
    fetch(`/inventario-psf/${id}/ubicacion`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ubicacion })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar feedback visual
            const select = event.target;
            select.classList.add('bg-green-100');
            setTimeout(() => select.classList.remove('bg-green-100'), 1000);
        } else {
            alert('Error al actualizar ubicación: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar ubicación');
    });
}

function mostrarNotas(id, notasActuales) {
    equipoIdActual = id;
    document.getElementById('notasInput').value = notasActuales;
    document.getElementById('modalNotas').classList.remove('hidden');
}

function cerrarModalNotas() {
    document.getElementById('modalNotas').classList.add('hidden');
    equipoIdActual = null;
}

function guardarNotas() {
    const notas = document.getElementById('notasInput').value;
    
    fetch(`/inventario-psf/${equipoIdActual}/notas`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notas })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cerrarModalNotas();
            alert('Notas guardadas exitosamente');
            location.reload();
        } else {
            alert('Error al guardar notas: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar notas');
    });
}

function eliminarEquipo(id) {
    equipoIdActual = id;
    document.getElementById('notaEliminar').value = '';
    document.getElementById('modalEliminar').classList.remove('hidden');
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').classList.add('hidden');
    equipoIdActual = null;
}

function confirmarEliminar() {
    const nota = document.getElementById('notaEliminar').value.trim();
    
    if (!nota) {
        alert('La nota es obligatoria para eliminar el equipo');
        return;
    }
    
    fetch(`/inventario-psf/${equipoIdActual}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notas: nota })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cerrarModalEliminar();
            alert('Equipo eliminado exitosamente');
            location.reload();
        } else {
            alert('Error al eliminar: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el equipo');
    });
}

// Mostrar botón flotante
if (typeof mostrarBotonVolverArriba === 'function') {
    mostrarBotonVolverArriba();
}
</script>
@endsection

