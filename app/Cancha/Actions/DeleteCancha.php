<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;
use App\Cancha\Support\CanchaImagen;
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

        $rutaImagen = $cancha->getRawOriginal('imagen_url');

        $cancha->delete();

        CanchaImagen::deleteIfCustom($rutaImagen);
    }
}
