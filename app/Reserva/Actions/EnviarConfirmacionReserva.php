<?php

namespace App\Reserva\Actions;

use App\Reserva\Mail\ReservaConfirmada;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Facades\Mail;

class EnviarConfirmacionReserva
{
    public function __invoke(Reserva $reserva): void
    {
        $destinatario = $reserva->email_cliente;

        if (blank($destinatario)) {
            return;
        }

        defer(fn () => Mail::to($destinatario)->send(new ReservaConfirmada($reserva)));
    }
}
