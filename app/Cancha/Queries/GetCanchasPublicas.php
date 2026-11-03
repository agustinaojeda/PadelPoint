<?php

namespace App\Cancha\Queries;

use App\Cancha\Models\Cancha;
use Illuminate\Database\Eloquent\Collection;

class GetCanchasPublicas
{
    /**
     * Retorna las canchas disponibles para la página principal.
     *
     * @return Collection<int, Cancha>
     */
    public function __invoke(): Collection
    {
        return Cancha::query()->disponibles()->orderBy('nombre')->get();
    }
}
