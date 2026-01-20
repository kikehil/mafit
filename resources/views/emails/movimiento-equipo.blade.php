<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Movimiento de Equipo</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        @php
            $tipos = [
                'retiro' => 'Retiro de Equipo',
                'remplazo_dano' => 'Remplazo de Equipo por Daño',
                'remplazo_renovacion' => 'Remplazo de Equipo por Renovación',
                'agregar' => 'Agregar Equipo',
                'reingreso_garantia' => 'Reingreso por Garantía',
            ];
            $tipoNombre = $tipos[$movimiento->tipo_movimiento] ?? 'Movimiento de Equipo';
        @endphp
        <h1 style="color: #2563eb; margin-top: 0;">Notificación de {{ $tipoNombre }}</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <p style="font-size: 16px; margin-bottom: 15px;">
            <strong>Realizado por:</strong> {{ $movimiento->user->name }}
        </p>
        
        <p style="font-size: 16px; margin-bottom: 10px;">
            <strong>Fecha:</strong> {{ $movimiento->created_at->format('d/m/Y H:i') }}
        </p>

        @if(in_array($movimiento->tipo_movimiento, ['retiro', 'remplazo_dano', 'remplazo_renovacion', 'reingreso_garantia']))
            <div style="background-color: #fef2f2; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ef4444;">
                <h2 style="margin-top: 0; color: #991b1b; font-size: 18px;">Datos de la Tienda</h2>
                <p style="margin: 5px 0;"><strong>CR:</strong> {{ $movimiento->cr ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Tienda:</strong> {{ $movimiento->tienda ?? 'N/A' }}</p>
            </div>

            <div style="background-color: #fff7ed; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f97316;">
                <h2 style="margin-top: 0; color: #9a3412; font-size: 18px;">Equipo Retirado</h2>
                <p style="margin: 5px 0;"><strong>Placa:</strong> {{ $movimiento->equipo_retirado_placa ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Serie:</strong> {{ $movimiento->equipo_retirado_serie ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Descripción:</strong> {{ $movimiento->equipo_retirado_descripcion ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Marca:</strong> {{ $movimiento->equipo_retirado_marca ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Modelo:</strong> {{ $movimiento->equipo_retirado_modelo ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Activo:</strong> {{ $movimiento->equipo_retirado_activo ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Remanente:</strong> ${{ number_format($movimiento->equipo_retirado_remanente ?? 0, 2) }}</p>
            </div>

            @if($movimiento->tipo_movimiento === 'retiro')
                <div style="background-color: #fee2e2; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #dc2626;">
                    <p style="margin: 0; font-weight: bold; color: #991b1b;">
                        ⚠️ IMPORTANTE: No se dejó equipo de reemplazo
                    </p>
                </div>
            @endif

            @if(in_array($movimiento->tipo_movimiento, ['remplazo_dano', 'remplazo_renovacion']))
                <div style="background-color: #ecfdf5; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #10b981;">
                    <h2 style="margin-top: 0; color: #065f46; font-size: 18px;">Equipo de Reemplazo</h2>
                    <p style="margin: 5px 0;"><strong>Placa:</strong> {{ $movimiento->equipo_remplazo_placa ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Serie:</strong> {{ $movimiento->equipo_remplazo_serie ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Descripción:</strong> {{ $movimiento->equipo_remplazo_descripcion ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Marca:</strong> {{ $movimiento->equipo_remplazo_marca ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Modelo:</strong> {{ $movimiento->equipo_remplazo_modelo ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Activo:</strong> {{ $movimiento->equipo_remplazo_activo ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Remanente:</strong> ${{ number_format($movimiento->equipo_remplazo_remanente ?? 0, 2) }}</p>
                </div>
                <div style="background-color: #dbeafe; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6;">
                    <p style="margin: 0; font-weight: bold; color: #1e40af;">
                        Tipo de movimiento: {{ $tipoNombre }}
                    </p>
                </div>
            @endif

            @if($movimiento->motivo)
                <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p style="margin: 0 0 10px 0; font-weight: bold; color: #374151;">Motivo del retiro:</p>
                    <p style="margin: 0; color: #1f2937; white-space: pre-wrap;">{{ $movimiento->motivo }}</p>
                </div>
            @endif

            @if($movimiento->seguimiento)
                <div style="background-color: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;">
                    <p style="margin: 0; font-weight: bold; color: #92400e;">
                        Seguimiento: {{ strtoupper($movimiento->seguimiento) }}
                    </p>
                </div>
            @endif
        @endif

        @if($movimiento->tipo_movimiento === 'agregar')
            <div style="background-color: #fef2f2; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ef4444;">
                <h2 style="margin-top: 0; color: #991b1b; font-size: 18px;">Datos de la Tienda</h2>
                <p style="margin: 5px 0;"><strong>CR:</strong> {{ $movimiento->cr ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Tienda:</strong> {{ $movimiento->tienda ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Plaza:</strong> {{ $movimiento->plaza ?? 'N/A' }}</p>
                @if($movimiento->nombre_plaza)
                    <p style="margin: 5px 0;"><strong>Nombre Plaza:</strong> {{ $movimiento->nombre_plaza }}</p>
                @endif
            </div>

            <div style="background-color: #ecfdf5; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #10b981;">
                <h2 style="margin-top: 0; color: #065f46; font-size: 18px;">Equipo Agregado</h2>
                <p style="margin: 5px 0;"><strong>Placa:</strong> {{ $movimiento->equipo_agregado_placa ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Serie:</strong> {{ $movimiento->equipo_agregado_serie ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Descripción:</strong> {{ $movimiento->equipo_agregado_descripcion ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Marca:</strong> {{ $movimiento->equipo_agregado_marca ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Modelo:</strong> {{ $movimiento->equipo_agregado_modelo ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Activo:</strong> {{ $movimiento->equipo_agregado_activo ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Remanente:</strong> ${{ number_format($movimiento->equipo_agregado_remanente ?? 0, 2) }}</p>
            </div>

            <div style="background-color: #dbeafe; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6;">
                <p style="margin: 0; font-weight: bold; color: #1e40af;">
                    Tipo de movimiento: {{ $tipoNombre }}
                </p>
            </div>
        @endif

        @if($movimiento->tipo_movimiento === 'reingreso_garantia' && $movimiento->comentarios)
            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #374151;">Comentarios:</p>
                <p style="margin: 0; color: #1f2937; white-space: pre-wrap;">{{ $movimiento->comentarios }}</p>
            </div>
        @endif

        <div style="background-color: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #0ea5e9;">
            <p style="margin: 0; font-weight: bold; color: #0c4a6e;">
                @if($movimiento->realizo_inventario)
                    ✓ El usuario realizó inventario de equipo
                @else
                    ✗ El usuario decidió no realizar inventario de equipo
                @endif
            </p>
        </div>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 14px;">
        <p style="margin: 0;">Este es un correo automático del sistema MAFIT.</p>
        <p style="margin: 5px 0 0 0;">Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>







