<?php

namespace App\Reserva\Queries;

use App\Models\Cancha;
use App\Reserva\Models\Reserva;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GetGrillaReservas
{
    /**
     * Retorna las canchas y reservas de la fecha para el panel administrativo.
     *
     * @return array{canchas: Collection<int, Cancha>, reservas: Collection<int, Reserva>, fechaSeleccionada: string}
     */
    public function __invoke(?string $fecha): array
    {
        $fechaSeleccionada = $this->resolverFecha($fecha);

        return [
            'canchas' => Cancha::query()->disponibles()->orderBy('nombre')->get(),
            'reservas' => Reserva::query()
                ->with('cancha')
                ->delDia($fechaSeleccionada)
                ->get(),
            'fechaSeleccionada' => $fechaSeleccionada,
        ];
    }

    /**
     * Normaliza y valida la fecha recibida por query string.
     */
    private function resolverFecha(?string $fecha): string
    {
        return is_string($fecha) && Carbon::hasFormat($fecha, 'Y-m-d')
            ? $fecha
            : now()->toDateString();
    }
}
