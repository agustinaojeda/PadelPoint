<?php

namespace App\Actions\Reserva;

use App\Enums\EstadoReserva;
use App\Models\Reserva;
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
