

<?php $__env->startSection('title', 'Captura Inventario PSF'); ?>
<?php $__env->startSection('page-title', 'Captura Inventario PSF'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Captura de Inventario PSF - <?php echo e($plazaUsuario); ?></h2>
            
            <!-- Formulario de Búsqueda con Escaneo -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por PLACA o SERIE</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Ingrese placa/serie o escanee código de barras..."
                            class="w-full px-4 py-3 pr-32 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            autofocus
                        >
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="button"
                            id="btnEscanear"
                            onclick="iniciarEscaneo()"
                            class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium whitespace-nowrap flex items-center gap-2"
                            title="Escanear código de barras"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Escanear
                        </button>
                        <button 
                            type="button"
                            id="btnBuscar"
                            onclick="buscarEquipo()"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium whitespace-nowrap"
                        >
                            Buscar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video para escaneo (oculto por defecto) -->
            <div id="scannerContainer" class="hidden mb-6">
                <div class="relative">
                    <video id="video" width="100%" height="300" class="border rounded-lg"></video>
                    <button 
                        onclick="detenerEscaneo()"
                        class="absolute top-2 right-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Cerrar
                    </button>
                </div>
            </div>

            <!-- Resultado de búsqueda o formulario de captura -->
            <div id="resultadoContainer" class="hidden">
                <div id="equipoEncontrado" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 class="font-semibold text-green-900 mb-3">Equipo encontrado en MAF</h3>
                    <div id="datosEquipo" class="grid grid-cols-2 gap-4"></div>
                </div>

                <form id="formCaptura" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="maf_id" name="maf_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                            <input type="text" id="placa" name="placa" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serie</label>
                            <input type="text" id="serie" name="serie"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <input type="text" id="marca" name="marca"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                            <input type="text" id="modelo" name="modelo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Activo</label>
                            <input type="text" id="activo" name="activo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Año Compra</label>
                            <input type="number" id="anocompra" name="anocompra"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Neto</label>
                            <input type="number" step="0.01" id="valor_neto" name="valor_neto"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remanente</label>
                            <input type="number" step="0.01" id="remanente" name="remanente"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
                            <select id="ubicacion" name="ubicacion" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione ubicación</option>
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
                        <button type="button" onclick="limpiarFormulario()" 
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Limpiar
                        </button>
                        <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Notificación -->
<div id="modalNotificacion" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="cerrarModal()"></div>
        <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10">
            <h3 id="modalTitulo" class="text-lg font-semibold mb-2"></h3>
            <p id="modalMensaje" class="text-gray-600"></p>
            <button onclick="cerrarModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Aceptar
            </button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let scanner = null;
let scanning = false;

function mostrarModal(titulo, mensaje) {
    document.getElementById('modalTitulo').textContent = titulo;
    document.getElementById('modalMensaje').textContent = mensaje;
    document.getElementById('modalNotificacion').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalNotificacion').classList.add('hidden');
}

function iniciarEscaneo() {
    if (scanning) {
        detenerEscaneo();
        return;
    }
    
    const container = document.getElementById('scannerContainer');
    const video = document.getElementById('video');
    const btnEscanear = document.getElementById('btnEscanear');
    
    if (!container || !video) {
        mostrarModal('Error', 'No se pudo inicializar el escáner. Verifique que su dispositivo tenga cámara.');
        return;
    }
    
    container.classList.remove('hidden');
    scanning = true;
    if (btnEscanear) {
        btnEscanear.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Detener';
        btnEscanear.classList.remove('bg-green-600', 'hover:bg-green-700');
        btnEscanear.classList.add('bg-red-600', 'hover:bg-red-700');
    }
    
    try {
        scanner = new Html5Qrcode("video");
        scanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => {
                document.getElementById('searchInput').value = decodedText;
                scanner.stop().then(() => {
                    scanner = null;
                }).catch(() => {});
                container.classList.add('hidden');
                scanning = false;
                if (btnEscanear) {
                    btnEscanear.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Escanear';
                    btnEscanear.classList.remove('bg-red-600', 'hover:bg-red-700');
                    btnEscanear.classList.add('bg-green-600', 'hover:bg-green-700');
                }
                buscarEquipo();
            },
            (errorMessage) => {
                // Ignorar errores de escaneo continuo
            }
        );
    } catch (error) {
        console.error('Error al iniciar escáner:', error);
        mostrarModal('Error', 'No se pudo acceder a la cámara. Verifique los permisos del navegador.');
        container.classList.add('hidden');
        scanning = false;
        if (btnEscanear) {
            btnEscanear.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Escanear';
            btnEscanear.classList.remove('bg-red-600', 'hover:bg-red-700');
            btnEscanear.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    }
}

function detenerEscaneo() {
    const container = document.getElementById('scannerContainer');
    const btnEscanear = document.getElementById('btnEscanear');
    
    if (scanner) {
        scanner.stop().then(() => {
            scanner = null;
        }).catch((err) => {
            console.error('Error al detener escáner:', err);
            scanner = null;
        });
    }
    
    if (container) {
        container.classList.add('hidden');
    }
    
    if (btnEscanear) {
        btnEscanear.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Escanear';
        btnEscanear.classList.remove('bg-red-600', 'hover:bg-red-700');
        btnEscanear.classList.add('bg-green-600', 'hover:bg-green-700');
    }
    
    scanning = false;
}

function buscarEquipo() {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) {
        mostrarModal('Error', 'Por favor ingrese una placa o serie');
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
                llenarFormulario(data.equipo);
                document.getElementById('equipoEncontrado').classList.remove('hidden');
                mostrarDatosEquipo(data.equipo);
            } else {
                limpiarFormulario();
                document.getElementById('equipoEncontrado').classList.add('hidden');
            }
            document.getElementById('resultadoContainer').classList.remove('hidden');
            if (typeof mostrarBotonVolverArriba === 'function') {
                mostrarBotonVolverArriba();
            }
        } else {
            mostrarModal('Error', data.message || 'Error al buscar equipo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al buscar equipo');
    });
}

function mostrarDatosEquipo(equipo) {
    const container = document.getElementById('datosEquipo');
    container.innerHTML = `
        <div><strong>Plaza:</strong> ${equipo.nombre_plaza || equipo.plaza || '-'}</div>
        <div><strong>Tienda:</strong> ${equipo.nombre_tienda || equipo.cr || '-'}</div>
        <div><strong>Marca:</strong> ${equipo.marca || '-'}</div>
        <div><strong>Modelo:</strong> ${equipo.modelo || '-'}</div>
        <div><strong>Serie:</strong> ${equipo.serie || '-'}</div>
        <div><strong>Activo:</strong> ${equipo.activo || '-'}</div>
        <div><strong>Año Compra:</strong> ${equipo.anocompra || '-'}</div>
        <div><strong>Valor Neto:</strong> ${equipo.valor_neto ? '$' + parseFloat(equipo.valor_neto).toFixed(2) : '-'}</div>
        <div><strong>Remanente:</strong> ${equipo.remanente ? '$' + parseFloat(equipo.remanente).toFixed(2) : '-'}</div>
    `;
}

function llenarFormulario(equipo) {
    document.getElementById('maf_id').value = equipo.maf_id || '';
    document.getElementById('placa').value = equipo.placa || '';
    document.getElementById('serie').value = equipo.serie || '';
    document.getElementById('marca').value = equipo.marca || '';
    document.getElementById('modelo').value = equipo.modelo || '';
    document.getElementById('activo').value = equipo.activo || '';
    document.getElementById('anocompra').value = equipo.anocompra || '';
    document.getElementById('valor_neto').value = equipo.valor_neto || '';
    document.getElementById('remanente').value = equipo.remanente || '';
}

function limpiarFormulario() {
    document.getElementById('formCaptura').reset();
    document.getElementById('maf_id').value = '';
    document.getElementById('equipoEncontrado').classList.add('hidden');
    document.getElementById('resultadoContainer').classList.add('hidden');
    document.getElementById('searchInput').value = '';
}

document.getElementById('formCaptura').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo e(route("inventario-psf.guardar")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal('Éxito', 'Inventario guardado exitosamente');
            limpiarFormulario();
        } else {
            mostrarModal('Error', data.message || 'Error al guardar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal('Error', 'Error al guardar el inventario');
    });
});

// Permitir búsqueda con Enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        buscarEquipo();
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/inventario-psf/captura.blade.php ENDPATH**/ ?>