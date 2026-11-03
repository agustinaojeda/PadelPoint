<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;

class ActivateCancha
{
    /**
     * Reactiva una cancha previamente deshabilitada.
     */
    public function __invoke(Cancha $cancha): void
    {
        $cancha->update([
            'esta_disponible' => true,
            'desactivada_en' => null,
        ]);
    }
}
