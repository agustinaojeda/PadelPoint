<?php

namespace App\Dashboard\Queries;

use App\Reserva\Models\Reserva;
use Illuminate\Database\Eloquent\Collection;

class GetProximasReservas
{
    public const LIMITE = 6;

    /**
     * @return Collection<int, Reserva>
     */
    public function __invoke(string $fecha): Collection
    {
        return Reserva::query()
            ->with('cancha')
            ->delDia($fecha)
            ->activas()
            ->orderBy('hora_inicio')
            ->take(self::LIMITE)
            ->get();
    }
}
