<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Inventario</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #2563eb; margin-top: 0;">Notificación de Inventario</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <p style="font-size: 16px; margin-bottom: 15px;">
            Se le informa que se ha realizado el inventario de equipos para la tienda:
        </p>
        
        <div style="background-color: #eff6ff; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #2563eb;">
            <p style="margin: 0; font-size: 18px; font-weight: bold; color: #1e40af;">
                <?php echo e($tiendaNombre); ?>

            </p>
        </div>
        
        <p style="font-size: 16px; margin-bottom: 10px;">
            <strong>Realizado por:</strong> <?php echo e($usuarioRealizo); ?>

        </p>
        
        <p style="font-size: 16px; margin-bottom: 10px;">
            <strong>Fecha:</strong> <?php echo e(now()->format('d/m/Y H:i')); ?>

        </p>
        
        <?php if($notas): ?>
        <div style="background-color: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;">
            <p style="margin: 0 0 10px 0; font-weight: bold; color: #92400e;">Notas adicionales:</p>
            <p style="margin: 0; color: #78350f; white-space: pre-wrap;"><?php echo e($notas); ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 14px;">
        <p style="margin: 0;">Este es un correo automático del sistema MAFIT.</p>
        <p style="margin: 5px 0 0 0;">Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>

<?php /**PATH C:\WEB\MAFIT\resources\views/emails/inventario-notificacion.blade.php ENDPATH**/ ?>