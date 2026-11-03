<?php

namespace App\Reserva\Queries;

use App\Reserva\Models\Reserva;

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
            ->delDia($fecha)
            ->activas()
            ->when($ignorarReservaId, fn ($query) => $query->whereKeyNot($ignorarReservaId))
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }
}
