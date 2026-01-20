<?php $__env->startSection('title', 'Consulta Inventario'); ?>
<?php $__env->startSection('page-title', 'Consulta Inventario'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 pb-8">
    <!-- Búsqueda de Tienda -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-10">
    <div class="p-4">
            <div class="flex gap-2 mb-3">
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
            
            <!-- Filtro de Categoría -->
            <div id="filtroCategoriaContainer" class="mt-3 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Categoría</label>
                <select 
                    id="filtroCategoria"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Todas las categorías</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Contenedor de Equipos -->
    <div id="equiposContainer" class="hidden">
        <div class="p-4">
            <div id="tiendaInfo" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-gray-900" id="tiendaNombre"></h3>
                <p class="text-sm text-gray-700 font-medium" id="tiendaCR"></p>
        </div>

            <div id="equiposPorCategoria"></div>
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

<!-- Modal de Sin Inventarios Previos -->
<div id="modalSinInventario" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div id="modalSinInventarioOverlay" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <!-- Modal centrado -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 id="modalSinInventarioTitulo" class="text-lg leading-6 font-medium text-gray-900">Sin Inventarios Previos</h3>
                        <div class="mt-2">
                            <p id="modalSinInventarioMensaje" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                    type="button" 
                    id="modalSinInventarioBtnCerrar"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let tiendaSeleccionada = null;
let equiposData = {};
let categoriasDisponibles = [];

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

// Filtro de categoría
document.getElementById('filtroCategoria').addEventListener('change', function() {
    if (tiendaSeleccionada) {
        obtenerEquipos();
        // Mostrar botón flotante cuando se selecciona un filtro
        if (typeof mostrarBotonVolverArriba === 'function') {
            mostrarBotonVolverArriba();
        }
    }
});

function buscarTienda() {
    const query = document.getElementById('searchTienda').value.trim();
    if (!query) {
        mostrarModal('Información', 'Por favor ingrese un CR o nombre de tienda', 'info');
        return;
    }

    fetch('<?php echo e(route("inventario.buscar-tienda-consulta")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ query })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.tiendas.length > 0) {
            mostrarResultadosTienda(data.tiendas);
        } else {
            const query = document.getElementById('searchTienda').value.trim();
            mostrarModal(query || 'Búsqueda', 'No ha tenido inventario de equipos', 'info');
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
    obtenerEquipos();
}

function obtenerEquipos() {
    if (!tiendaSeleccionada) return;
    
    const categoriaFiltro = document.getElementById('filtroCategoria').value;
    
    fetch('<?php echo e(route("inventario.obtener-equipos-consulta")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ 
            cr: tiendaSeleccionada.cr, 
            plaza: tiendaSeleccionada.plaza,
            categoria: categoriaFiltro
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            equiposData = data;
            categoriasDisponibles = data.categorias_disponibles || [];
            mostrarEquipos(data);
            actualizarFiltroCategoria();
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

function actualizarFiltroCategoria() {
    const select = document.getElementById('filtroCategoria');
    const container = document.getElementById('filtroCategoriaContainer');
    
    // Limpiar opciones existentes excepto "Todas"
    select.innerHTML = '<option value="">Todas las categorías</option>';
    
    // Agregar categorías disponibles
    categoriasDisponibles.forEach(categoria => {
        const option = document.createElement('option');
        option.value = categoria;
        option.textContent = categoria;
        select.appendChild(option);
    });
    
    // Mostrar el filtro
    container.classList.remove('hidden');
}

function mostrarEquipos(data) {
    document.getElementById('emptyState').classList.add('hidden');
    
    const tiendaNombre = data.tienda.tienda || data.tienda.cr;
    document.getElementById('tiendaNombre').textContent = tiendaNombre;
    document.getElementById('tiendaCR').textContent = `CR: ${data.tienda.cr}`;
    
    const container = document.getElementById('equiposPorCategoria');
    container.innerHTML = '';
    
    if (data.categorias.length === 0) {
        // Mostrar modal indicando que la tienda no ha tenido inventario de equipos
        mostrarModalSinInventario(tiendaNombre);
        document.getElementById('equiposContainer').classList.add('hidden');
        return;
    }
    
    document.getElementById('equiposContainer').classList.remove('hidden');
    
    // Mantener el orden del array
    data.categorias.forEach(categoriaData => {
        const categoriaDiv = document.createElement('div');
        categoriaDiv.className = 'mb-6';
        categoriaDiv.innerHTML = `
            <h4 class="text-xl font-bold text-gray-900 mb-4 px-4 py-3 rounded-lg shadow-md bg-blue-200 border-2 border-blue-400">${categoriaData.categoria || 'Sin categoría'}</h4>
            <div class="space-y-3">
                ${categoriaData.equipos.map(equipo => crearFilaEquipo(equipo)).join('')}
            </div>
        `;
        container.appendChild(categoriaDiv);
    });
}

function crearFilaEquipo(equipo) {
    const estadoIcon = equipo.estado === 'check' ? '✓' : '✗';
    const estadoColor = equipo.estado === 'check' ? 'text-green-600' : 'text-red-600';
    
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
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="font-semibold text-gray-900 text-base flex-1">${equipo.descripcion || '-'}</div>
                ${etiquetasEstado ? `<div class="flex flex-wrap gap-2 ml-2">${etiquetasEstado}</div>` : ''}
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Placa:</label>
                    <span class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium">${equipo.placa || '-'}</span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Marca:</label>
                    <span class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium">${equipo.marca || '-'}</span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Modelo:</label>
                    <span class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium">${equipo.modelo || '-'}</span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 w-20">Serie:</label>
                    <span class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-medium">${equipo.serie || '-'}</span>
                </div>
                <div class="flex items-center justify-end gap-4 mt-3 pt-3 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-700">Estado:</label>
                    <span class="text-lg ${estadoColor} font-semibold">${estadoIcon}</span>
                </div>
            </div>
        </div>
    `;
}

// Funciones para el modal de sin inventario
function mostrarModalSinInventario(tiendaNombre) {
    const modal = document.getElementById('modalSinInventario');
    const modalMensaje = document.getElementById('modalSinInventarioMensaje');
    
    modalMensaje.textContent = `La tienda "${tiendaNombre}" no ha tenido inventario de equipos anteriormente.`;
    
    modal.classList.remove('hidden');
}

function cerrarModalSinInventario() {
    const modal = document.getElementById('modalSinInventario');
    modal.classList.add('hidden');
}

// Event listeners del modal
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalOverlay').addEventListener('click', cerrarModal);
    document.getElementById('modalBtnCerrar').addEventListener('click', cerrarModal);
    document.getElementById('modalSinInventarioOverlay').addEventListener('click', cerrarModalSinInventario);
    document.getElementById('modalSinInventarioBtnCerrar').addEventListener('click', cerrarModalSinInventario);
});
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/inventario/consulta.blade.php ENDPATH**/ ?>