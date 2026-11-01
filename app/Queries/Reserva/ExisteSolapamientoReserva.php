<?php

namespace App\Queries\Reserva;

use App\Enums\EstadoReserva;
use App\Models\Reserva;

class ExisteSolapamientoReserva
{
    /**
     * Determina si un rango horario se solapa con otra reserva activa de la misma cancha.
     */
    public function __invoke(
        int $canchaId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $ignorarReservaId = null,
    ): bool {
        return Reserva::query()
            ->where('cancha_id', $canchaId)
            ->where('fecha_turno', $fecha)
            ->where('estado', '!=', EstadoReserva::Cancelada->value)
            ->when($ignorarReservaId, fn ($query) => $query->whereKeyNot($ignorarReservaId))
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }
}
