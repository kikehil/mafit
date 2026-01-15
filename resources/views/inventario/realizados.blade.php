@extends('layouts.app')

@section('title', 'Inventarios Realizados')
@section('page-title', 'Inventarios Realizados')

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="p-4">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Filtrar por:</label>
                    <div class="flex gap-6">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="filtro" 
                                value="con_inventario"
                                id="filtroConInventario"
                                checked
                                class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Con Inventario</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="filtro" 
                                value="sin_inventario"
                                id="filtroSinInventario"
                                class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Sin Inventario</span>
                        </label>
                    </div>
                </div>
                <button 
                    id="btnBuscar"
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                >
                    Buscar
                </button>
            </div>
        </div>

        <!-- Resultados -->
        <div id="resultadosContainer" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Resultados</h3>
                        <span id="totalTiendas" class="text-sm text-gray-600"></span>
                    </div>
                </div>
                <div id="resultados" class="divide-y divide-gray-200"></div>
            </div>
        </div>

        <!-- Estado vacío -->
        <div id="emptyState" class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-gray-500 text-lg">Seleccione un filtro y haga clic en "Buscar" para ver las tiendas</p>
        </div>
    </div>
</div>

<!-- Modal de Notificación -->
<div id="modalNotificacion" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div id="modalOverlay" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <!-- Modal centrado -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div id="modalIcon" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Icono se llenará dinámicamente -->
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 id="modalTitulo" class="text-lg leading-6 font-medium text-gray-900"></h3>
                        <div class="mt-2">
                            <p id="modalMensaje" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                    type="button" 
                    id="modalBtnCerrar"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para el modal de notificación
function mostrarModal(titulo, mensaje, tipo = 'info') {
    const modal = document.getElementById('modalNotificacion');
    const modalTitulo = document.getElementById('modalTitulo');
    const modalMensaje = document.getElementById('modalMensaje');
    const modalIcon = document.getElementById('modalIcon');
    
    modalTitulo.textContent = titulo;
    modalMensaje.textContent = mensaje;
    
    // Limpiar clases previas
    modalIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10';
    
    // Configurar icono según el tipo
    if (tipo === 'success') {
        modalIcon.classList.add('bg-green-100');
        modalIcon.innerHTML = `
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
    } else if (tipo === 'error') {
        modalIcon.classList.add('bg-red-100');
        modalIcon.innerHTML = `
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
    } else {
        modalIcon.classList.add('bg-blue-100');
        modalIcon.innerHTML = `
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    }
    
    modal.classList.remove('hidden');
}

function cerrarModal() {
    const modal = document.getElementById('modalNotificacion');
    modal.classList.add('hidden');
}

// Event listeners del modal
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalOverlay').addEventListener('click', cerrarModal);
    document.getElementById('modalBtnCerrar').addEventListener('click', cerrarModal);
    
    // Event listener para el botón de búsqueda
    document.getElementById('btnBuscar').addEventListener('click', buscarTiendas);
    
    // Event listener para los radio buttons
    document.querySelectorAll('input[name="filtro"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Mostrar botón flotante cuando se selecciona un filtro
            if (typeof mostrarBotonVolverArriba === 'function') {
                mostrarBotonVolverArriba();
            }
            // Opcional: buscar automáticamente al cambiar el filtro
            // buscarTiendas();
        });
    });
});

function buscarTiendas() {
    const filtroSeleccionado = document.querySelector('input[name="filtro"]:checked');
    
    if (!filtroSeleccionado) {
        mostrarModal('Información', 'Por favor seleccione un filtro', 'info');
        return;
    }
    
    // Mostrar botón flotante cuando se realiza una búsqueda
    if (typeof mostrarBotonVolverArriba === 'function') {
        mostrarBotonVolverArriba();
    }
    
    const filtro = filtroSeleccionado.value;
    
    fetch('{{ route("inventario.obtener-tiendas-realizados") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ filtro })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarResultados(data.tiendas, data.total, filtro);
        } else {
            mostrarModal('Error', data.message || 'Error al obtener tiendas', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar tiendas. Por favor intente nuevamente.', 'error');
    });
}

function mostrarResultados(tiendas, total, filtro) {
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('resultadosContainer').classList.remove('hidden');
    
    const totalElement = document.getElementById('totalTiendas');
    const tipoTexto = filtro === 'con_inventario' ? 'con inventario' : 'sin inventario';
    totalElement.textContent = `${total} ${total === 1 ? 'tienda encontrada' : 'tiendas encontradas'} ${tipoTexto}`;
    
    const container = document.getElementById('resultados');
    container.innerHTML = '';
    
    if (tiendas.length === 0) {
        container.innerHTML = `
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No se encontraron tiendas ${tipoTexto}</p>
            </div>
        `;
        return;
    }
    
    tiendas.forEach(tienda => {
        const tiendaDiv = document.createElement('div');
        tiendaDiv.className = 'px-4 md:px-6 py-4 hover:bg-gray-50 transition-colors';
        tiendaDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h4 class="text-base font-semibold text-gray-900">${tienda.tienda || tienda.cr}</h4>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">${tienda.cr}</span>
                    </div>
                    ${tienda.ultimo_inventario ? `
                        <p class="text-sm text-gray-600 mt-1">
                            Último inventario: <span class="font-medium">${tienda.ultimo_inventario.fecha}</span> 
                            por <span class="font-medium">${tienda.ultimo_inventario.usuario}</span>
                        </p>
                    ` : `
                        <p class="text-sm text-gray-500 mt-1">Sin inventarios registrados</p>
                    `}
                </div>
            </div>
        `;
        container.appendChild(tiendaDiv);
    });
}
</script>
@endsection

