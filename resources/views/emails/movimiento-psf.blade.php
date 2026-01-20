<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimiento en Inventario PSF</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #2563eb; margin-top: 0;">
            @if($tipoMovimiento === 'cambio_ubicacion')
                Cambio de Ubicación - Inventario PSF
            @elseif($tipoMovimiento === 'eliminacion')
                Eliminación de Equipo - Inventario PSF
            @else
                Actualización - Inventario PSF
            @endif
        </h2>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
        <h3 style="color: #1f2937; margin-top: 0;">Detalles del Equipo</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>Placa:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $inventario->placa ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>Marca:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $inventario->marca ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>Modelo:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $inventario->modelo ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>Serie:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $inventario->serie ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>Ubicación Actual:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $inventario->ubicacion ?? '-' }}</td>
            </tr>
        </table>

        @if($tipoMovimiento === 'cambio_ubicacion')
        <div style="margin-top: 20px; padding: 15px; background-color: #dbeafe; border-left: 4px solid #2563eb; border-radius: 4px;">
            <p style="margin: 0;"><strong>Cambio de Ubicación:</strong></p>
            <p style="margin: 5px 0 0 0;">De: <strong>{{ $datosAdicionales['ubicacion_anterior'] ?? '-' }}</strong></p>
            <p style="margin: 5px 0 0 0;">A: <strong>{{ $datosAdicionales['ubicacion_nueva'] ?? '-' }}</strong></p>
        </div>
        @elseif($tipoMovimiento === 'eliminacion')
        <div style="margin-top: 20px; padding: 15px; background-color: #fee2e2; border-left: 4px solid #dc2626; border-radius: 4px;">
            <p style="margin: 0;"><strong>Equipo Eliminado del Inventario</strong></p>
            <p style="margin: 5px 0 0 0;"><strong>Motivo:</strong> {{ $datosAdicionales['notas'] ?? '-' }}</p>
        </div>
        @endif

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
            <p style="margin: 0;">Plaza: <strong>{{ $inventario->plaza_usuario ?? '-' }}</strong></p>
            <p style="margin: 5px 0 0 0;">Fecha: <strong>{{ $inventario->updated_at->format('d/m/Y H:i') }}</strong></p>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 12px;">
        <p>Este es un mensaje automático del sistema MAFIT.</p>
    </div>
</body>
</html>

