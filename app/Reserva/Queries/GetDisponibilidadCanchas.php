<?php

namespace App\Reserva\Queries;

use App\Models\Cancha;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Carbon;

class GetDisponibilidadCanchas
{
    public function __construct(private GetSlotsCancha $getSlotsCancha) {}

    /**
     * Retorna las canchas operativas para la fecha junto con sus turnos disponibles.
     *
     * @return array<int, array<string, mixed>>
     */
    public function __invoke(string $fecha): array
    {
        $diaSemana = Carbon::parse($fecha)->dayOfWeek;

        $reservas = Reserva::query()
            ->delDia($fecha)
            ->activas()
            ->get(['id', 'cancha_id', 'hora_inicio', 'hora_fin', 'estado']);

        return Cancha::query()
            ->disponibles()
            ->orderBy('nombre')
            ->get()
            ->filter(fn (Cancha $cancha): bool => $cancha->atiendeDia($diaSemana))
            ->map(fn (Cancha $cancha): array => [
                ...$cancha->only([
                    'id',
                    'nombre',
                    'superficie',
                    'es_techada',
                    'esta_disponible',
                    'precio',
                    'imagen_url',
                    'descripcion',
                    'duracion_turno',
                    'cantidad_jugadores',
                    'hora_apertura',
                    'hora_cierre',
                    'dias_disponibles',
                ]),
                'slots' => ($this->getSlotsCancha)($cancha, $reservas, $fecha),
            ])
            ->values()
            ->all();
    }
}
