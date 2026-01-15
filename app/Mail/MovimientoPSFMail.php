<?php

namespace App\Mail;

use App\Models\InventarioPSF;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MovimientoPSFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inventario;
    public $tipoMovimiento;
    public $datosAdicionales;
    public $supervisor;

    public function __construct(InventarioPSF $inventario, string $tipoMovimiento, array $datosAdicionales = [], User $supervisor)
    {
        $this->inventario = $inventario;
        $this->tipoMovimiento = $tipoMovimiento;
        $this->datosAdicionales = $datosAdicionales;
        $this->supervisor = $supervisor;
    }

    public function build()
    {
        $titulo = match($this->tipoMovimiento) {
            'cambio_ubicacion' => 'Cambio de Ubicación - Inventario PSF',
            'eliminacion' => 'Eliminación de Equipo - Inventario PSF',
            'nota' => 'Actualización de Notas - Inventario PSF',
            default => 'Movimiento en Inventario PSF',
        };

        return $this->subject($titulo)
            ->view('emails.movimiento-psf');
    }
}
