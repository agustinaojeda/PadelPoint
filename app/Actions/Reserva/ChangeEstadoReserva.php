<?php

namespace App\Actions\Reserva;

use App\Enums\EstadoReserva;
use App\Models\Reserva;
use Illuminate\Validation\ValidationException;

class ChangeEstadoReserva
{
    /**
     * Cambia el estado de una reserva respetando las transiciones permitidas.
     *
     * @throws ValidationException
     */
    public function __invoke(Reserva $reserva, EstadoReserva $estado): Reserva
    {
        if ($reserva->estado === EstadoReserva::Completada) {
            throw ValidationException::withMessages([
                'estado' => 'No se puede modificar el estado de una reserva completada.',
            ]);
        }

        if ($reserva->estado === $estado) {
            return $reserva;
        }

        $reserva->update(['estado' => $estado]);

        return $reserva;
    }
}
