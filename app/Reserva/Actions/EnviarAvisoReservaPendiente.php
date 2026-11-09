<?php

namespace App\Reserva\Actions;

use App\Models\Admin;
use App\Reserva\Mail\NuevaReservaPendiente;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Facades\Mail;

class EnviarAvisoReservaPendiente
{
    public function __invoke(Reserva $reserva): void
    {
        $destinatarios = Admin::query()
            ->pluck('correoAdmin')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($destinatarios === []) {
            return;
        }

        defer(fn () => Mail::to($destinatarios)->send(new NuevaReservaPendiente($reserva)));
    }
}
