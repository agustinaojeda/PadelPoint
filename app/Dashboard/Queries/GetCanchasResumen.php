<?php

namespace App\Dashboard\Queries;

use App\Cancha\Models\Cancha;
use Illuminate\Support\Collection;

class GetCanchasResumen
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function __invoke(string $inicioMes, string $finMes): Collection
    {
        return Cancha::query()
            ->disponibles()
            ->withCount(['reservas as reservas_mes' => fn ($query) => $query
                ->confirmadasOCompletadas()
                ->enRangoFechas($inicioMes, $finMes)])
            ->withSum(['reservas as recaudado_mes' => fn ($query) => $query
                ->confirmadasOCompletadas()
                ->enRangoFechas($inicioMes, $finMes)], 'monto_total')
            ->get()
            ->map(fn (Cancha $cancha): array => [
                'id' => $cancha->id,
                'nombre' => $cancha->nombre,
                'es_techada' => $cancha->es_techada,
                'reservas_mes' => $cancha->reservas_mes ?? 0,
                'recaudado_mes' => (float) ($cancha->recaudado_mes ?? 0),
            ]);
    }
}
