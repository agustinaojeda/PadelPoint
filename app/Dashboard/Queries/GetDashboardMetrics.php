<?php

namespace App\Dashboard\Queries;

use App\Cancha\Models\Cancha;
use App\Reserva\Models\Reserva;

class GetDashboardMetrics
{
    /**
     * @return array{ingresos_hoy: float, reservas_hoy: int, ingresos_mes: float, reservas_mes: int, total_canchas: int}
     */
    public function __invoke(string $hoy, string $inicioMes, string $finMes): array
    {
        $metricasHoy = $this->metricasDelRango($hoy, $hoy);
        $metricasMes = $this->metricasDelRango($inicioMes, $finMes);

        return [
            'ingresos_hoy' => $metricasHoy['total_ingresos'],
            'reservas_hoy' => $metricasHoy['total_reservas'],
            'ingresos_mes' => $metricasMes['total_ingresos'],
            'reservas_mes' => $metricasMes['total_reservas'],
            'total_canchas' => Cancha::query()->disponibles()->count(),
        ];
    }

    /**
     * @return array{total_reservas: int, total_ingresos: float}
     */
    private function metricasDelRango(string $desde, string $hasta): array
    {
        $metricas = Reserva::query()
            ->confirmadasOCompletadas()
            ->enRangoFechas($desde, $hasta)
            ->selectRaw('COUNT(*) as total_reservas, COALESCE(SUM(monto_total), 0) as total_ingresos')
            ->first();

        return [
            'total_reservas' => (int) $metricas->total_reservas,
            'total_ingresos' => (float) $metricas->total_ingresos,
        ];
    }
}
