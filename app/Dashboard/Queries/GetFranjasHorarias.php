<?php

namespace App\Dashboard\Queries;

use App\Reserva\Models\Reserva;

class GetFranjasHorarias
{
    /**
     * @return array<int, array{key: string, total: int, porcentaje: int}>
     */
    public function __invoke(string $inicioMes, string $finMes): array
    {
        $conteo = Reserva::query()
            ->confirmadasOCompletadas()
            ->enRangoFechas($inicioMes, $finMes)
            ->selectRaw("
                SUM(CASE WHEN hora_inicio < '14:00:00' THEN 1 ELSE 0 END) as manana,
                SUM(CASE WHEN hora_inicio >= '14:00:00' AND hora_inicio < '18:00:00' THEN 1 ELSE 0 END) as tarde,
                SUM(CASE WHEN hora_inicio >= '18:00:00' THEN 1 ELSE 0 END) as noche
            ")
            ->first();

        $franjas = [
            'manana' => (int) $conteo->manana,
            'tarde' => (int) $conteo->tarde,
            'noche' => (int) $conteo->noche,
        ];

        $total = array_sum($franjas);

        return collect($franjas)
            ->map(fn (int $cantidad, string $key): array => [
                'key' => $key,
                'total' => $cantidad,
                'porcentaje' => $total > 0 ? (int) round(($cantidad / $total) * 100) : 0,
            ])
            ->values()
            ->all();
    }
}
