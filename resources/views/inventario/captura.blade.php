@extends('layouts.app')

@section('title', 'Captura Inventario')
@section('page-title', 'Captura Inventario')

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <!-- Búsqueda de Tienda -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-10">
        <div class="p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Buscar Tienda</h2>
            <div class="flex gap-2">
                <input 
                    type="text" 
                    id="searchTienda" 
                    placeholder="Ingrese CR o nombre de tienda..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                    autocomplete="off"
                >
                <button 
                    id="btnBuscar" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                >
                    Buscar
                </button>
            </div>
            <div id="tiendaResults" class="mt-2 hidden"></div>
        </div>
    </div>

    <!-- Contenedor de Equipos -->
    <div id="equiposContainer" class="hidden">
        <div class="p-4">
            <div id="tiendaInfo" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-gray-900" id="tiendaNombre"></h3>
                <p class="text-sm text-gray-700 font-medium" id="tiendaCR"></p>
                <p class="text-sm text-gray-600 mt-2" id="ultimoInventario"></p>
            </div>

            <!-- Filtro de Categoría y Botón Agregar Equipo -->
            <div class="mb-4 hidden" id="filtroCategoriaContainer">
                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Categoría</label>
                        <select 
                            id="filtroCategoria"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <button 
                        type="button"
                        id="btnAgregarEquipo"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-medium whitespace-nowrap"
                    >
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Agregar Equipo
                    </button>
                </div>
            </div>

            <form id="inventarioForm">
                <div id="equiposPorCategoria"></div>

                <div class="mt-6 sticky bottom-0 bg-white border-t border-gray-200 p-4 -mx-4 space-y-4">
                    <div>
                        <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                        <textarea 
                            id="notas" 
                            name="notas" 
                            rows="3"
                            placeholder="Agregar notas sobre el inventario..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 resize-none"
                        ></textarea>
                    </div>
                    <button 
                        type="submit" 
                        id="btnCerrarInventario"
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-semibold text-lg"
                    >
                        Cerrar Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Estado vacío -->
    <div id="emptyState" class="flex flex-col items-center justify-center min-h-[60vh] px-4 text-center">
        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <p class="text-gray-500 text-lg">Busque una tienda para comenzar</p>
    </div>
</div>

<!-- Modal para Agregar Equipo -->
<div id="modalAgregarEquipo" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div id="modalAgregarEquipoOverlay" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <!-- Modal centrado -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Agregar Nuevo Equipo</h3>
                        <form id="formAgregarEquipo">
                            <div class="space-y-4">
                                <div>
                                    <label for="nuevaPlaca" class="block text-sm font-medium text-gray-700 mb-1">
                                        Placa <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nuevaPlaca" 
                                        name="placa"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Ingrese la placa del equipo"
                                    >
                                </div>
                                <div>
                                    <label for="nuevaMarca" class="block text-sm font-medium text-gray-700 mb-1">
                                        Marca <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nuevaMarca" 
                                        name="marca"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Ingrese la marca del equipo"
                                    >
                                </div>
                                <div>
                                    <label for="nuevoModelo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Modelo <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nuevoModelo" 
                                        name="modelo"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Ingrese el modelo del equipo"
                                    >
                                </div>
                                <div>
                                    <label for="nuevaSerie" class="block text-sm font-medium text-gray-700 mb-1">
                                        Serie <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nuevaSerie" 
                                        name="serie"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Ingrese el número de serie"
                                    >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                    type="button" 
                    id="btnGuardarEquipo"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Guardar
                </button>
                <button 
                    type="button" 
                    id="btnCancelarAgregarEquipo"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Cancelar
                </button>
            </div>
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
let tiendaSeleccionada = null;
let equiposData = {};
let categoriasDisponibles = [];
let equiposOriginales = [];
// Objeto para mantener los valores editados en memoria
let valoresEditados = {};

// Funciones para el modal
function mostrarModal(titulo, mensaje, tipo = 'success') {
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

function abrirModalAgregarEquipo() {
    if (!tiendaSeleccionada) {
        mostrarModal('Información', 'Por favor seleccione una tienda primero', 'info');
        return;
    }
    const modal = document.getElementById('modalAgregarEquipo');
    modal.classList.remove('hidden');
    // Limpiar formulario
    document.getElementById('formAgregarEquipo').reset();
}

function cerrarModalAgregarEquipo() {
    const modal = document.getElementById('modalAgregarEquipo');
    modal.classList.add('hidden');
    document.getElementById('formAgregarEquipo').reset();
}

function guardarNuevoEquipo() {
    if (!tiendaSeleccionada) {
        mostrarModal('Error', 'No hay tienda seleccionada', 'error');
        return;
    }
    
    const placa = document.getElementById('nuevaPlaca').value.trim();
    const marca = document.getElementById('nuevaMarca').value.trim();
    const modelo = document.getElementById('nuevoModelo').value.trim();
    const serie = document.getElementById('nuevaSerie').value.trim();
    
    if (!placa || !marca || !modelo || !serie) {
        mostrarModal('Error', 'Por favor complete todos los campos requeridos', 'error');
        return;
    }
    
    // Deshabilitar botón mientras se guarda
    const btnGuardar = document.getElementById('btnGuardarEquipo');
    btnGuardar.disabled = true;
    btnGuardar.textContent = 'Guardando...';
    
    fetch('{{ route("inventario.agregar-equipo") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            cr: tiendaSeleccionada.cr,
            plaza: tiendaSeleccionada.plaza,
            placa: placa,
            marca: marca,
            modelo: modelo,
            serie: serie
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal('Éxito', 'Equipo agregado exitosamente', 'success');
            cerrarModalAgregarEquipo();
            // Recargar equipos
            seleccionarTienda(tiendaSeleccionada.cr, tiendaSeleccionada.plaza);
        } else {
            mostrarModal('Error', data.message || 'Error al agregar el equipo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al agregar el equipo. Por favor intente nuevamente.', 'error');
    })
    .finally(() => {
        btnGuardar.disabled = false;
        btnGuardar.textContent = 'Guardar';
    });
}

// Filtro de categoría y modal agregar equipo
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalOverlay').addEventListener('click', cerrarModal);
    document.getElementById('modalBtnCerrar').addEventListener('click', cerrarModal);
    
    // Modal agregar equipo
    document.getElementById('modalAgregarEquipoOverlay').addEventListener('click', cerrarModalAgregarEquipo);
    document.getElementById('btnCancelarAgregarEquipo').addEventListener('click', cerrarModalAgregarEquipo);
    document.getElementById('btnAgregarEquipo').addEventListener('click', abrirModalAgregarEquipo);
    document.getElementById('btnGuardarEquipo').addEventListener('click', guardarNuevoEquipo);
    
    // Event listener para el filtro de categoría
    const filtroCategoria = document.getElementById('filtroCategoria');
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', function() {
            if (equiposData && equiposData.categorias) {
                const categoriaSeleccionada = this.value;
                mostrarEquipos(equiposData, categoriaSeleccionada);
                // Mostrar botón flotante cuando se selecciona un filtro
                if (typeof mostrarBotonVolverArriba === 'function') {
                    mostrarBotonVolverArriba();
                }
            }
        });
    }
});

// Búsqueda de tienda automática
let searchTimeout;
document.getElementById('searchTienda').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim();
    
    // Si el campo está vacío, ocultar resultados
    if (query.length === 0) {
        document.getElementById('tiendaResults').classList.add('hidden');
        return;
    }
    
    // Esperar 500ms después de que el usuario deje de escribir
    searchTimeout = setTimeout(() => {
        buscarTienda();
    }, 500);
});

// También permitir búsqueda con Enter o clic en el botón
document.getElementById('btnBuscar').addEventListener('click', function() {
    clearTimeout(searchTimeout);
    buscarTienda();
});

document.getElementById('searchTienda').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(searchTimeout);
        buscarTienda();
    }
});

function buscarTienda() {
    const query = document.getElementById('searchTienda').value.trim();
    if (!query) {
        mostrarModal('Información', 'Por favor ingrese un CR o nombre de tienda', 'info');
        return;
    }

    fetch('{{ route("inventario.buscar-tienda") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ query })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.tiendas.length > 0) {
            mostrarResultadosTienda(data.tiendas);
        } else {
            mostrarModal('Sin resultados', 'No se encontraron tiendas con ese criterio', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar tienda. Por favor intente nuevamente.', 'error');
    });
}

function mostrarResultadosTienda(tiendas) {
    const container = document.getElementById('tiendaResults');
    container.innerHTML = '<div class="space-y-2">' +
        tiendas.map(tienda => `
            <button 
                onclick="seleccionarTienda('${tienda.cr}', '${tienda.plaza || ''}')"
                class="w-full text-left p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors"
            >
                <div class="font-medium text-gray-900">${tienda.cr}</div>
                <div class="text-sm text-gray-600">${tienda.tienda || 'Sin nombre'}</div>
            </button>
        `).join('') +
        '</div>';
    container.classList.remove('hidden');
}

function seleccionarTienda(cr, plaza) {
    tiendaSeleccionada = { cr, plaza };
    document.getElementById('tiendaResults').classList.add('hidden');
    
    // Obtener equipos
    fetch('{{ route("inventario.obtener-equipos") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cr, plaza })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            equiposData = data;
            equiposOriginales = data.categorias; // Guardar datos originales
            mostrarEquipos(data);
            actualizarFiltroCategoria(data);
            // Mostrar botón flotante cuando se cargan equipos
            if (typeof mostrarBotonVolverArriba === 'function') {
                mostrarBotonVolverArriba();
            }
        } else {
            mostrarModal('Error', data.message || 'Error al obtener equipos', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al obtener equipos. Por favor intente nuevamente.', 'error');
    });
}

function mostrarEquipos(data, categoriaFiltro = '') {
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('equiposContainer').classList.remove('hidden');
    
    document.getElementById('tiendaNombre').textContent = data.tienda.tienda || data.tienda.cr;
    document.getElementById('tiendaCR').textContent = `CR: ${data.tienda.cr}`;
    
    // Mostrar último inventario si existe
    const ultimoInventarioEl = document.getElementById('ultimoInventario');
    if (data.ultimo_inventario) {
        ultimoInventarioEl.textContent = `Último inventario: ${data.ultimo_inventario.fecha} por ${data.ultimo_inventario.usuario}`;
        ultimoInventarioEl.classList.remove('hidden');
    } else {
        ultimoInventarioEl.textContent = 'Sin inventarios previos';
        ultimoInventarioEl.classList.remove('hidden');
    }
    
    // Inicializar valoresEditados con valores originales de TODOS los equipos (no solo los visibles)
    data.categorias.forEach(categoriaData => {
        categoriaData.equipos.forEach(equipo => {
            const mafId = equipo.id;
            if (!valoresEditados[mafId]) {
                valoresEditados[mafId] = {
                    estado: 'check',
                    placa: (equipo.placa || '').trim(),
                    marca: (equipo.marca || '').trim(),
                    modelo: (equipo.modelo || '').trim(),
                    serie: (equipo.serie || '').trim()
                };
            }
        });
    });
    
    const container = document.getElementById('equiposPorCategoria');
    container.innerHTML = '';
    
    // Filtrar categorías si se seleccionó una
    let categoriasAMostrar = data.categorias;
    if (categoriaFiltro && categoriaFiltro !== '') {
        if (categoriaFiltro === 'Sin categoría') {
            // Mostrar solo equipos sin categoría
            categoriasAMostrar = data.categorias.filter(cat => !cat.categoria || cat.categoria === '' || cat.categoria === null);
        } else {
            // Mostrar solo la categoría seleccionada
            categoriasAMostrar = data.categorias.filter(cat => {
                const catNombre = cat.categoria || 'Sin categoría';
                return catNombre.toUpperCase() === categoriaFiltro.toUpperCase();
            });
        }
    }
    
    // Mantener el orden del array
    categoriasAMostrar.forEach(categoriaData => {
        const categoriaDiv = document.createElement('div');
        categoriaDiv.className = 'mb-6';
        const categoriaNombre = categoriaData.categoria || 'Sin categoría';
        // Usar estilo diferente para "Sin categoría"
        const bgColor = categoriaNombre === 'Sin categoría' 
            ? 'bg-gray-200 border-gray-400' 
            : 'bg-blue-200 border-blue-400';
        categoriaDiv.innerHTML = `
            <h4 class="text-xl font-bold text-gray-900 mb-4 px-4 py-3 rounded-lg shadow-md ${bgColor} border-2">${categoriaNombre}</h4>
            <div class="space-y-3">
                ${categoriaData.equipos.map(equipo => crearFilaEquipo(equipo)).join('')}
            </div>
        `;
        container.appendChild(categoriaDiv);
    });
    
    // Restaurar valores editados y estados después de crear el HTML
    setTimeout(() => {
        data.categorias.forEach(categoriaData => {
            categoriaData.equipos.forEach(equipo => {
                const mafId = equipo.id;
                if (valoresEditados[mafId]) {
                    const equipoDiv = document.querySelector(`[data-maf-id="${mafId}"]`);
                    if (equipoDiv) {
                        // Restaurar estado
                        const estado = valoresEditados[mafId].estado || 'check';
                        const estadoRadio = equipoDiv.querySelector(`input[type="radio"][name*="[estado]"][value="${estado}"]`);
                        if (estadoRadio && !estadoRadio.checked) {
                            estadoRadio.checked = true;
                            // Actualizar visualmente los inputs según el estado
                            const inputs = equipoDiv.querySelectorAll('.equipo-input');
                            inputs.forEach(input => {
                                const campo = input.getAttribute('data-campo');
                                if (estado === 'x') {
                                    input.disabled = false;
                                    input.classList.remove('bg-gray-50', 'text-gray-900', 'font-medium');
                                    input.classList.add('bg-white', 'text-gray-900', 'font-semibold', 'border-blue-500');
                                    // Restaurar valor editado si existe
                                    if (valoresEditados[mafId][campo] !== undefined) {
                                        input.value = valoresEditados[mafId][campo];
                                    }
                                } else {
                                    input.disabled = true;
                                    input.classList.remove('bg-white', 'text-gray-900', 'font-semibold', 'border-blue-500');
                                    input.classList.add('bg-gray-50', 'text-gray-900', 'font-medium');
                                }
                            });
                        } else if (estado === 'x') {
                            // Si el estado es 'x', restaurar valores editados
                            const inputs = equipoDiv.querySelectorAll('.equipo-input');
                            inputs.forEach(input => {
                                const campo = input.getAttribute('data-campo');
                                if (valoresEditados[mafId][campo] !== undefined) {
                                    input.value = valoresEditados[mafId][campo];
                                }
                            });
                        }
                    }
                }
            });
        });
    }, 50);
}

function actualizarFiltroCategoria(data) {
    const select = document.getElementById('filtroCategoria');
    const container = document.getElementById('filtroCategoriaContainer');
    
    // Limpiar opciones existentes excepto "Todas"
    select.innerHTML = '<option value="">Todas las categorías</option>';
    
    // Obtener categorías únicas
    const categoriasUnicas = new Set();
    let tieneSinCategoria = false;
    
    data.categorias.forEach(categoriaData => {
        if (categoriaData.categoria && categoriaData.categoria !== '') {
            categoriasUnicas.add(categoriaData.categoria);
        } else {
            tieneSinCategoria = true;
        }
    });
    
    // Agregar categorías únicas
    Array.from(categoriasUnicas).sort().forEach(categoria => {
        const option = document.createElement('option');
        option.value = categoria;
        option.textContent = categoria;
        select.appendChild(option);
    });
    
    // Agregar "Sin categoría" si hay equipos sin categoría
    if (tieneSinCategoria) {
        const option = document.createElement('option');
        option.value = 'Sin categoría';
        option.textContent = 'Sin categoría';
        select.appendChild(option);
    }
    
    // Mostrar el filtro
    container.classList.remove('hidden');
}

function crearFilaEquipo(equipo) {
    // Generar etiquetas de estado de movimiento
    let etiquetasEstado = '';
    
    // Verificar movimientos del equipo
    if (equipo.movimientos && equipo.movimientos.length > 0) {
        const movimientos = equipo.movimientos;
        movimientos.forEach(mov => {
            if (mov.tipo === 'retiro' && mov.seguimiento) {
                const color = mov.seguimiento === 'baja' ? 'red' : 'yellow';
                const texto = mov.seguimiento === 'baja' ? 'BAJA' : 'GARANTÍA';
                etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800 mr-1">${texto}</span>`;
            } else if (mov.tipo === 'remplazo_dano' || mov.tipo === 'remplazo_renovacion') {
                // Este equipo fue remplazado por otro
                if (mov.fue_remplazado_por) {
                    etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 mr-1" title="Este equipo fue remplazado por ${mov.fue_remplazado_por}">REMPLAZADO POR: ${mov.fue_remplazado_por}</span>`;
                }
                // Mostrar seguimiento si existe
                if (mov.seguimiento) {
                    const color = mov.seguimiento === 'baja' ? 'red' : 'yellow';
                    const texto = mov.seguimiento === 'baja' ? 'BAJA' : 'GARANTÍA';
                    etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800 mr-1">${texto}</span>`;
                }
            } else if (mov.tipo === 'remplazo_recibido') {
                // Este equipo remplazó a otro
                if (mov.remplazo_a) {
                    etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-1" title="Este equipo remplazó al equipo ${mov.remplazo_a}">REMPLAZÓ A: ${mov.remplazo_a}</span>`;
                } else {
                    etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-1">EQUIPO DE REMPLAZO</span>`;
                }
            } else if (mov.tipo === 'agregado') {
                etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-1">EQUIPO AGREGADO</span>`;
            }
        });
    }
    
    // Verificar estado de seguimiento desde inventariotda
    if (equipo.estado_movimiento) {
        const estado = equipo.estado_movimiento;
        if (estado.tipo === 'seguimiento' && estado.valor) {
            const color = estado.valor === 'baja' ? 'red' : 'yellow';
            const texto = estado.valor === 'baja' ? 'BAJA' : 'GARANTÍA';
            // Solo agregar si no existe ya
            if (!etiquetasEstado.includes(texto)) {
                etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800 mr-1">${texto}</span>`;
            }
        } else if (estado.tipo === 'en_garantia') {
            if (!etiquetasEstado.includes('GARANTÍA')) {
                etiquetasEstado += `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-1">EN GARANTÍA</span>`;
            }
        }
    }
    
    return `
        <div class="bg-white border border-gray-200 rounded-lg p-4" data-maf-id="${equipo.id}">
            <div class="flex items-start justify-between mb-3">
                <div class="font-semibold text-gray-900 text-base flex-1">${equipo.descripcion || '-'}</div>
                ${etiquetasEstado ? `<div class="flex flex-wrap gap-2 ml-2">${etiquetasEstado}</div>` : ''}
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Placa:</label>
                    <input 
                        type="text" 
                        name="equipos[${equipo.id}][placa_editada]"
                        value="${equipo.placa || ''}"
                        disabled
                        class="equipo-input flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium"
                        data-maf-id="${equipo.id}"
                        data-campo="placa"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Marca:</label>
                    <input 
                        type="text" 
                        name="equipos[${equipo.id}][marca_editada]"
                        value="${equipo.marca || ''}"
                        disabled
                        class="equipo-input flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium"
                        data-maf-id="${equipo.id}"
                        data-campo="marca"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Modelo:</label>
                    <input 
                        type="text" 
                        name="equipos[${equipo.id}][modelo_editado]"
                        value="${equipo.modelo || ''}"
                        disabled
                        class="equipo-input flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium"
                        data-maf-id="${equipo.id}"
                        data-campo="modelo"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Serie:</label>
                    <input 
                        type="text" 
                        name="equipos[${equipo.id}][serie_editada]"
                        value="${equipo.serie || ''}"
                        disabled
                        class="equipo-input flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium"
                        data-maf-id="${equipo.id}"
                        data-campo="serie"
                    >
                </div>
                <div class="flex items-center justify-end gap-4 mt-3 pt-3 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-700">Estado:</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="equipos[${equipo.id}][estado]"
                                value="check"
                                checked
                                class="estado-radio w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500"
                                onchange="toggleInputs(this, '${equipo.id}')"
                            >
                            <span class="ml-2 text-lg text-green-600 font-semibold">✓</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="equipos[${equipo.id}][estado]"
                                value="x"
                                class="estado-radio w-5 h-5 text-red-600 border-gray-300 focus:ring-red-500"
                                onchange="toggleInputs(this, '${equipo.id}')"
                            >
                            <span class="ml-2 text-lg text-red-600 font-semibold">✗</span>
                        </label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="equipos[${equipo.id}][maf_id]" value="${equipo.id}">
        </div>
    `;
}

function toggleInputs(radio, mafId) {
    const estado = radio.value;
    const inputs = document.querySelectorAll(`.equipo-input[data-maf-id="${mafId}"]`);
    
    // Guardar valores actuales antes de cambiar el estado
    inputs.forEach(input => {
        const campo = input.getAttribute('data-campo');
        if (!valoresEditados[mafId]) {
            valoresEditados[mafId] = {};
        }
        valoresEditados[mafId][campo] = input.value;
    });
    
    // Buscar equipo original para restaurar valores
    let equipoOriginal = null;
    for (const categoriaData of equiposData.categorias) {
        equipoOriginal = categoriaData.equipos.find(e => e.id == mafId);
        if (equipoOriginal) break;
    }
    
    inputs.forEach(input => {
        const campo = input.getAttribute('data-campo');
        if (estado === 'x') {
            input.disabled = false;
            input.classList.remove('bg-gray-50', 'text-gray-900', 'font-medium');
            input.classList.add('bg-white', 'text-gray-900', 'font-semibold', 'border-blue-500');
            // Restaurar valor editado si existe, sino usar el original
            if (valoresEditados[mafId] && valoresEditados[mafId][campo] !== undefined) {
                input.value = valoresEditados[mafId][campo];
            } else if (equipoOriginal) {
                input.value = equipoOriginal[campo] || '';
            }
        } else {
            input.disabled = true;
            input.classList.remove('bg-white', 'text-gray-900', 'font-semibold', 'border-blue-500');
            input.classList.add('bg-gray-50', 'text-gray-900', 'font-medium');
            // Restaurar valor original
            if (equipoOriginal) {
                input.value = equipoOriginal[campo] || '';
            }
        }
    });
    
    // Guardar el estado también
    if (!valoresEditados[mafId]) {
        valoresEditados[mafId] = {};
    }
    valoresEditados[mafId].estado = estado;
}

// Guardar valores editados cuando el usuario escribe en los inputs
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('equipo-input')) {
        const mafId = e.target.getAttribute('data-maf-id');
        const campo = e.target.getAttribute('data-campo');
        if (mafId && campo) {
            if (!valoresEditados[mafId]) {
                valoresEditados[mafId] = {};
            }
            valoresEditados[mafId][campo] = e.target.value;
        }
    }
});

// Guardar inventario
document.getElementById('inventarioForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!tiendaSeleccionada) {
        mostrarModal('Información', 'Por favor seleccione una tienda', 'info');
        return;
    }
    
    // Guardar valores actuales del DOM antes de recopilar (por si hay algún cambio pendiente)
    document.querySelectorAll('[data-maf-id]').forEach(equipoDiv => {
        const mafId = equipoDiv.getAttribute('data-maf-id');
        const estadoRadio = equipoDiv.querySelector('input[type="radio"][name*="[estado]"]:checked');
        if (estadoRadio) {
            if (!valoresEditados[mafId]) {
                valoresEditados[mafId] = {};
            }
            valoresEditados[mafId].estado = estadoRadio.value;
        }
        
        const inputs = equipoDiv.querySelectorAll('.equipo-input');
        inputs.forEach(input => {
            const campo = input.getAttribute('data-campo');
            if (campo) {
                if (!valoresEditados[mafId]) {
                    valoresEditados[mafId] = {};
                }
                valoresEditados[mafId][campo] = input.value;
            }
        });
    });
    
    // Recopilar y enviar datos (usa valoresEditados para todos los equipos, no solo los visibles)
    recopilarYEnviarDatos();
});

function recopilarYEnviarDatos() {
    const equipos = [];
    
    // Recopilar datos de TODOS los equipos desde equiposData (no solo los visibles en el DOM)
    equiposData.categorias.forEach(categoriaData => {
        categoriaData.equipos.forEach(equipo => {
            const mafId = equipo.id;
            
            // Obtener estado y valores editados desde valoresEditados o del DOM
            let estado = 'check';
            let placaValor = '';
            let marcaValor = '';
            let modeloValor = '';
            let serieValor = '';
            
            // Intentar obtener del DOM primero (si está visible y tiene valores actualizados)
            const equipoDiv = document.querySelector(`[data-maf-id="${mafId}"]`);
            if (equipoDiv) {
                const estadoRadio = equipoDiv.querySelector('input[type="radio"][name*="[estado]"]:checked');
                if (estadoRadio) {
                    estado = estadoRadio.value;
                    // Guardar en memoria también
                    if (!valoresEditados[mafId]) {
                        valoresEditados[mafId] = {};
                    }
                    valoresEditados[mafId].estado = estado;
                }
                
                const placaInput = equipoDiv.querySelector('input[name*="[placa_editada]"]');
                const marcaInput = equipoDiv.querySelector('input[name*="[marca_editada]"]');
                const modeloInput = equipoDiv.querySelector('input[name*="[modelo_editado]"]');
                const serieInput = equipoDiv.querySelector('input[name*="[serie_editada]"]');
                
                if (placaInput) {
                    placaValor = (placaInput.value || '').trim();
                    if (!valoresEditados[mafId]) valoresEditados[mafId] = {};
                    valoresEditados[mafId].placa = placaValor;
                }
                if (marcaInput) {
                    marcaValor = (marcaInput.value || '').trim();
                    if (!valoresEditados[mafId]) valoresEditados[mafId] = {};
                    valoresEditados[mafId].marca = marcaValor;
                }
                if (modeloInput) {
                    modeloValor = (modeloInput.value || '').trim();
                    if (!valoresEditados[mafId]) valoresEditados[mafId] = {};
                    valoresEditados[mafId].modelo = modeloValor;
                }
                if (serieInput) {
                    serieValor = (serieInput.value || '').trim();
                    if (!valoresEditados[mafId]) valoresEditados[mafId] = {};
                    valoresEditados[mafId].serie = serieValor;
                }
            }
            
            // Si no está visible o no se encontró en el DOM, usar valores guardados en memoria
            if (!equipoDiv && valoresEditados[mafId]) {
                estado = valoresEditados[mafId].estado || 'check';
                placaValor = (valoresEditados[mafId].placa || '').trim();
                marcaValor = (valoresEditados[mafId].marca || '').trim();
                modeloValor = (valoresEditados[mafId].modelo || '').trim();
                serieValor = (valoresEditados[mafId].serie || '').trim();
            }
            
            // Si el estado es 'x', siempre guardar los valores actuales (editados)
            // Si el estado es 'check', guardar null (no se guardan valores editados cuando está en check)
            equipos.push({
                maf_id: mafId,
                estado: estado,
                placa_editada: estado === 'x' ? (placaValor || null) : null,
                marca_editada: estado === 'x' ? (marcaValor || null) : null,
                modelo_editado: estado === 'x' ? (modeloValor || null) : null,
                serie_editada: estado === 'x' ? (serieValor || null) : null,
            });
        });
    });
    
    const notas = document.getElementById('notas').value.trim();
    
    const data = {
        cr: tiendaSeleccionada.cr,
        plaza: tiendaSeleccionada.plaza,
        notas: notas || null,
        equipos: equipos
    };
    
    fetch('{{ route("inventario.guardar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal('Éxito', 'Inventario guardado exitosamente', 'success');
            // Recargar después de 1.5 segundos
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarModal('Error', 'Error: ' + (data.message || 'Error al guardar'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al guardar el inventario. Por favor intente nuevamente.', 'error');
    });
}
</script>
@endsection

