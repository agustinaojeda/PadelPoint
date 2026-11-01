<?php

namespace App\Actions\Cancha;

use App\Models\Cancha;
use App\Support\CanchaImagen;
use Illuminate\Http\UploadedFile;

class UpdateCancha
{
    /**
     * Actualiza los datos de una cancha y reemplaza su imagen si se subió una nueva.
     *
     * @param  array<string, mixed>  $data
     */
    public function __invoke(Cancha $cancha, array $data, ?UploadedFile $imagen = null): void
    {
        unset($data['imagen']);

        if ($imagen) {
            $cancha->eliminarImagenFisica();
            $data['imagen_url'] = CanchaImagen::store($imagen);
        }

        $cancha->update($data);
    }
}
