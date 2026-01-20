<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha</label>
            <p class="text-sm text-gray-900"><?php echo e($movimiento->created_at->format('d/m/Y H:i')); ?></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de Movimiento</label>
            <p class="text-sm text-gray-900"><?php echo e($tipos[$movimiento->tipo_movimiento] ?? $movimiento->tipo_movimiento); ?></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Usuario</label>
            <p class="text-sm text-gray-900"><?php echo e($movimiento->user->name ?? 'N/A'); ?></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Realizó Inventario</label>
            <p class="text-sm text-gray-900"><?php echo e($movimiento->realizo_inventario ? 'Sí' : 'No'); ?></p>
        </div>
    </div>

    <?php if(in_array($movimiento->tipo_movimiento, ['retiro', 'remplazo_dano', 'remplazo_renovacion', 'reingreso_garantia'])): ?>
        <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-900 mb-3">Datos de la Tienda</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">CR</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->cr ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tienda</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->tienda ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-900 mb-3">Equipo Retirado</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Placa</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_placa ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_serie ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_descripcion ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_marca ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Modelo</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_modelo ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_retirado_activo ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                    <p class="text-sm text-gray-900">$<?php echo e(number_format($movimiento->equipo_retirado_remanente ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>

        <?php if(in_array($movimiento->tipo_movimiento, ['remplazo_dano', 'remplazo_renovacion'])): ?>
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-900 mb-3">Equipo de Reemplazo</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Placa</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_placa ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_serie ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_descripcion ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_marca ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Modelo</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_modelo ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                        <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_remplazo_activo ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                        <p class="text-sm text-gray-900">$<?php echo e(number_format($movimiento->equipo_remplazo_remanente ?? 0, 2)); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($movimiento->motivo): ?>
            <div class="border-t pt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Motivo del Retiro</label>
                <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($movimiento->motivo); ?></p>
            </div>
        <?php endif; ?>

        <?php if($movimiento->seguimiento): ?>
            <div class="border-t pt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Seguimiento</label>
                <p class="text-sm text-gray-900"><?php echo e(strtoupper($movimiento->seguimiento)); ?></p>
            </div>
        <?php endif; ?>

        <?php if($movimiento->comentarios): ?>
            <div class="border-t pt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Comentarios</label>
                <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($movimiento->comentarios); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($movimiento->tipo_movimiento === 'agregar'): ?>
        <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-900 mb-3">Datos de la Tienda</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">CR</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->cr ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tienda</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->tienda ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Plaza</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->plaza ?? 'N/A'); ?></p>
                </div>
                <?php if($movimiento->nombre_plaza): ?>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nombre Plaza</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->nombre_plaza); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-900 mb-3">Equipo Agregado</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Placa</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_placa ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_serie ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_descripcion ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_marca ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Modelo</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_modelo ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Activo</label>
                    <p class="text-sm text-gray-900"><?php echo e($movimiento->equipo_agregado_activo ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remanente</label>
                    <p class="text-sm text-gray-900">$<?php echo e(number_format($movimiento->equipo_agregado_remanente ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php /**PATH C:\WEB\MAFIT\resources\views/movimientos/detalle.blade.php ENDPATH**/ ?>