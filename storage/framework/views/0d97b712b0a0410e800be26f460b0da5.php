<?php $__env->startSection('title', 'Detalle de Lote'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Resumen del Batch -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Lote: <?php echo e($batch->period); ?></h2>
                <div class="flex gap-2">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                    <form method="POST" action="<?php echo e(route('maf.batches.apply-categories', $batch)); ?>" class="inline" onsubmit="return confirm('¿Estás seguro de aplicar categorías a este lote? Esto actualizará la columna categoria en todos los registros MAF del lote.');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Aplicar Categorías
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('maf.batches.export', $batch)); ?>" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Exportar CSV
                    </a>
                </div>
            </div>

            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                        <?php if($batch->status === 'done'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completado
                            </span>
                        <?php elseif($batch->status === 'processing'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Procesando
                            </span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Fallido
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Archivo</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($batch->filename); ?></dd>
                </div>
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Filas Procesadas</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php echo e(number_format($batch->inserted_rows)); ?> / <?php echo e(number_format($batch->total_rows)); ?>

                    </dd>
                </div>
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($batch->uploadedBy->name ?? 'N/A'); ?></dd>
                </div>
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Inicio</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php echo e($batch->started_at ? $batch->started_at->format('d/m/Y H:i:s') : 'N/A'); ?>

                    </dd>
                </div>
                <div class="px-4 py-5 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Finalización</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php echo e($batch->finished_at ? $batch->finished_at->format('d/m/Y H:i:s') : 'N/A'); ?>

                    </dd>
                </div>
            </dl>

            <?php if($batch->notes): ?>
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800"><strong>Notas:</strong> <?php echo e($batch->notes); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($batch->status === 'done'): ?>
        <!-- Conflictos Graves -->
        <?php
            $totalConflicts = count($report['conflicts']['placa']) + count($report['conflicts']['activo']) + count($report['conflicts']['serie']);
        ?>

        <?php if($totalConflicts > 0): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-xl font-bold text-red-900 mb-4">⚠️ Conflictos Graves</h3>
                    <p class="text-sm text-red-800 mb-4">
                        Los siguientes identificadores aparecen en 2 o más tiendas distintas. Esto es un conflicto grave que debe corregirse en el MAF.
                    </p>

                    <?php $__currentLoopData = ['placa' => 'PLACA', 'activo' => 'ACTIVO', 'serie' => 'SERIE']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(count($report['conflicts'][$key]) > 0): ?>
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-red-900 mb-3"><?php echo e($label); ?> (<?php echo e(count($report['conflicts'][$key])); ?> conflictos)</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-red-200">
                                        <thead class="bg-red-100">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Valor</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Filas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Tiendas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Plazas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-red-200">
                                            <?php $__currentLoopData = $report['conflicts'][$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($item['value']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['rows_count']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['tiendas_distintas']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['plazas_distintas']); ?></td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <button 
                                                            onclick="toggleDetails('conflict-<?php echo e($key); ?>-<?php echo e($loop->index); ?>')"
                                                            class="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Ver Detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr id="conflict-<?php echo e($key); ?>-<?php echo e($loop->index); ?>" class="hidden">
                                                    <td colspan="5" class="px-4 py-3 bg-gray-50">
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-full text-sm">
                                                                <thead>
                                                                    <tr class="border-b">
                                                                        <th class="px-2 py-1 text-left">Fila</th>
                                                                        <th class="px-2 py-1 text-left">Plaza</th>
                                                                        <th class="px-2 py-1 text-left">CR</th>
                                                                        <th class="px-2 py-1 text-left">Tienda</th>
                                                                        <th class="px-2 py-1 text-left">Descripción</th>
                                                                        <th class="px-2 py-1 text-left">Marca</th>
                                                                        <th class="px-2 py-1 text-left">Modelo</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $item['occurrences']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $occ): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr class="border-b">
                                                                            <td class="px-2 py-1"><?php echo e($occ->row_num); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->plaza ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->cr ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->tienda ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->descripcion ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->marca ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->modelo ?? ''); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Duplicados Simples -->
        <?php
            $totalDuplicates = count($report['duplicates']['placa']) + count($report['duplicates']['activo']) + count($report['duplicates']['serie']);
        ?>

        <?php if($totalDuplicates > 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-xl font-bold text-yellow-900 mb-4">⚠️ Duplicados Simples</h3>
                    <p class="text-sm text-yellow-800 mb-4">
                        Los siguientes identificadores aparecen más de una vez en la misma tienda. Esto puede ser normal, pero debe verificarse.
                    </p>

                    <?php $__currentLoopData = ['placa' => 'PLACA', 'activo' => 'ACTIVO', 'serie' => 'SERIE']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(count($report['duplicates'][$key]) > 0): ?>
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-yellow-900 mb-3"><?php echo e($label); ?> (<?php echo e(count($report['duplicates'][$key])); ?> duplicados)</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-yellow-200">
                                        <thead class="bg-yellow-100">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase">Valor</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase">Filas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase">Tiendas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase">Plazas</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-yellow-800 uppercase">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-yellow-200">
                                            <?php $__currentLoopData = $report['duplicates'][$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($item['value']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['rows_count']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['tiendas_distintas']); ?></td>
                                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['plazas_distintas']); ?></td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <button 
                                                            onclick="toggleDetails('duplicate-<?php echo e($key); ?>-<?php echo e($loop->index); ?>')"
                                                            class="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Ver Detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr id="duplicate-<?php echo e($key); ?>-<?php echo e($loop->index); ?>" class="hidden">
                                                    <td colspan="5" class="px-4 py-3 bg-gray-50">
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-full text-sm">
                                                                <thead>
                                                                    <tr class="border-b">
                                                                        <th class="px-2 py-1 text-left">Fila</th>
                                                                        <th class="px-2 py-1 text-left">Plaza</th>
                                                                        <th class="px-2 py-1 text-left">CR</th>
                                                                        <th class="px-2 py-1 text-left">Tienda</th>
                                                                        <th class="px-2 py-1 text-left">Descripción</th>
                                                                        <th class="px-2 py-1 text-left">Marca</th>
                                                                        <th class="px-2 py-1 text-left">Modelo</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $item['occurrences']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $occ): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr class="border-b">
                                                                            <td class="px-2 py-1"><?php echo e($occ->row_num); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->plaza ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->cr ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->tienda ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->descripcion ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->marca ?? ''); ?></td>
                                                                            <td class="px-2 py-1"><?php echo e($occ->modelo ?? ''); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if($totalConflicts === 0 && $totalDuplicates === 0): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <p class="text-green-800 font-semibold">✓ No se encontraron conflictos ni duplicados en este lote.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function toggleDetails(id) {
    const element = document.getElementById(id);
    if (element) {
        element.classList.toggle('hidden');
    }
}
</script>
<?php $__env->stopSection(); ?>











<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/maf/batches/show.blade.php ENDPATH**/ ?>