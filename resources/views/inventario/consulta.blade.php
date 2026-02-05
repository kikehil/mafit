@extends('layouts.app')

@section('title', 'Consulta Inventario')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header with Glassmorphism -->
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-20 z-30 transition-all duration-300 hover:shadow-md">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Consulta de Inventario</h1>
            <p class="text-slate-500 text-sm mt-1">Busque equipos por tienda o CR</p>
        </div>
        
        <!-- Search Bar -->
        <div class="w-full md:w-[32rem] flex gap-3">
             <div class="relative group flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchTienda" 
                    placeholder="Ingrese CR o nombre de tienda..."
                    class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all shadow-sm group-hover:bg-white"
                    autocomplete="off"
                >
            </div>
            <button 
                id="btnBuscar"
                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold tracking-wide transition-all active:scale-[0.98]"
            >
                Buscar
            </button>
        </div>
    </div>

    <!-- Filtro de Categoría (Oculto inicialmente) -->
    <div id="filtroCategoriaContainer" class="hidden animate-fade-in-down">
         <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-4">
            <label class="text-sm font-semibold text-slate-700 whitespace-nowrap">Filtrar por Categoría:</label>
            <div class="relative flex-1 max-w-ws">
                <select 
                    id="filtroCategoria"
                    class="block w-full pl-3 pr-10 py-2 text-base border-slate-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg"
                >
                    <option value="">Todas las categorías</option>
                </select>
            </div>
         </div>
    </div>

    <!-- Resultados de Tienda (Lista desplegable) -->
    <div id="tiendaResults" class="hidden bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden divide-y divide-slate-100 animate-fade-in"></div>

    <!-- Contenedor de Equipos -->
    <div id="equiposContainer" class="hidden space-y-6 animate-fade-in-up">
        <!-- Info Tienda Seleccionada -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight mb-1" id="tiendaNombre"></h2>
                    <div class="flex items-center gap-3">
                         <span class="px-2.5 py-0.5 rounded-lg bg-blue-500/20 text-blue-200 text-sm font-bold font-mono border border-blue-500/30" id="tiendaCR"></span>
                         <span class="text-slate-400 text-sm">Inventario de Equipos</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="equiposPorCategoria" class="space-y-8"></div>
    </div>

    <!-- Estado vacío -->
    <div id="emptyState" class="flex flex-col items-center justify-center min-h-[40vh] text-center p-8">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Busque una tienda</h3>
        <p class="text-slate-500 mt-2 max-w-md">Ingrese el CR o nombre de la tienda arriba para consultar su inventario de equipos.</p>
    </div>
</div>

<!-- Modal de Fotos (Local implementation simplified, relies less on global if specific image gallery needed, but styling updated) -->
<div id="modalFotos" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" id="modalFotosOverlay"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
             <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start w-full">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl font-bold leading-6 text-slate-900 mb-6" id="modalFotosTitulo"></h3>
                            <div id="modalFotosContenedor" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Las imágenes se insertarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                    <button type="button" id="modalFotosBtnCerrar" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all active:scale-95">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let tiendaSeleccionada = null;
let equiposData = {};
let categoriasDisponibles = [];

// Búsqueda de tienda - solo cuando se presiona Enter o se hace clic en buscar
let searchTimeout;
document.getElementById('searchTienda').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim();
    
    // Si el campo está vacío, ocultar resultados
    if (query.length === 0) {
        document.getElementById('tiendaResults').classList.add('hidden');
        return;
    }
});

// Búsqueda con clic en el botón
document.getElementById('btnBuscar').addEventListener('click', function() {
    clearTimeout(searchTimeout);
    buscarTienda();
});

// Búsqueda con Enter
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
    }
});

// Auto-busqueda si existe parametro CR
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const crParam = urlParams.get('cr');
    if (crParam) {
        const input = document.getElementById('searchTienda');
        if (input) {
            input.value = crParam;
            // Pequeño delay para asegurar que todo esté cargado
            setTimeout(() => {
                buscarTienda();
            }, 100);
        }
    }
});

function buscarTienda() {
    const query = document.getElementById('searchTienda').value.trim();
    if (!query) {
        mostrarModal('Información', 'Por favor ingrese un CR o nombre de tienda', 'info');
        return;
    }

    // Ocultar resultados anteriores si existen
    document.getElementById('tiendaResults').classList.add('hidden');

    fetch('{{ route("inventario.buscar-tienda-consulta") }}', {
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
            // Updated to use GLOBAL MODAL
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
    container.innerHTML = tiendas.map(tienda => `
            <button 
                onclick="seleccionarTienda('${tienda.cr}', '${tienda.plaza || ''}')"
                class="w-full text-left p-4 hover:bg-slate-50 transition-all flex items-center justify-between group"
            >
                <div>
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 mb-1">${tienda.cr}</span>
                    <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">${tienda.tienda || 'Sin nombre'}</div>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        `).join('');
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
    
    fetch('{{ route("inventario.obtener-equipos-consulta") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    // Guardar valor actual para restaurarlo si es posible
    const valorActual = select.value;
    
    // Limpiar opciones existentes excepto "Todas"
    select.innerHTML = '<option value="">Todas las categorías</option>';
    
    // Agregar categorías disponibles
    categoriasDisponibles.forEach(categoria => {
        const option = document.createElement('option');
        option.value = categoria;
        option.textContent = categoria;
        select.appendChild(option);
    });

    // Restaurar valor si aun existe en las opciones
    if (categoriasDisponibles.includes(valorActual)) {
        select.value = valorActual;
    }
    
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
        // Usar Global Modal
        mostrarModal(tiendaNombre, `La tienda "${tiendaNombre}" no ha tenido inventario de equipos anteriormente.`, 'info');
        document.getElementById('equiposContainer').classList.add('hidden');
        document.getElementById('emptyState').classList.remove('hidden');
        return;
    }
    
    document.getElementById('equiposContainer').classList.remove('hidden');
    
    // Mantener el orden del array
    data.categorias.forEach(categoriaData => {
        const categoriaDiv = document.createElement('div');
        categoriaDiv.className = 'bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden';
        categoriaDiv.innerHTML = `
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h4 class="text-lg font-bold text-slate-800">${categoriaData.categoria || 'Sin categoría'}</h4>
                <span class="text-xs font-semibold bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full">${categoriaData.equipos.length} Equipos</span>
            </div>
            <div class="divide-y divide-slate-100">
                ${categoriaData.equipos.map(equipo => crearFilaEquipo(equipo)).join('')}
            </div>
        `;
        container.appendChild(categoriaDiv);
    });
}

function crearFilaEquipo(equipo) {
    const estadoIcon = equipo.estado === 'check' ? 
        '<div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>' : 
        '<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></div>';
    
    // Icono de fotos si tiene imágenes
    let iconoFotos = '';
    const tieneFotos = (equipo.foto1 || equipo.foto2);
    if (tieneFotos) {
        const cantidadFotos = (equipo.foto1 ? 1 : 0) + (equipo.foto2 ? 1 : 0);
        const foto1Escapada = equipo.foto1 ? equipo.foto1.replace(/'/g, "\\'") : '';
        const foto2Escapada = equipo.foto2 ? equipo.foto2.replace(/'/g, "\\'") : '';
        const descripcionEscapada = (equipo.descripcion || 'Equipo').replace(/'/g, "\\'");
        iconoFotos = `
            <button 
                type="button"
                onclick="mostrarFotosEquipo('${equipo.id}', '${descripcionEscapada}', '${foto1Escapada}', '${foto2Escapada}')"
                class="ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors"
                title="Ver fotos"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>${cantidadFotos}</span>
            </button>
        `;
    }
    
    // Generar etiquetas de estado de movimiento
    let etiquetasEstado = '';
    
    // Verificar movimientos del equipo
    if (equipo.movimientos && equipo.movimientos.length > 0) {
        const movimientos = equipo.movimientos;
        movimientos.forEach(mov => {
            if (mov.tipo === 'retiro' && mov.seguimiento) {
                const color = mov.seguimiento === 'baja' ? 'rose' : 'amber';
                const texto = mov.seguimiento === 'baja' ? 'BAJA' : 'GARANTÍA';
                etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-${color}-100 text-${color}-700 mr-2 border border-${color}-200 shadow-sm">${texto}</span>`;
            } else if (mov.tipo === 'remplazo_dano' || mov.tipo === 'remplazo_renovacion') {
                if (mov.fue_remplazado_por) {
                    etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-orange-100 text-orange-700 mr-2 border border-orange-200" title="Remplazado por ${mov.fue_remplazado_por}">REMPLAZADO</span>`;
                }
                if (mov.seguimiento) {
                    const color = mov.seguimiento === 'baja' ? 'rose' : 'amber';
                    const texto = mov.seguimiento === 'baja' ? 'BAJA' : 'GARANTÍA';
                    etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-${color}-100 text-${color}-700 mr-2 border border-${color}-200 shadow-sm">${texto}</span>`;
                }
            } else if (mov.tipo === 'remplazo_recibido') {
                 etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-emerald-100 text-emerald-700 mr-2 border border-emerald-200 shadow-sm">NUEVO/REMPLAZO</span>`;
            } else if (mov.tipo === 'agregado') {
                etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-purple-100 text-purple-700 mr-2 border border-purple-200 shadow-sm">AGREGADO</span>`;
            }
        });
    }
    
    // Verificar estado de seguimiento desde inventariotda
    if (equipo.estado_movimiento) {
        const estado = equipo.estado_movimiento;
        if (estado.tipo === 'seguimiento' && estado.valor) {
             const color = estado.valor === 'baja' ? 'rose' : 'amber';
             const texto = estado.valor === 'baja' ? 'BAJA' : 'GARANTÍA';
            if (!etiquetasEstado.includes(texto)) {
                etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-${color}-100 text-${color}-700 mr-2 border border-${color}-200 shadow-sm">${texto}</span>`;
            }
        } else if (estado.tipo === 'en_garantia') {
            if (!etiquetasEstado.includes('GARANTÍA')) {
                 etiquetasEstado += `<span class="px-2 py-0.5 text-xs font-bold rounded-md bg-amber-100 text-amber-700 mr-2 border border-amber-200 shadow-sm">EN GARANTÍA</span>`;
            }
        }
    }
    
    return `
        <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-bold text-slate-800 text-sm md:text-base">${equipo.descripcion || '-'}</span>
                        ${iconoFotos}
                        ${etiquetasEstado}
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-y-2 gap-x-6 text-sm">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-semibold uppercase">Placa</span>
                            <span class="text-slate-700 font-mono">${equipo.placa || '-'}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-semibold uppercase">Marca</span>
                            <span class="text-slate-700">${equipo.marca || '-'}</span>
                        </div>
                        <div class="flex flex-col">
                             <span class="text-xs text-slate-400 font-semibold uppercase">Modelo</span>
                            <span class="text-slate-700">${equipo.modelo || '-'}</span>
                        </div>
                         <div class="flex flex-col">
                             <span class="text-xs text-slate-400 font-semibold uppercase">Serie</span>
                            <span class="text-slate-700 font-mono">${equipo.serie || '-'}</span>
                        </div>
                    </div>
                </div>
                
                 <div class="flex items-center gap-4 pl-4 border-l border-slate-100">
                    <div class="flex flex-col items-center">
                        ${estadoIcon}
                        <span class="text-[10px] uppercase font-bold text-slate-400 mt-1">Estado</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Funciones para el modal de fotos
function mostrarFotosEquipo(equipoId, descripcion, foto1, foto2) {
    const modal = document.getElementById('modalFotos');
    const modalTitulo = document.getElementById('modalFotosTitulo');
    const modalContenedor = document.getElementById('modalFotosContenedor');
    
    modalTitulo.textContent = `Fotos del equipo: ${descripcion}`;
    modalContenedor.innerHTML = '';
    
    const fotos = [foto1, foto2].filter(foto => foto && foto !== '' && foto !== 'null' && foto !== null);
    
    if (fotos.length === 0) {
        modalContenedor.innerHTML = '<p class="text-slate-500 text-center col-span-2 py-8 italic">No hay fotos disponibles para este equipo</p>';
    } else {
        fotos.forEach((foto, index) => {
            const fotoDiv = document.createElement('div');
            fotoDiv.className = 'group relative aspect-video bg-slate-100 rounded-xl overflow-hidden shadow-sm border border-slate-200';
            
            const img = document.createElement('img');
            img.src = foto;
            img.alt = `Foto ${index + 1}`;
            img.className = 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer';
            img.onclick = function() {
                window.open(foto, '_blank');
            };
            img.onerror = function() {
                 this.parentElement.innerHTML = `
                    <div class="flex flex-col items-center justify-center w-full h-full text-slate-400">
                         <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-sm font-medium">Imagen no disponible</span>
                    </div>
                 `;
            };
            
            const label = document.createElement('div');
            label.className = 'absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 pt-8';
            label.innerHTML = `<span class="text-white text-xs font-bold tracking-wide">FOTO ${index + 1}</span>`;
            
            fotoDiv.appendChild(img);
            fotoDiv.appendChild(label);
            modalContenedor.appendChild(fotoDiv);
        });
    }
    
    modal.classList.remove('hidden');
}

function cerrarModalFotos() {
    const modal = document.getElementById('modalFotos');
    modal.classList.add('hidden');
}

// Event listeners del modal
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalFotosOverlay').addEventListener('click', cerrarModalFotos);
    document.getElementById('modalFotosBtnCerrar').addEventListener('click', cerrarModalFotos);
});
</script>
@endsection
