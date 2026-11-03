<?php

namespace App\Dashboard\Queries;

use App\Reserva\Models\Reserva;
use Illuminate\Support\Facades\DB;

class GetOcupacionPorDia
{
    /**
     * @return array<int, array{dia_semana: int, total: int, porcentaje: int}>
     */
    public function __invoke(string $inicioMes, string $finMes): array
    {
        $esSqlite = DB::connection()->getDriverName() === 'sqlite';

        $diaSql = $esSqlite ? "strftime('%w', fecha_turno)" : 'DAYOFWEEK(fecha_turno)';

        $conteoPorDia = Reserva::query()
            ->confirmadasOCompletadas()
            ->enRangoFechas($inicioMes, $finMes)
            ->selectRaw("{$diaSql} as dia_sql, COUNT(*) as total")
            ->groupBy('dia_sql')
            ->pluck('total', 'dia_sql');

        $dias = [
            ['id' => $esSqlite ? '1' : 2, 'dia_semana' => 1],
            ['id' => $esSqlite ? '2' : 3, 'dia_semana' => 2],
            ['id' => $esSqlite ? '3' : 4, 'dia_semana' => 3],
            ['id' => $esSqlite ? '4' : 5, 'dia_semana' => 4],
            ['id' => $esSqlite ? '5' : 6, 'dia_semana' => 5],
            ['id' => $esSqlite ? '6' : 7, 'dia_semana' => 6],
            ['id' => $esSqlite ? '0' : 1, 'dia_semana' => 7],
        ];

        $total = (int) $conteoPorDia->sum();

        return collect($dias)
            ->map(fn (array $dia): array => [
                'dia_semana' => $dia['dia_semana'],
                'total' => (int) $conteoPorDia->get($dia['id'], 0),
                'porcentaje' => $total > 0
                    ? (int) round(((int) $conteoPorDia->get($dia['id'], 0) / $total) * 100)
                    : 0,
            ])
            ->values()
            ->all();
    }
}
