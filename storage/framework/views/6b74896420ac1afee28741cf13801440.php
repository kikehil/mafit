

<?php $__env->startSection('title', 'Gestión de Usuarios'); ?>
<?php $__env->startSection('page-title', 'Gestión de Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Usuarios</h2>
            <a href="<?php echo e(route('admin.users.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Nuevo Usuario
            </a>
        </div>

        <!-- Búsqueda -->
        <div class="mb-6">
            <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    value="<?php echo e(request('q')); ?>" 
                    placeholder="Buscar por nombre, email o teléfono..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Buscar
                </button>
                <?php if(request('q')): ?>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if($users->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plaza</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($user->name); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($user->email); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($user->phone ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if($user->plazaRef): ?>
                                        <?php echo e($user->plaza); ?> - <?php echo e($user->plazaRef->plaza_nom); ?>

                                    <?php else: ?>
                                        <?php echo e($user->plaza ?? '-'); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($user->role === 'admin'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Admin
                                        </span>
                                    <?php elseif($user->role === 'supervisor'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Supervisor
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Técnico
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php if($user->isAdmin()): ?>
                                        <span class="text-xs text-purple-600 font-semibold">Todos los módulos</span>
                                    <?php else: ?>
                                        <?php
                                            $userModules = $user->modules;
                                        ?>
                                        <?php if($userModules->count() > 0): ?>
                                            <div class="flex flex-wrap gap-1">
                                                <?php $__currentLoopData = $userModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                                        <?php echo e($module->display_name); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-red-600">Sin módulos asignados</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="text-indigo-600 hover:text-indigo-900">
                                            Editar
                                        </a>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Eliminar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($users->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500">No se encontraron usuarios.</p>
                <?php if(request('q')): ?>
                    <p class="text-sm text-gray-400 mt-2">Intenta con otros términos de búsqueda.</p>
                <?php else: ?>
                    <p class="text-sm text-gray-400 mt-2">Usa el botón "Nuevo Usuario" en la parte superior para comenzar.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/admin/users/index.blade.php ENDPATH**/ ?>