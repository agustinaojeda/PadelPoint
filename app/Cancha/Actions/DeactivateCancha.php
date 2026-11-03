<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;

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
