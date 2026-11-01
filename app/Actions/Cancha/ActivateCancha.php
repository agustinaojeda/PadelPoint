<?php

namespace App\Actions\Cancha;

use App\Models\Cancha;

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
