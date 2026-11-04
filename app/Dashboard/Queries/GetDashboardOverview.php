<?php

namespace App\Dashboard\Queries;

use Carbon\CarbonInterface;

class GetDashboardOverview
{
    public function __construct(
        private GetDashboardMetrics $getDashboardMetrics,
        private GetFranjasHorarias $getFranjasHorarias,
        private GetOcupacionPorDia $getOcupacionPorDia,
        private GetProximasReservas $getProximasReservas,
        private GetCanchasResumen $getCanchasResumen,
        private GetBalanceAnual $getBalanceAnual,
        private GetReservasPendientesHoy $getReservasPendientesHoy,
    ) {}

    /**
     * Obtiene todas las métricas que consume el panel de control.
     *
     * @return array<string, mixed>
     */
    public function __invoke(?CarbonInterface $ahora = null): array
    {
        $ahora ??= now();

        $hoy = $ahora->toDateString();
        $inicioMes = $ahora->copy()->startOfMonth()->toDateString();
        $finMes = $ahora->copy()->endOfMonth()->toDateString();

        return [
            'metrics' => ($this->getDashboardMetrics)($hoy, $inicioMes, $finMes),
            'franjasHorarias' => ($this->getFranjasHorarias)($inicioMes, $finMes),
            'ocupacionDias' => ($this->getOcupacionPorDia)($inicioMes, $finMes),
            'proximasReservas' => ($this->getProximasReservas)($hoy),
            'canchasResumen' => ($this->getCanchasResumen)($inicioMes, $finMes),
            'balanceAnual' => ($this->getBalanceAnual)((int) $ahora->format('Y')),
            'reservasPendientesHoy' => ($this->getReservasPendientesHoy)($hoy),
        ];
    }
}
