<?php

namespace App\Dashboard\Queries;

use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;

class GetReservasPendientesHoy
{
    /**
     * Cuenta las reservas que siguen pendientes de confirmación para la fecha indicada.
     */
    public function __invoke(string $fecha): int
    {
        return Reserva::query()
            ->delDia($fecha)
            ->where('estado', EstadoReserva::Pendiente->value)
            ->count();
    }
}
