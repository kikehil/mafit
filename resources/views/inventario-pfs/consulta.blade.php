@extends('layouts.app')

@section('title', 'Consulta Inventario PFS')
@section('page-title', 'Consulta Inventario PFS')

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Filtros de búsqueda - Colapsable -->
        <div class="bg-white shadow-sm border-b rounded-lg mb-6">
            <button 
                id="toggleFiltros" 
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
            >
                <h2 class="text-lg font-semibold text-gray-900">Filtros</h2>
                <svg id="iconFiltros" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="contenidoFiltros" class="px-4 pb-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <select 
                            id="filtroUbicacion" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Todas las ubicaciones</option>
                            <option value="BODEGA">BODEGA</option>
                            <option value="PS ERICK">PS ERICK</option>
                            <option value="PS OSCAR">PS OSCAR</option>
                            <option value="PS ALFREDO">PS ALFREDO</option>
                            <option value="PS ROBERTO1">PS ROBERTO1</option>
                            <option value="PS ROBERTO 2">PS ROBERTO 2</option>
                            <option value="PS SAMUEL">PS SAMUEL</option>
                            <option value="OFF. VALLES">OFF. VALLES</option>
                            <option value="OFF TAMPICO">OFF TAMPICO</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button 
                            id="btnFiltrar" 
                            class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                        >
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <div id="resultadosContainer" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Captura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultadosBody" class="bg-white divide-y divide-gray-200">
                        @forelse($equipos as $equipo)
                        <tr class="hover:bg-gray-50" data-equipo-id="{{ $equipo->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $equipo->placa ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->marca ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->modelo ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->serie ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->activo ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <select 
                                    class="ubicacion-select px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    data-equipo-id="{{ $equipo->id }}"
                                    data-ubicacion-actual="{{ $equipo->ubicacion ?? '' }}"
                                >
                                    <option value="BODEGA" {{ ($equipo->ubicacion ?? '') == 'BODEGA' ? 'selected' : '' }}>BODEGA</option>
                                    <option value="PS ERICK" {{ ($equipo->ubicacion ?? '') == 'PS ERICK' ? 'selected' : '' }}>PS ERICK</option>
                                    <option value="PS OSCAR" {{ ($equipo->ubicacion ?? '') == 'PS OSCAR' ? 'selected' : '' }}>PS OSCAR</option>
                                    <option value="PS ALFREDO" {{ ($equipo->ubicacion ?? '') == 'PS ALFREDO' ? 'selected' : '' }}>PS ALFREDO</option>
                                    <option value="PS ROBERTO1" {{ ($equipo->ubicacion ?? '') == 'PS ROBERTO1' ? 'selected' : '' }}>PS ROBERTO1</option>
                                    <option value="PS ROBERTO 2" {{ ($equipo->ubicacion ?? '') == 'PS ROBERTO 2' ? 'selected' : '' }}>PS ROBERTO 2</option>
                                    <option value="PS SAMUEL" {{ ($equipo->ubicacion ?? '') == 'PS SAMUEL' ? 'selected' : '' }}>PS SAMUEL</option>
                                    <option value="OFF. VALLES" {{ ($equipo->ubicacion ?? '') == 'OFF. VALLES' ? 'selected' : '' }}>OFF. VALLES</option>
                                    <option value="OFF TAMPICO" {{ ($equipo->ubicacion ?? '') == 'OFF TAMPICO' ? 'selected' : '' }}>OFF TAMPICO</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->created_at ? $equipo->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipo->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button 
                                    onclick="abrirModalBaja({{ $equipo->id }}, '{{ $equipo->placa ?? '' }}')"
                                    class="px-3 py-1.5 bg-red-700 text-white text-xs font-bold rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-md border border-red-800"
                                >
                                    Dar de Baja
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg">No hay equipos inventariados</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($equipos->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($equipos->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-not-allowed">
                            Anterior
                        </span>
                        @else
                        <a href="{{ $equipos->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                        @endif
                        
                        @if($equipos->hasMorePages())
                        <a href="{{ $equipos->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                        @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-not-allowed">
                            Siguiente
                        </span>
                        @endif
                    </div>
                    
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ $equipos->firstItem() }}</span>
                                a
                                <span class="font-medium">{{ $equipos->lastItem() }}</span>
                                de
                                <span class="font-medium">{{ $equipos->total() }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                @if($equipos->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Anterior</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                @else
                                <a href="{{ $equipos->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <span class="sr-only">Anterior</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endif
                                
                                @foreach($equipos->getUrlRange(1, $equipos->lastPage()) as $page => $url)
                                    @if($page == $equipos->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                        {{ $page }}
                                    </span>
                                    @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ $page }}
                                    </a>
                                    @endif
                                @endforeach
                                
                                @if($equipos->hasMorePages())
                                <a href="{{ $equipos->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <span class="sr-only">Siguiente</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Siguiente</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para dar de baja -->
<div id="modalBaja" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="cerrarModalBaja()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Dar de Baja Equipo</h3>
                <p class="text-sm text-gray-500 mb-4">Placa: <span id="modalPlaca" class="font-semibold"></span></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la baja <span class="text-red-500">*</span></label>
                    <textarea 
                        id="notasBaja" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Ingrese el motivo de la baja del equipo..."
                        required
                    ></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                    type="button" 
                    onclick="confirmarBaja()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Confirmar Baja
                </button>
                <button 
                    type="button" 
                    onclick="cerrarModalBaja()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let equipoIdBaja = null;

// Toggle filtros colapsable
document.getElementById('toggleFiltros').addEventListener('click', function() {
    const contenido = document.getElementById('contenidoFiltros');
    const icon = document.getElementById('iconFiltros');
    contenido.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
});

// Filtros
document.getElementById('btnFiltrar').addEventListener('click', filtrarEquipos);
document.getElementById('filtroUbicacion').addEventListener('change', filtrarEquipos);

// Actualizar ubicación al cambiar el select
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('ubicacion-select')) {
        const equipoId = e.target.dataset.equipoId;
        const nuevaUbicacion = e.target.value;
        const ubicacionAnterior = e.target.dataset.ubicacionActual;
        
        if (nuevaUbicacion === ubicacionAnterior) {
            return; // No hacer nada si no cambió
        }
        
        actualizarUbicacion(equipoId, nuevaUbicacion, e.target);
    }
});

function actualizarUbicacion(equipoId, ubicacion, selectElement) {
    fetch(`{{ url('inventario-psf') }}/${equipoId}/ubicacion`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ubicacion: ubicacion })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.ubicacionActual = ubicacion;
            mostrarModal('Éxito', 'Ubicación actualizada correctamente', 'success');
        } else {
            // Revertir al valor anterior
            selectElement.value = selectElement.dataset.ubicacionActual;
            mostrarModal('Error', data.message || 'Error al actualizar ubicación', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = selectElement.dataset.ubicacionActual;
        mostrarModal('Error', 'Error al actualizar ubicación', 'error');
    });
}

function filtrarEquipos() {
    const ubicacion = document.getElementById('filtroUbicacion').value;
    const url = new URL('{{ route("inventario-psf.obtener-equipos") }}', window.location.origin);
    
    if (ubicacion) {
        url.searchParams.append('ubicacion', ubicacion);
    }
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarResultados(data.equipos);
        } else {
            mostrarModal('Error', 'Error al obtener equipos', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al filtrar equipos', 'error');
    });
}

function mostrarResultados(equipos) {
    const tbody = document.getElementById('resultadosBody');
    
    if (equipos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-lg">No se encontraron equipos con los filtros seleccionados</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = equipos.map(equipo => {
        const fecha = equipo.created_at ? new Date(equipo.created_at).toLocaleDateString('es-MX', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : '-';
        
        return `
            <tr class="hover:bg-gray-50" data-equipo-id="${equipo.id}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${equipo.placa || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${equipo.marca || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${equipo.modelo || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${equipo.serie || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${equipo.activo || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <select 
                        class="ubicacion-select px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        data-equipo-id="${equipo.id}"
                        data-ubicacion-actual="${equipo.ubicacion || ''}"
                    >
                        <option value="BODEGA" ${(equipo.ubicacion || '') == 'BODEGA' ? 'selected' : ''}>BODEGA</option>
                        <option value="PS ERICK" ${(equipo.ubicacion || '') == 'PS ERICK' ? 'selected' : ''}>PS ERICK</option>
                        <option value="PS OSCAR" ${(equipo.ubicacion || '') == 'PS OSCAR' ? 'selected' : ''}>PS OSCAR</option>
                        <option value="PS ALFREDO" ${(equipo.ubicacion || '') == 'PS ALFREDO' ? 'selected' : ''}>PS ALFREDO</option>
                        <option value="PS ROBERTO1" ${(equipo.ubicacion || '') == 'PS ROBERTO1' ? 'selected' : ''}>PS ROBERTO1</option>
                        <option value="PS ROBERTO 2" ${(equipo.ubicacion || '') == 'PS ROBERTO 2' ? 'selected' : ''}>PS ROBERTO 2</option>
                        <option value="PS SAMUEL" ${(equipo.ubicacion || '') == 'PS SAMUEL' ? 'selected' : ''}>PS SAMUEL</option>
                        <option value="OFF. VALLES" ${(equipo.ubicacion || '') == 'OFF. VALLES' ? 'selected' : ''}>OFF. VALLES</option>
                        <option value="OFF TAMPICO" ${(equipo.ubicacion || '') == 'OFF TAMPICO' ? 'selected' : ''}>OFF TAMPICO</option>
                    </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${fecha}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${equipo.user ? equipo.user.name : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button 
                        onclick="abrirModalBaja(${equipo.id}, '${equipo.placa || ''}')"
                        class="px-3 py-1.5 bg-red-700 text-white text-xs font-bold rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-md border border-red-800"
                    >
                        Dar de Baja
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    if (typeof mostrarBotonVolverArriba === 'function') {
        mostrarBotonVolverArriba();
    }
}

function abrirModalBaja(id, placa) {
    equipoIdBaja = id;
    document.getElementById('modalPlaca').textContent = placa;
    document.getElementById('notasBaja').value = '';
    document.getElementById('modalBaja').classList.remove('hidden');
}

function cerrarModalBaja() {
    document.getElementById('modalBaja').classList.add('hidden');
    equipoIdBaja = null;
    document.getElementById('notasBaja').value = '';
}

function confirmarBaja() {
    const notas = document.getElementById('notasBaja').value.trim();
    
    if (!notas) {
        mostrarModal('Error', 'Por favor ingrese el motivo de la baja', 'error');
        return;
    }
    
    fetch(`{{ url('inventario-psf') }}/${equipoIdBaja}/dar-de-baja`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ notas: notas })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal('Éxito', 'Equipo dado de baja correctamente', 'success');
            cerrarModalBaja();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarModal('Error', data.message || 'Error al dar de baja', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al dar de baja el equipo', 'error');
    });
}

// Cargar equipos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if (typeof mostrarBotonVolverArriba === 'function') {
        mostrarBotonVolverArriba();
    }
});
</script>
@endsection
