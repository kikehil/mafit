<?php $__env->startSection('title', 'Inicio'); ?>
<?php $__env->startSection('page-title', 'Inicio'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full <?php echo e(isset($results) ? 'max-w-7xl' : 'max-w-2xl'); ?> px-4 md:px-4">
        <!-- Logo/Título -->
        <div class="text-center mb-8">
            <p class="text-gray-600 text-lg">Sistema de Consulta de Activos Fijos</p>
        </div>

        <!-- Formulario de Búsqueda -->
        <form action="<?php echo e(route('dashboard.search')); ?>" method="POST" class="mb-6">
            <?php echo csrf_field(); ?>
            <div class="relative">
                <div class="flex items-center border-2 border-gray-300 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-200 focus-within:border-blue-500 focus-within:shadow-xl">
                    <!-- Icono de búsqueda -->
                    <div class="pl-6 pr-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Campo de búsqueda -->
                    <input 
                        type="text" 
                        name="query" 
                        value="<?php echo e(old('query', $query ?? '')); ?>"
                        placeholder="Buscar por PLACA o SERIE..."
                        autofocus
                        class="flex-1 py-4 px-2 text-lg outline-none bg-transparent text-gray-900 placeholder-gray-400"
                        required
                    >
                    
                    <!-- Botón de búsqueda -->
                    <button 
                        type="submit" 
                        class="mr-2 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
                    >
                        Buscar
                    </button>
                </div>
            </div>
        </form>

        <!-- Resultados -->
        <?php if(isset($results)): ?>
            <?php if($results->isEmpty()): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8 w-full">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Resultados de la búsqueda
                            </h3>
                            <span class="text-sm text-gray-600">
                                <?php echo e($results->count()); ?> <?php echo e($results->count() === 1 ? 'equipo encontrado' : 'equipos encontrados'); ?>

                            </span>
                        </div>
                        <?php if($currentBatch): ?>
                            <p class="text-sm text-gray-500 mt-1">
                                Lote: <span class="font-medium"><?php echo e($currentBatch->period); ?></span>
                                (<?php echo e($currentBatch->finished_at?->format('d/m/Y H:i')); ?>)
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No se encontraron equipos con "<?php echo e($query); ?>"</p>
                        <p class="text-gray-400 text-sm mt-2">Intente con otra PLACA o SERIE</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Vista móvil: Cards con grid de 3 columnas -->
                <div class="block md:hidden mt-8 space-y-4 px-4">
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-3">
                        <div class="grid grid-cols-3 gap-2 border border-slate-200 rounded-lg overflow-hidden">
                            <!-- FILA 1: NOMBRE plaza | NOMBRE tienda | placa -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">NOMBRE plaza</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->plazaRelation?->plaza_nom ?? $equipo->plaza ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">NOMBRE tienda</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->tienda ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">placa</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->placa ?? '-'); ?></div>
                            </div>
                            
                            <!-- FILA 2: descripcion | marca | modelo -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">descripcion</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->descripcion ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">marca</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->marca ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">modelo</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->modelo ?? '-'); ?></div>
                            </div>
                            
                            <!-- FILA 3: serie | anocompra | valor_neto -->
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">serie</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->serie ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">anocompra</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->anocompra ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">valor_neto</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    <?php if($equipo->valor_neto): ?>
                                        $<?php echo e(number_format($equipo->valor_neto, 2, '.', ',')); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- FILA 4: remanente (ocupa 3 columnas) -->
                            <div class="p-3 bg-slate-50 col-span-3">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">remanente</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    <?php if($equipo->remanente): ?>
                                        $<?php echo e(number_format($equipo->remanente, 2, '.', ',')); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">serie</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->serie ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">anocompra</div>
                                <div class="text-sm text-slate-900 mt-1 break-words"><?php echo e($equipo->anocompra ?? '-'); ?></div>
                            </div>
                            <div class="p-3 bg-slate-50">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">valor_neto</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    <?php if($equipo->valor_neto): ?>
                                        $<?php echo e(number_format($equipo->valor_neto, 2, '.', ',')); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- FILA 4: remanente (ocupa 3 columnas) -->
                            <div class="p-3 bg-slate-50 col-span-3">
                                <div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">remanente</div>
                                <div class="text-sm text-slate-900 mt-1 break-words">
                                    <?php if($equipo->remanente): ?>
                                        $<?php echo e(number_format($equipo->remanente, 2, '.', ',')); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Vista desktop: Tabla con encabezado -->
                <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 mt-8 w-full">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Resultados de la búsqueda
                            </h3>
                            <span class="text-sm text-gray-600">
                                <?php echo e($results->count()); ?> <?php echo e($results->count() === 1 ? 'equipo encontrado' : 'equipos encontrados'); ?>

                            </span>
                        </div>
                        <?php if($currentBatch): ?>
                            <p class="text-sm text-gray-500 mt-1">
                                Lote: <span class="font-medium"><?php echo e($currentBatch->period); ?></span>
                                (<?php echo e($currentBatch->finished_at?->format('d/m/Y H:i')); ?>)
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PLACA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SERIE</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIVO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TIENDA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PLAZA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DESCRIPCIÓN</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CATEGORÍA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MARCA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MODELO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VALOR NETO</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900"><?php echo e($equipo->placa ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->serie ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->activo ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->cr ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->tienda ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->plaza ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->descripcion ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($equipo->categoria): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo e($equipo->categoria); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->marca ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo e($equipo->modelo ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">
                                            <?php if($equipo->valor_neto): ?>
                                                $<?php echo e(number_format($equipo->valor_neto, 2, '.', ',')); ?>

                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Animación de entrada suave
    // Mostrar botón flotante cuando hay resultados
    document.addEventListener('DOMContentLoaded', function() {
        // Si hay resultados, mostrar el botón
        <?php if(isset($results) && $results->isNotEmpty()): ?>
            if (typeof mostrarBotonVolverArriba === 'function') {
                mostrarBotonVolverArriba();
            }
        <?php endif; ?>
        
        // Mostrar botón cuando se envía el formulario de búsqueda
        const searchForm = document.querySelector('form[action="<?php echo e(route("dashboard.search")); ?>"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                setTimeout(() => {
                    if (typeof mostrarBotonVolverArriba === 'function') {
                        mostrarBotonVolverArriba();
                    }
                }, 500);
            });
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            form.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    });
</script>

<style>
    /* Efecto de enfoque suave en el input */
    input:focus {
        outline: none;
    }
    
    /* Animación sutil en el hover del formulario */
    form .border-gray-300:hover {
        border-color: #9CA3AF;
    }
</style>
<?php $__env->stopSection(); ?>
 MLA
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/dashboard/index.blade.php ENDPATH**/ ?>