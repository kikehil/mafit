

<?php $__env->startSection('title', 'Nueva Importación MAF'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cargar Archivo Maestro (MAF)</h2>

        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Nota importante:</strong> El sistema realizará una limpieza automática de caracteres invisibles y normalización de datos antes de procesar el archivo.
            </p>
        </div>

        <form action="<?php echo e(route('maf.import.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                    Período (YYYY-MM)
                </label>
                <input 
                    type="text" 
                    id="period" 
                    name="period" 
                    value="<?php echo e(old('period', date('Y-m'))); ?>"
                    pattern="\d{4}-\d{2}"
                    placeholder="2024-01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">Formato: YYYY-MM (ejemplo: 2024-01)</p>
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    Archivo Excel (.xlsx)
                </label>
                <input 
                    type="file" 
                    id="file" 
                    name="file" 
                    accept=".xlsx"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">Tamaño máximo: 50MB</p>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="<?php echo e(route('maf.batches.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Procesar
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>










<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WEB\MAFIT\resources\views/maf/import.blade.php ENDPATH**/ ?>