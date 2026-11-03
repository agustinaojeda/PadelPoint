<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;
use App\Cancha\Support\CanchaImagen;
use Illuminate\Http\UploadedFile;

class CreateCancha
{
    /**
     * Crea una cancha nueva dejándola disponible para reservas.
     *
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data, ?UploadedFile $imagen = null): Cancha
    {
        $data['esta_disponible'] = true;
        $data['imagen_url'] = $imagen
            ? CanchaImagen::store($imagen)
            : CanchaImagen::DEFAULT_PATH;

        return Cancha::create($data);
    }
}
