<?php

namespace App\Reserva\Mail;

use App\Reserva\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu turno en PadelPoint está confirmado',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservas.confirmada',
            with: ['reserva' => $this->reserva->loadMissing('cancha')],
        );
    }
}
