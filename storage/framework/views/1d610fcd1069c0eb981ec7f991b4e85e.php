

<?php $__env->startSection('title', 'Captura Inventario PFS'); ?>
<?php $__env->startSection('page-title', 'Captura Inventario PFS'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Formulario de Búsqueda -->
        <div class="bg-white shadow-sm border-b rounded-lg mb-6 p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Buscar Equipo</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Ingrese PLACA o SERIE..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                        autocomplete="off"
                    >
                </div>
                <button 
                    id="btnBuscar" 
                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium whitespace-nowrap"
                >
                    Buscar
                </button>
            </div>
        </div>

        <!-- Resultados / Formulario de Captura -->
        <div id="formContainer" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Datos del Equipo</h3>
                
                <form id="formInventarioPFS">
                    <input type="hidden" id="maf_id" name="maf_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plaza</label>
                            <input 
                                type="text" 
                                id="plaza" 
                                name="plaza"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Plaza</label>
                            <input 
                                type="text" 
                                id="nombre_plaza" 
                                name="nombre_plaza"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CR</label>
                            <input 
                                type="text" 
                                id="cr" 
                                name="cr"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Tienda</label>
                            <input 
                                type="text" 
                                id="nombre_tienda" 
                                name="nombre_tienda"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placa <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                id="placa" 
                                name="placa"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <input 
                                type="text" 
                                id="marca" 
                                name="marca"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                            <input 
                                type="text" 
                                id="modelo" 
                                name="modelo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serie</label>
                            <input 
                                type="text" 
                                id="serie" 
                                name="serie"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Activo</label>
                            <input 
                                type="text" 
                                id="activo" 
                                name="activo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Año Compra</label>
                            <input 
                                type="number" 
                                id="anocompra" 
                                name="anocompra"
                                min="1900"
                                max="<?php echo e(date('Y')); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Neto</label>
                            <input 
                                type="number" 
                                id="valor_neto" 
                                name="valor_neto"
                                step="0.01"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remanente</label>
                            <input 
                                type="number" 
                                id="remanente" 
                                name="remanente"
                                step="0.01"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación <span class="text-red-500">*</span></label>
                            <select 
                                id="ubicacion" 
                                name="ubicacion"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Seleccione una ubicación</option>
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
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button 
                            type="button" 
                            id="btnCancelar"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                        >
                            Guardar
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
            <p class="text-gray-500 text-lg">Busque un equipo por placa o serie para comenzar</p>
        </div>
    </div>
</div>

<script>
document.getElementById('btnBuscar').addEventListener('click', buscarEquipo);
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        buscarEquipo();
    }
});

document.getElementById('btnCancelar').addEventListener('click', function() {
    document.getElementById('formContainer').classList.add('hidden');
    document.getElementById('emptyState').classList.remove('hidden');
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInput').focus();
});

document.getElementById('formInventarioPFS').addEventListener('submit', guardarInventario);

function buscarEquipo() {
    const query = document.getElementById('searchInput').value.trim();
    
    if (!query) {
        mostrarModal('Información', 'Por favor ingrese una placa o serie', 'info');
        return;
    }

    fetch('<?php echo e(route("inventario-psf.buscar-equipo")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ query })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.encontrado) {
                llenarFormulario(data.equipo, true);
            } else {
                // Limpiar formulario para captura manual
                limpiarFormulario();
                document.getElementById('placa').value = query;
                document.getElementById('formContainer').classList.remove('hidden');
                document.getElementById('emptyState').classList.add('hidden');
            }
        } else {
            mostrarModal('Error', data.message || 'Error al buscar equipo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar equipo. Por favor intente nuevamente.', 'error');
    });
}

function llenarFormulario(equipo, encontrado) {
    document.getElementById('maf_id').value = equipo.maf_id || '';
    document.getElementById('plaza').value = equipo.plaza || '';
    document.getElementById('nombre_plaza').value = equipo.nombre_plaza || '';
    document.getElementById('cr').value = equipo.cr || '';
    document.getElementById('nombre_tienda').value = equipo.nombre_tienda || '';
    document.getElementById('placa').value = equipo.placa || '';
    document.getElementById('marca').value = equipo.marca || '';
    document.getElementById('modelo').value = equipo.modelo || '';
    document.getElementById('serie').value = equipo.serie || '';
    document.getElementById('activo').value = equipo.activo || '';
    document.getElementById('anocompra').value = equipo.anocompra || '';
    document.getElementById('valor_neto').value = equipo.valor_neto || '';
    document.getElementById('remanente').value = equipo.remanente || '';
    
    // Si no se encontró, hacer campos editables
    if (!encontrado) {
        document.getElementById('placa').readOnly = false;
        document.getElementById('marca').readOnly = false;
        document.getElementById('modelo').readOnly = false;
        document.getElementById('serie').readOnly = false;
        document.getElementById('activo').readOnly = false;
        document.getElementById('anocompra').readOnly = false;
        document.getElementById('valor_neto').readOnly = false;
        document.getElementById('remanente').readOnly = false;
    } else {
        // Si se encontró, solo placa es editable
        document.getElementById('placa').readOnly = false;
        document.getElementById('marca').readOnly = true;
        document.getElementById('modelo').readOnly = true;
        document.getElementById('serie').readOnly = true;
        document.getElementById('activo').readOnly = true;
        document.getElementById('anocompra').readOnly = true;
        document.getElementById('valor_neto').readOnly = true;
        document.getElementById('remanente').readOnly = true;
    }
    
    document.getElementById('formContainer').classList.remove('hidden');
    document.getElementById('emptyState').classList.add('hidden');
}

function limpiarFormulario() {
    document.getElementById('maf_id').value = '';
    document.getElementById('plaza').value = '';
    document.getElementById('nombre_plaza').value = '';
    document.getElementById('cr').value = '';
    document.getElementById('nombre_tienda').value = '';
    document.getElementById('marca').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('serie').value = '';
    document.getElementById('activo').value = '';
    document.getElementById('anocompra').value = '';
    document.getElementById('valor_neto').value = '';
    document.getElementById('remanente').value = '';
    document.getElementById('ubicacion').value = '';
    
    // Hacer todos los campos editables
    document.getElementById('placa').readOnly = false;
    document.getElementById('marca').readOnly = false;
    document.getElementById('modelo').readOnly = false;
    document.getElementById('serie').readOnly = false;
    document.getElementById('activo').readOnly = false;
    document.getElementById('anocompra').readOnly = false;
    document.getElementById('valor_neto').readOnly = false;
    document.getElementById('remanente').readOnly = false;
}

function guardarInventario(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Convertir valores vacíos a null
    Object.keys(data).forEach(key => {
        if (data[key] === '') {
            data[key] = null;
        }
    });
    
    fetch('<?php echo e(route("inventario-psf.guardar")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal('Éxito', 'Inventario PFS guardado exitosamente', 'success');
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


// Mostrar botón flotante cuando se carga el formulario
document.addEventListener('DOMContentLoaded', function() {
    if (typeof mostrarBotonVolverArriba === 'function') {
        mostrarBotonVolverArriba();
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/inventario-pfs/captura.blade.php ENDPATH**/ ?>