<?php

namespace App\Cancha\Queries;

use App\Cancha\Models\Cancha;
use Illuminate\Database\Eloquent\Collection;

class GetCanchasParaPurgar
{
    public const DIAS_RETENCION = 30;

    /**
     * Retorna las canchas desactivadas hace más de 30 días y sin reservas.
     *
     * @return Collection<int, Cancha>
     */
    public function __invoke(): Collection
    {
        return Cancha::query()
            ->where('esta_disponible', false)
            ->whereNotNull('desactivada_en')
            ->where('desactivada_en', '<=', now()->subDays(self::DIAS_RETENCION))
            ->doesntHave('reservas')
            ->get();
    }
}
