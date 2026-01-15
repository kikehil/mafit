<?php

namespace App\Mail;

use App\Models\Movimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MovimientoEquipoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $movimiento;

    /**
     * Create a new message instance.
     */
    public function __construct(Movimiento $movimiento)
    {
        $this->movimiento = $movimiento;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tipos = [
            'retiro' => 'Retiro de Equipo',
            'remplazo_dano' => 'Remplazo de Equipo por Daño',
            'remplazo_renovacion' => 'Remplazo de Equipo por Renovación',
            'agregar' => 'Agregar Equipo',
            'reingreso_garantia' => 'Reingreso por Garantía',
        ];

        $tipoNombre = $tipos[$this->movimiento->tipo_movimiento] ?? 'Movimiento de Equipo';
        
        return new Envelope(
            subject: "Notificación de {$tipoNombre}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.movimiento-equipo',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
