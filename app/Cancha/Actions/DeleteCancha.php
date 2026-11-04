<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;
use App\Cancha\Support\CanchaImagen;

class DeleteCancha
{
    /**
     * Elimina definitivamente una cancha sin reservas. Si tiene reservas,
     * la oculta del panel conservando el historial y borra su imagen.
     *
     * @return bool Indica si la cancha fue ocultada en lugar de eliminada.
     */
    public function __invoke(Cancha $cancha): bool
    {
        $rutaImagen = $cancha->getRawOriginal('imagen_url');

        if ($cancha->reservas()->exists()) {
            $cancha->update([
                'esta_disponible' => false,
                'desactivada_en' => $cancha->desactivada_en ?? now(),
                'oculta_en' => now(),
                'imagen_url' => null,
            ]);

            CanchaImagen::deleteIfCustom($rutaImagen);

            return true;
        }

        $cancha->delete();

        CanchaImagen::deleteIfCustom($rutaImagen);

        return false;
    }
}
