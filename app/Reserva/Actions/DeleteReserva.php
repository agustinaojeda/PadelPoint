<?php

namespace App\Reserva\Actions;

use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;
use Illuminate\Validation\ValidationException;

class DeleteReserva
{
    /**
     * Elimina una reserva siempre que no haya sido completada.
     *
     * @throws ValidationException
     */
    public function __invoke(Reserva $reserva): void
    {
        if ($reserva->estado === EstadoReserva::Completada) {
            throw ValidationException::withMessages([
                'reserva' => 'No se puede eliminar una reserva que ya fue completada.',
            ]);
        }

        $reserva->delete();
    }
}
