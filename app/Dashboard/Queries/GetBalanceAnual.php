<?php

namespace App\Dashboard\Queries;

use App\Reserva\Models\Reserva;
use Illuminate\Support\Facades\DB;

class GetBalanceAnual
{
    /**
     * @return array<int, array{mes: int, total: float}>
     */
    public function __invoke(int $anio): array
    {
        $expresionMes = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', fecha_turno) AS INTEGER)"
            : 'MONTH(fecha_turno)';

        $ingresosPorMes = Reserva::query()
            ->selectRaw("{$expresionMes} as mes, SUM(monto_total) as total")
            ->whereYear('fecha_turno', $anio)
            ->confirmadasOCompletadas()
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->mapWithKeys(fn (mixed $total, mixed $mes): array => [(int) $mes => (float) $total]);

        return collect(range(1, 12))
            ->map(fn (int $mes): array => [
                'mes' => $mes,
                'total' => $ingresosPorMes[$mes] ?? 0.0,
            ])
            ->values()
            ->all();
    }
}
