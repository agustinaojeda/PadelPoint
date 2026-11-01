<?php

namespace App\Actions\Cancha;

use App\Models\Cancha;

class DeactivateCancha
{
    /**
     * Realiza la baja lógica de la cancha sin eliminar su historial.
     */
    public function __invoke(Cancha $cancha): void
    {
        $cancha->update([
            'esta_disponible' => false,
            'desactivada_en' => now(),
        ]);
    }
}
