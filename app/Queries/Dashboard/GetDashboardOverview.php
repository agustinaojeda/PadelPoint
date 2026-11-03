<?php

namespace App\Queries\Dashboard;

use App\Cancha\Models\Cancha;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetDashboardOverview
{
    /**
     * Obtiene todas las métricas que consume el panel de control.
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $metricasHoy = $this->metricasDelRango($hoy, $hoy);
        $metricasMes = $this->metricasDelRango($inicioMes, $finMes);

        return [
            'metrics' => [
                'ingresos_hoy' => (float) $metricasHoy->total_ingresos,
                'reservas_hoy' => (int) $metricasHoy->total_reservas,
                'ingresos_mes' => (float) $metricasMes->total_ingresos,
                'reservas_mes' => (int) $metricasMes->total_reservas,
                'total_canchas' => Cancha::query()->disponibles()->count(),
            ],
            'franjasHorarias' => $this->franjasHorarias($inicioMes, $finMes, (int) $metricasMes->total_reservas),
            'ocupacionDias' => $this->ocupacionPorDia($inicioMes, $finMes, (int) $metricasMes->total_reservas),
            'proximasReservas' => Reserva::query()
                ->with('cancha')
                ->delDia($hoy)
                ->activas()
                ->orderBy('hora_inicio')
                ->take(6)
                ->get(),
            'canchasResumen' => $this->canchasResumen($inicioMes, $finMes),
            'balanceAnual' => $this->balanceAnual(now()->year),
        ];
    }

    private function metricasDelRango(string $desde, string $hasta): object
    {
        return Reserva::query()
            ->confirmadasOCompletadas()
            ->enRangoFechas($desde, $hasta)
            ->selectRaw('COUNT(*) as total_reservas, COALESCE(SUM(monto_total), 0) as total_ingresos')
            ->first();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function franjasHorarias(string $inicioMes, string $finMes, int $totalReservasMes): array
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

        $calcularPorcentaje = fn (int $cantidad): int => $totalReservasMes > 0
            ? (int) round(($cantidad / $totalReservasMes) * 100)
            : 0;

        return [
            [
                'key' => 'manana',
                'label' => 'Mañana (08:00 - 14:00 hs)',
                'total' => (int) $conteo->manana,
                'porcentaje' => $calcularPorcentaje((int) $conteo->manana),
                'color' => '#f59e0b',
            ],
            [
                'key' => 'tarde',
                'label' => 'Tarde (14:00 - 18:00 hs)',
                'total' => (int) $conteo->tarde,
                'porcentaje' => $calcularPorcentaje((int) $conteo->tarde),
                'color' => '#3b82f6',
            ],
            [
                'key' => 'noche',
                'label' => 'Noche (18:00 - 00:00 hs)',
                'total' => (int) $conteo->noche,
                'porcentaje' => $calcularPorcentaje((int) $conteo->noche),
                'color' => '#10b981',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function ocupacionPorDia(string $inicioMes, string $finMes, int $totalReservasMes): array
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
            ['id' => $esSqlite ? '1' : 2, 'nombre' => 'Lunes'],
            ['id' => $esSqlite ? '2' : 3, 'nombre' => 'Martes'],
            ['id' => $esSqlite ? '3' : 4, 'nombre' => 'Miércoles'],
            ['id' => $esSqlite ? '4' : 5, 'nombre' => 'Jueves'],
            ['id' => $esSqlite ? '5' : 6, 'nombre' => 'Viernes'],
            ['id' => $esSqlite ? '6' : 7, 'nombre' => 'Sábado'],
            ['id' => $esSqlite ? '0' : 1, 'nombre' => 'Domingo'],
        ];

        $totalSegmentos = max($totalReservasMes, 1);

        return collect($dias)
            ->map(fn (array $dia): array => [
                'dia' => $dia['nombre'],
                'total' => (int) $conteoPorDia->get($dia['id'], 0),
                'porcentaje' => (int) round(((int) $conteoPorDia->get($dia['id'], 0) / $totalSegmentos) * 100),
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function canchasResumen(string $inicioMes, string $finMes): Collection
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

    /**
     * @return array<int, array{mes: string, num_mes: int, total: float}>
     */
    private function balanceAnual(int $anio): array
    {
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

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

        return collect($mesesNombres)
            ->map(fn (string $nombre, int $numMes): array => [
                'mes' => $nombre,
                'num_mes' => $numMes,
                'total' => (float) ($ingresosPorMes[$numMes] ?? 0),
            ])
            ->values()
            ->all();
    }
}
