

<?php $__env->startSection('title', 'Asignar Tiendas'); ?>
<?php $__env->startSection('page-title', 'Asignar Tiendas'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Asignar Tiendas</h2>
            <p class="mt-2 text-sm text-gray-600">
                Usuario: <span class="font-medium"><?php echo e($user->name); ?></span> | 
                Plaza: <span class="font-medium"><?php echo e($user->plazaRef ? $user->plazaRef->plaza_nom : $user->plaza); ?></span>
            </p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.tienda-assignment.update', $user)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php if($tiendas->count() > 0): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Selecciona las tiendas de las que <?php echo e($user->name); ?> es responsable:
                    </label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        <?php $__currentLoopData = $tiendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tienda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="tiendas[]" 
                                    value="<?php echo e($tienda->id); ?>"
                                    <?php echo e(in_array($tienda->id, $tiendasAsignadas) ? 'checked' : ''); ?>

                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <span class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium"><?php echo e($tienda->cr); ?></span>
                                    <?php if($tienda->tienda): ?>
                                        <span class="text-gray-500"> - <?php echo e($tienda->tienda); ?></span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <?php echo e($tiendas->count()); ?> tienda(s) disponible(s) en esta plaza
                    </p>
                </div>
            <?php else: ?>
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        No hay tiendas registradas para la plaza <strong><?php echo e($user->plaza); ?></strong>.
                        Las tiendas se crean automáticamente al importar archivos MAF.
                    </p>
                </div>
            <?php endif; ?>

            <div class="mt-6 flex justify-end gap-3">
                <a href="<?php echo e(route('admin.tienda-assignment.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Guardar Asignación
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/admin/tienda-assignment/edit.blade.php ENDPATH**/ ?>