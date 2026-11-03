<?php

namespace App\Reserva\Actions;

use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;

class CompleteExpiredReservas
{
    /**
     * Marca como completadas las reservas confirmadas cuyo horario ya terminó.
     */
    public function __invoke(): int
    {
        $ahoraFecha = now()->toDateString();
        $ahoraHora = now()->toTimeString();

        return Reserva::query()
            ->where('estado', EstadoReserva::Confirmada)
            ->where(function ($query) use ($ahoraFecha, $ahoraHora) {
                $query->where('fecha_turno', '<', $ahoraFecha)
                    ->orWhere(function ($query) use ($ahoraFecha, $ahoraHora) {
                        $query->where('fecha_turno', $ahoraFecha)
                            ->where('hora_fin', '<=', $ahoraHora);
                    });
            })
            ->update(['estado' => EstadoReserva::Completada]);
    }
}
