<?php

namespace App\Reserva\Actions;

use App\Cancha\Models\Cancha;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;
use App\Reserva\Queries\GetSlotsCancha;
use Illuminate\Validation\ValidationException;

class CreateReservaPublica
{
    public function __construct(
        private CreateReserva $createReserva,
        private GetSlotsCancha $getSlotsCancha,
    ) {}

    /**
     * Registra la reserva de un visitante anónimo en estado pendiente.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function __invoke(array $data): Reserva
    {
        $cancha = Cancha::query()->findOrFail($data['cancha_id']);

        if (! $cancha->esta_disponible) {
            throw ValidationException::withMessages([
                'cancha_id' => 'La cancha seleccionada no está disponible para reservas.',
            ]);
        }

        $reservas = Reserva::query()
            ->where('cancha_id', $cancha->id)
            ->delDia($data['fecha_turno'])
            ->activas()
            ->get(['id', 'cancha_id', 'hora_inicio', 'hora_fin']);

        $slot = collect(($this->getSlotsCancha)($cancha, $reservas, $data['fecha_turno']))
            ->firstWhere('hora_inicio', $data['hora_inicio']);

        if ($slot === null || ! $slot['disponible']) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El turno seleccionado ya no está disponible. Elegí otro horario.',
            ]);
        }

        return ($this->createReserva)([
            ...$data,
            'hora_fin' => $slot['hora_fin'],
            'monto_total' => $cancha->precio,
            'observaciones' => $data['observaciones'] ?? null,
        ], EstadoReserva::Pendiente);
    }
}
