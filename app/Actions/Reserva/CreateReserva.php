<?php

namespace App\Actions\Reserva;

use App\Enums\EstadoReserva;
use App\Models\Cancha;
use App\Models\Reserva;
use App\Queries\Reserva\ExisteSolapamientoReserva;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateReserva
{
    public function __construct(private ExisteSolapamientoReserva $existeSolapamiento) {}

    /**
     * Registra una reserva evitando solapamientos incluso bajo concurrencia.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function __invoke(array $data): Reserva
    {
        return DB::transaction(function () use ($data) {
            $cancha = Cancha::query()
                ->lockForUpdate()
                ->findOrFail($data['cancha_id']);

            if (! $cancha->esta_disponible) {
                throw ValidationException::withMessages([
                    'cancha_id' => 'La cancha seleccionada no está disponible para reservas.',
                ]);
            }

            if (($this->existeSolapamiento)(
                $cancha->id,
                $data['fecha_turno'],
                $data['hora_inicio'],
                $data['hora_fin'],
            )) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'El rango horario seleccionado se solapa con una reserva existente.',
                ]);
            }

            return $cancha->reservas()->create([
                ...$data,
                'estado' => EstadoReserva::Confirmada,
            ]);
        });
    }
}
