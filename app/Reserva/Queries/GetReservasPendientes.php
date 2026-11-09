<?php

namespace App\Reserva\Queries;

use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;

class GetReservasPendientes
{
    /**
     * Cuenta las reservas pendientes de confirmación desde la fecha indicada.
     */
    public function __invoke(?string $desde = null): int
    {
        $desde ??= now()->toDateString();

        return Reserva::query()
            ->where('estado', EstadoReserva::Pendiente->value)
            ->where('fecha_turno', '>=', $desde)
            ->count();
    }
}
