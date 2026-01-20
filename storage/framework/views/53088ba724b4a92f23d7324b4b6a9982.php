<?php $__env->startSection('title', 'Consulta de Equipos'); ?>
<?php $__env->startSection('page-title', 'Consulta de Equipos'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Formulario de Búsqueda -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buscar Equipo</h3>
        <form action="<?php echo e(route('maf.search')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="query" class="block text-sm font-medium text-gray-700 mb-2">
                        PLACA o SERIE
                    </label>
                    <input 
                        type="text" 
                        id="query" 
                        name="query" 
                        value="<?php echo e(old('query', $query ?? '')); ?>"
                        placeholder="Ingrese PLACA o SERIE del equipo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                        autofocus
                    >
                </div>
                <div>
                    <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Lote
                    </label>
                    <select 
                        id="batch_id" 
                        name="batch_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Último lote (<?php echo e($lastBatch?->period ?? 'N/A'); ?>)</option>
                        <?php $__currentLoopData = $batches ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($batch->id); ?>" <?php echo e((isset($currentBatch) && $currentBatch->id === $batch->id) ? 'selected' : ''); ?>>
                                <?php echo e($batch->period); ?> - <?php echo e($batch->finished_at?->format('d/m/Y H:i')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div>
                <button 
                    type="submit" 
                    class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
                >
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Resultados -->
    <?php if(isset($results)): ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
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

            <?php if($results->isEmpty()): ?>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">No se encontraron equipos con "<?php echo e($query); ?>"</p>
                    <p class="text-gray-400 text-sm mt-2">Intente con otra PLACA o SERIE</p>
                </div>
            <?php else: ?>
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
    <?php else: ?>
        <!-- Estado inicial -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg">Ingrese una PLACA o SERIE para buscar equipos</p>
            <p class="text-gray-400 text-sm mt-2">La búsqueda se realizará en el último lote procesado</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/maf/search/index.blade.php ENDPATH**/ ?>