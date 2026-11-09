<?php

namespace App\Reserva\Mail;

use App\Reserva\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaReservaPendiente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva)
    {
        $this->reserva->loadMissing('cancha');
    }

    public function envelope(): Envelope
    {
        $fecha = $this->reserva->fecha_turno->format('d/m');
        $hora = substr($this->reserva->hora_inicio, 0, 5);

        return new Envelope(
            subject: "Nueva reserva pendiente · {$this->reserva->cancha->nombre} · {$fecha} {$hora}",
            replyTo: filled($this->reserva->email_cliente)
                ? [new Address($this->reserva->email_cliente, $this->reserva->nombre_cliente)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservas.pendiente-admin',
            with: [
                'reserva' => $this->reserva,
                'url' => route('admin.reservas.index', [
                    'fecha' => $this->reserva->fecha_turno->toDateString(),
                ]),
            ],
        );
    }
}
