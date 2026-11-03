<?php

namespace App\Reserva\Actions;

use App\Models\Cancha;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;
use App\Reserva\Queries\ExisteSolapamientoReserva;
use Illuminate\Support\Carbon;
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
    public function __invoke(array $data, EstadoReserva $estado = EstadoReserva::Confirmada): Reserva
    {
        return DB::transaction(function () use ($data, $estado) {
            $cancha = Cancha::query()
                ->lockForUpdate()
                ->findOrFail($data['cancha_id']);

            if (! $cancha->esta_disponible) {
                throw ValidationException::withMessages([
                    'cancha_id' => 'La cancha seleccionada no está disponible para reservas.',
                ]);
            }

            $fecha = Carbon::parse($data['fecha_turno']);

            if (! $cancha->atiendeDia($fecha->dayOfWeek)) {
                throw ValidationException::withMessages([
                    'fecha_turno' => 'La cancha seleccionada no atiende ese día.',
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
                'estado' => $estado,
            ]);
        });
    }
}
