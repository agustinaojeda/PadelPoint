<?php

namespace App\Cancha\Queries;

use App\Cancha\Models\Cancha;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCanchasAdmin
{
    /**
     * Retorna el listado paginado de canchas para el panel administrativo.
     */
    public function __invoke(): LengthAwarePaginator
    {
        return Cancha::query()
            ->orderBy('esta_disponible', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);
    }
}
