<?php

namespace App\Actions\Cancha;

use App\Models\Cancha;
use Illuminate\Validation\ValidationException;

class DeleteCancha
{
    /**
     * Elimina definitivamente una cancha solo si no tiene reservas asociadas.
     *
     * @throws ValidationException
     */
    public function __invoke(Cancha $cancha): void
    {
        if ($cancha->reservas()->exists()) {
            throw ValidationException::withMessages([
                'cancha' => 'No se puede eliminar una cancha que tiene reservas registradas.',
            ]);
        }

        $cancha->eliminarImagenFisica();
        $cancha->delete();
    }
}
