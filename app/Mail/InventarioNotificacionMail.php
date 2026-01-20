<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InventarioNotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tiendaNombre;
    public $usuarioRealizo;
    public $notas;

    /**
     * Create a new message instance.
     */
    public function __construct($tiendaNombre, $usuarioRealizo, $notas = null)
    {
        $this->tiendaNombre = $tiendaNombre;
        $this->usuarioRealizo = $usuarioRealizo;
        $this->notas = $notas;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación de Inventario - ' . $this->tiendaNombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inventario-notificacion',
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
