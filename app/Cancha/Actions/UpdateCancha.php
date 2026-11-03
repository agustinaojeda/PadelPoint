<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;
use App\Cancha\Support\CanchaImagen;
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
        $rutaAnterior = $cancha->getRawOriginal('imagen_url');

        if ($imagen) {
            $data['imagen_url'] = CanchaImagen::store($imagen);
        }

        $cancha->update($data);

        if ($imagen) {
            CanchaImagen::deleteIfCustom($rutaAnterior);
        }
    }
}
