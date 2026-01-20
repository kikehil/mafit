

<?php $__env->startSection('title', 'Movimiento de Equipos'); ?>
<?php $__env->startSection('page-title', 'Movimiento de Equipos'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="p-4">
        <!-- Selección de Tipo de Movimiento -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Seleccione el tipo de movimiento</h2>
            <div>
                <select 
                    id="tipoMovimientoSelect" 
                    onchange="seleccionarTipoMovimiento(this.value)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white"
                >
                    <option value="">-- Seleccione un tipo de movimiento --</option>
                    <option value="retiro">Retirar Equipo - Retirar equipo sin dejar reemplazo</option>
                    <option value="remplazo_dano">Remplazar por Daño - Remplazar equipo dañado</option>
                    <option value="remplazo_renovacion">Remplazar por Renovación - Remplazar equipo por renovación</option>
                    <option value="agregar">Agregar Equipo - Agregar nuevo equipo a tienda</option>
                    <option value="reingreso_garantia">Reingreso por Garantía - Reingresar equipo de garantía</option>
                </select>
            </div>
        </div>

        <!-- Formularios de Movimiento (ocultos inicialmente) -->
        <div id="formulariosContainer" class="hidden">
            <!-- Formulario para Retiro, Remplazo y Reingreso -->
            <div id="formRetiroRemplazo" class="hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4" id="tituloFormRetiro"></h3>
                    
                    <!-- Búsqueda de Equipo -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Equipo (Placa o Serie)</label>
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                id="buscarEquipoInput" 
                                placeholder="Ingrese placa o serie del equipo..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <button 
                                onclick="buscarEquipo()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                            >
                                Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Resultado de Búsqueda Equipo Retirado -->
                    <div id="resultadoEquipoRetirado" class="hidden mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Equipo Encontrado</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                <input type="text" id="equipoRetiradoDescripcion" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
                                <input type="text" id="equipoRetiradoMarca" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Modelo</label>
                                <input type="text" id="equipoRetiradoModelo" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                <input type="text" id="equipoRetiradoSerie" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                                <input type="text" id="equipoRetiradoActivo" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                                <input type="text" id="equipoRetiradoRemanente" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                        <input type="hidden" id="equipoRetiradoInventariotdaId">
                        <input type="hidden" id="equipoRetiradoMafId">
                        <input type="hidden" id="equipoRetiradoPlaca">
                    </div>

                    <!-- Equipo de Remplazo (solo para remplazo_dano y remplazo_renovacion) -->
                    <div id="seccionRemplazo" class="hidden mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Equipo de Reemplazo</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Equipo de Reemplazo (Placa o Serie)</label>
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    id="buscarEquipoRemplazoInput" 
                                    placeholder="Ingrese placa o serie del equipo de reemplazo..."
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                <button 
                                    onclick="buscarEquipoRemplazo()" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-medium"
                                >
                                    Buscar
                                </button>
                            </div>
                        </div>
                        <div id="resultadoEquipoRemplazo" class="hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción *</label>
                                    <input type="text" id="equipoRemplazoDescripcion" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Marca *</label>
                                    <input type="text" id="equipoRemplazoMarca" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Modelo *</label>
                                    <input type="text" id="equipoRemplazoModelo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                    <input type="text" id="equipoRemplazoSerie" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                                    <input type="text" id="equipoRemplazoActivo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                                    <input type="text" id="equipoRemplazoRemanente" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                </div>
                            </div>
                            <input type="hidden" id="equipoRemplazoInventariotdaId">
                            <input type="hidden" id="equipoRemplazoMafId">
                            <input type="hidden" id="equipoRemplazoPlaca">
                        </div>
                    </div>

                    <!-- Seguimiento (solo para retiro y remplazo_dano) -->
                    <div id="seccionSeguimiento" class="hidden mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seguimiento *</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="seguimiento" value="baja" class="form-radio text-blue-600" required>
                                <span class="ml-2 text-gray-700">Baja</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="seguimiento" value="garantia" class="form-radio text-blue-600" required>
                                <span class="ml-2 text-gray-700">Garantía</span>
                            </label>
                        </div>
                    </div>

                    <!-- Motivo (obligatorio para retiro, remplazo_dano, remplazo_renovacion) -->
                    <div id="seccionMotivo" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del Retiro *</label>
                        <textarea 
                            id="motivoRetiro" 
                            rows="4" 
                            placeholder="Describa el motivo del retiro del equipo..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        ></textarea>
                    </div>

                    <!-- Comentarios (solo para reingreso_garantia) -->
                    <div id="seccionComentarios" class="hidden mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
                        <textarea 
                            id="comentariosReingreso" 
                            rows="4" 
                            placeholder="Agregar comentarios sobre el reingreso..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>

                    <button 
                        onclick="guardarMovimiento()" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-semibold"
                    >
                        Guardar Movimiento
                    </button>
                </div>
            </div>

            <!-- Formulario para Agregar Equipo -->
            <div id="formAgregar" class="hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Equipo</h3>
                    
                    <!-- Búsqueda de Tienda -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Tienda (CR o Nombre)</label>
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                id="buscarTiendaAgregarInput" 
                                placeholder="Ingrese CR o nombre de tienda..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <button 
                                onclick="buscarTiendaAgregar()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                            >
                                Buscar
                            </button>
                        </div>
                        <div id="resultadosTiendaAgregar" class="mt-2 hidden"></div>
                    </div>

                    <!-- Datos de Tienda -->
                    <div id="datosTiendaAgregar" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Datos de la Tienda</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Plaza</label>
                                <input type="text" id="tiendaPlaza" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nombre Plaza</label>
                                <input type="text" id="tiendaNombrePlaza" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">CR</label>
                                <input type="text" id="tiendaCR" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Tienda</label>
                                <input type="text" id="tiendaNombre" readonly class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Equipo a Agregar -->
                    <div id="datosEquipoAgregar" class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Datos del Equipo</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Placa *</label>
                                <input type="text" id="equipoAgregadoPlaca" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                <input type="text" id="equipoAgregadoSerie" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Descripción *</label>
                                <input type="text" id="equipoAgregadoDescripcion" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Marca *</label>
                                <input type="text" id="equipoAgregadoMarca" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Modelo *</label>
                                <input type="text" id="equipoAgregadoModelo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                                <input type="text" id="equipoAgregadoActivo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                                <input type="number" step="0.01" id="equipoAgregadoRemanente" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                    </div>

                    <button 
                        onclick="guardarMovimiento()" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-semibold"
                    >
                        Guardar Movimiento
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación Inventario -->
<div id="modalInventario" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="cerrarModalInventario()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventario de Equipo</h3>
            <p id="mensajeInventario" class="text-gray-700 mb-6"></p>
            <div class="flex gap-3">
                <button 
                    onclick="irACapturaInventario()" 
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                >
                    Sí, realizar inventario
                </button>
                <button 
                    onclick="noRealizarInventario()" 
                    class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium"
                >
                    No
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let tipoMovimientoActual = null;
let crActual = null;
let datosEquipoRetirado = null;
let datosEquipoRemplazo = null;
let datosTiendaAgregar = null;

function seleccionarTipoMovimiento(tipo) {
    if (!tipo) {
        document.getElementById('formulariosContainer').classList.add('hidden');
        tipoMovimientoActual = null;
        return;
    }
    
    tipoMovimientoActual = tipo;
    document.getElementById('formulariosContainer').classList.remove('hidden');
    
    // Ocultar todos los formularios
    document.getElementById('formRetiroRemplazo').classList.add('hidden');
    document.getElementById('formAgregar').classList.add('hidden');
    
    // Mostrar formulario correspondiente
    if (tipo === 'agregar') {
        document.getElementById('formAgregar').classList.remove('hidden');
        resetearFormularioAgregar();
    } else {
        document.getElementById('formRetiroRemplazo').classList.remove('hidden');
        configurarFormularioRetiroRemplazo(tipo);
    }
}

function configurarFormularioRetiroRemplazo(tipo) {
    const titulos = {
        'retiro': 'Retirar Equipo',
        'remplazo_dano': 'Remplazar Equipo por Daño',
        'remplazo_renovacion': 'Remplazar Equipo por Renovación',
        'reingreso_garantia': 'Reingreso por Garantía'
    };
    document.getElementById('tituloFormRetiro').textContent = titulos[tipo];
    
    // Mostrar/ocultar secciones según el tipo
    document.getElementById('seccionRemplazo').classList.toggle('hidden', !['remplazo_dano', 'remplazo_renovacion'].includes(tipo));
    document.getElementById('seccionSeguimiento').classList.toggle('hidden', !['retiro', 'remplazo_dano'].includes(tipo));
    document.getElementById('seccionMotivo').classList.toggle('hidden', tipo === 'reingreso_garantia');
    document.getElementById('seccionComentarios').classList.toggle('hidden', tipo !== 'reingreso_garantia');
    
    resetearFormularioRetiroRemplazo();
}

function resetearFormularioRetiroRemplazo() {
    document.getElementById('buscarEquipoInput').value = '';
    document.getElementById('resultadoEquipoRetirado').classList.add('hidden');
    document.getElementById('buscarEquipoRemplazoInput').value = '';
    document.getElementById('resultadoEquipoRemplazo').classList.add('hidden');
    document.getElementById('motivoRetiro').value = '';
    document.getElementById('comentariosReingreso').value = '';
    document.querySelectorAll('input[name="seguimiento"]').forEach(r => r.checked = false);
    datosEquipoRetirado = null;
    datosEquipoRemplazo = null;
}

function resetearFormularioAgregar() {
    document.getElementById('buscarTiendaAgregarInput').value = '';
    document.getElementById('resultadosTiendaAgregar').classList.add('hidden');
    document.getElementById('datosTiendaAgregar').classList.add('hidden');
    document.getElementById('equipoAgregadoPlaca').value = '';
    document.getElementById('equipoAgregadoSerie').value = '';
    document.getElementById('equipoAgregadoDescripcion').value = '';
    document.getElementById('equipoAgregadoMarca').value = '';
    document.getElementById('equipoAgregadoModelo').value = '';
    document.getElementById('equipoAgregadoActivo').value = '';
    document.getElementById('equipoAgregadoRemanente').value = '';
    datosTiendaAgregar = null;
}

async function buscarEquipo() {
    const placaSerie = document.getElementById('buscarEquipoInput').value.trim();
    if (!placaSerie) {
        mostrarModal('Información', 'Por favor ingrese una placa o serie', 'info');
        return;
    }
    
    try {
        const response = await fetch('<?php echo e(route("movimientos.buscar-equipo")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ placa_serie: placaSerie })
        });
        
        const data = await response.json();
        
        if (data.success) {
            datosEquipoRetirado = data;
            document.getElementById('equipoRetiradoDescripcion').value = data.equipo.descripcion || '';
            document.getElementById('equipoRetiradoMarca').value = data.equipo.marca || '';
            document.getElementById('equipoRetiradoModelo').value = data.equipo.modelo || '';
            document.getElementById('equipoRetiradoSerie').value = data.equipo.serie || '';
            document.getElementById('equipoRetiradoActivo').value = data.equipo.activo || '';
            document.getElementById('equipoRetiradoRemanente').value = data.equipo.remanente ? '$' + parseFloat(data.equipo.remanente).toFixed(2) : '';
            document.getElementById('equipoRetiradoInventariotdaId').value = data.inventariotda_id || '';
            document.getElementById('equipoRetiradoMafId').value = data.maf_id || '';
            document.getElementById('equipoRetiradoPlaca').value = data.equipo.placa || '';
            crActual = data.equipo.cr || null;
            document.getElementById('resultadoEquipoRetirado').classList.remove('hidden');
        } else {
            mostrarModal('Sin resultados', data.message || 'No se encontró el equipo', 'info');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar el equipo', 'error');
    }
}

async function buscarEquipoRemplazo() {
    const placaSerie = document.getElementById('buscarEquipoRemplazoInput').value.trim();
    if (!placaSerie) {
        mostrarModal('Información', 'Por favor ingrese una placa o serie', 'info');
        return;
    }
    
    try {
        const response = await fetch('<?php echo e(route("movimientos.buscar-equipo-remplazo")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ placa_serie: placaSerie })
        });
        
        const data = await response.json();
        
        if (data.success) {
            datosEquipoRemplazo = data;
            document.getElementById('equipoRemplazoDescripcion').value = data.equipo.descripcion || '';
            document.getElementById('equipoRemplazoMarca').value = data.equipo.marca || '';
            document.getElementById('equipoRemplazoModelo').value = data.equipo.modelo || '';
            document.getElementById('equipoRemplazoSerie').value = data.equipo.serie || '';
            document.getElementById('equipoRemplazoActivo').value = data.equipo.activo || '';
            document.getElementById('equipoRemplazoRemanente').value = data.equipo.remanente || '';
            document.getElementById('equipoRemplazoInventariotdaId').value = data.inventariotda_id || '';
            document.getElementById('equipoRemplazoMafId').value = data.maf_id || '';
            document.getElementById('equipoRemplazoPlaca').value = data.equipo.placa || '';
            document.getElementById('resultadoEquipoRemplazo').classList.remove('hidden');
        } else {
            // Si no se encuentra, dejar campos vacíos para captura manual
            document.getElementById('equipoRemplazoPlaca').value = placaSerie;
            document.getElementById('resultadoEquipoRemplazo').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar el equipo', 'error');
    }
}

async function buscarTiendaAgregar() {
    const query = document.getElementById('buscarTiendaAgregarInput').value.trim();
    if (!query) {
        mostrarModal('Información', 'Por favor ingrese un CR o nombre de tienda', 'info');
        return;
    }
    
    try {
        const response = await fetch('<?php echo e(route("movimientos.buscar-tienda")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ query: query })
        });
        
        const data = await response.json();
        
        if (data.success && data.tiendas.length > 0) {
            mostrarResultadosTiendaAgregar(data.tiendas);
        } else {
            mostrarModal('Sin resultados', 'No se encontraron tiendas', 'info');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar tienda', 'error');
    }
}

function mostrarResultadosTiendaAgregar(tiendas) {
    const container = document.getElementById('resultadosTiendaAgregar');
    container.innerHTML = '<div class="space-y-2">' +
        tiendas.map(tienda => `
            <button 
                onclick="seleccionarTiendaAgregar('${tienda.cr}', '${tienda.plaza}', '${tienda.nombre_plaza || ''}', '${tienda.tienda || ''}')"
                class="w-full text-left p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg"
            >
                <div class="font-medium">${tienda.cr}</div>
                <div class="text-sm text-gray-600">${tienda.tienda || 'Sin nombre'}</div>
            </button>
        `).join('') +
        '</div>';
    container.classList.remove('hidden');
}

function seleccionarTiendaAgregar(cr, plaza, nombrePlaza, tienda) {
    datosTiendaAgregar = { cr, plaza, nombre_plaza: nombrePlaza, tienda };
    document.getElementById('tiendaCR').value = cr;
    document.getElementById('tiendaPlaza').value = plaza;
    document.getElementById('tiendaNombrePlaza').value = nombrePlaza;
    document.getElementById('tiendaNombre').value = tienda;
    document.getElementById('datosTiendaAgregar').classList.remove('hidden');
    document.getElementById('resultadosTiendaAgregar').classList.add('hidden');
    crActual = cr;
}

async function guardarMovimiento() {
    // Validaciones básicas
    if (!tipoMovimientoActual) {
        mostrarModal('Información', 'Por favor seleccione un tipo de movimiento', 'info');
        return;
    }
    
    let formData = {
        tipo_movimiento: tipoMovimientoActual,
        realizo_inventario: false
    };
    
    if (tipoMovimientoActual === 'agregar') {
        if (!datosTiendaAgregar) {
            mostrarModal('Información', 'Por favor seleccione una tienda', 'info');
            return;
        }
        formData.cr = datosTiendaAgregar.cr;
        formData.tienda = datosTiendaAgregar.tienda;
        formData.plaza = datosTiendaAgregar.plaza;
        formData.nombre_plaza = datosTiendaAgregar.nombre_plaza;
        formData.equipo_agregado_placa = document.getElementById('equipoAgregadoPlaca').value;
        formData.equipo_agregado_serie = document.getElementById('equipoAgregadoSerie').value;
        formData.equipo_agregado_descripcion = document.getElementById('equipoAgregadoDescripcion').value;
        formData.equipo_agregado_marca = document.getElementById('equipoAgregadoMarca').value;
        formData.equipo_agregado_modelo = document.getElementById('equipoAgregadoModelo').value;
        formData.equipo_agregado_activo = document.getElementById('equipoAgregadoActivo').value;
        formData.equipo_agregado_remanente = document.getElementById('equipoAgregadoRemanente').value || 0;
    } else {
        if (!datosEquipoRetirado) {
            mostrarModal('Información', 'Por favor busque y seleccione el equipo a retirar', 'info');
            return;
        }
        formData.equipo_retirado_placa = document.getElementById('equipoRetiradoPlaca').value;
        formData.equipo_retirado_serie = document.getElementById('equipoRetiradoSerie').value;
        formData.equipo_retirado_inventariotda_id = document.getElementById('equipoRetiradoInventariotdaId').value || null;
        formData.equipo_retirado_maf_id = document.getElementById('equipoRetiradoMafId').value || null;
        
        if (['retiro', 'remplazo_dano', 'remplazo_renovacion'].includes(tipoMovimientoActual)) {
            const motivo = document.getElementById('motivoRetiro').value.trim();
            if (!motivo) {
                mostrarModal('Información', 'Por favor ingrese el motivo del retiro', 'info');
                return;
            }
            formData.motivo = motivo;
        }
        
        if (['retiro', 'remplazo_dano'].includes(tipoMovimientoActual)) {
            const seguimiento = document.querySelector('input[name="seguimiento"]:checked');
            if (!seguimiento) {
                mostrarModal('Información', 'Por favor seleccione el tipo de seguimiento', 'info');
                return;
            }
            formData.seguimiento = seguimiento.value;
        }
        
        if (['remplazo_dano', 'remplazo_renovacion'].includes(tipoMovimientoActual)) {
            formData.equipo_remplazo_placa = document.getElementById('equipoRemplazoPlaca').value;
            formData.equipo_remplazo_serie = document.getElementById('equipoRemplazoSerie').value;
            formData.equipo_remplazo_descripcion = document.getElementById('equipoRemplazoDescripcion').value;
            formData.equipo_remplazo_marca = document.getElementById('equipoRemplazoMarca').value;
            formData.equipo_remplazo_modelo = document.getElementById('equipoRemplazoModelo').value;
            formData.equipo_remplazo_activo = document.getElementById('equipoRemplazoActivo').value;
            formData.equipo_remplazo_remanente = document.getElementById('equipoRemplazoRemanente').value || 0;
            formData.equipo_remplazo_inventariotda_id = document.getElementById('equipoRemplazoInventariotdaId').value || null;
            formData.equipo_remplazo_maf_id = document.getElementById('equipoRemplazoMafId').value || null;
        }
        
        if (tipoMovimientoActual === 'reingreso_garantia') {
            formData.comentarios = document.getElementById('comentariosReingreso').value;
        }
    }
    
    // Validar inventario antes de guardar
    if (crActual) {
        try {
            const response = await fetch('<?php echo e(route("movimientos.validar-inventario")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ cr: crActual })
            });
            
            const data = await response.json();
            
            if (data.success && !data.tiene_inventario_reciente) {
                // Mostrar modal para preguntar si desea realizar inventario
                document.getElementById('mensajeInventario').textContent = data.mensaje + ' ¿Desea registrarlo?';
                document.getElementById('modalInventario').classList.remove('hidden');
                window.formDataPendiente = formData;
                return;
            }
        } catch (error) {
            console.error('Error al validar inventario:', error);
        }
    }
    
    // Si no necesita validación o ya tiene inventario reciente, guardar directamente
    guardarMovimientoFinal(formData);
}

async function guardarMovimientoFinal(formData) {
    try {
        const response = await fetch('<?php echo e(route("movimientos.guardar")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarModal('Éxito', 'Movimiento registrado correctamente', 'success');
            // Resetear formulario
            location.reload();
        } else {
            mostrarModal('Error', data.message || 'Error al guardar el movimiento', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al guardar el movimiento', 'error');
    }
}

function irACapturaInventario() {
    if (window.formDataPendiente) {
        window.formDataPendiente.realizo_inventario = true;
        guardarMovimientoFinal(window.formDataPendiente);
    }
    cerrarModalInventario();
    window.location.href = '<?php echo e(route("inventario.captura")); ?>?cr=' + crActual;
}

function noRealizarInventario() {
    if (window.formDataPendiente) {
        window.formDataPendiente.realizo_inventario = false;
        guardarMovimientoFinal(window.formDataPendiente);
    }
    cerrarModalInventario();
}

function cerrarModalInventario() {
    document.getElementById('modalInventario').classList.add('hidden');
    window.formDataPendiente = null;
}

// Permitir buscar con Enter
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('buscarEquipoInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarEquipo();
    });
    document.getElementById('buscarEquipoRemplazoInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarEquipoRemplazo();
    });
    document.getElementById('buscarTiendaAgregarInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarTiendaAgregar();
    });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/movimientos/index.blade.php ENDPATH**/ ?>