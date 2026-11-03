<?php

namespace App\Reserva\Queries;

use App\Models\Cancha;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetSlotsCancha
{
    /**
     * Genera la grilla de turnos de una cancha para una fecha determinada.
     *
     * @param  Collection<int, Reserva>  $reservas
     * @return array<int, array{hora_inicio: string, hora_fin: string, disponible: bool}>
     */
    public function __invoke(Cancha $cancha, Collection $reservas, string $fecha): array
    {
        $aperturaMinutos = $this->horaAMinutos($cancha->hora_apertura);
        $cierreMinutos = $this->horaAMinutos($cancha->hora_cierre);
        $duracion = $cancha->duracion_turno;

        if ($duracion <= 0 || $aperturaMinutos >= $cierreMinutos) {
            return [];
        }

        $ocupados = $reservas
            ->where('cancha_id', $cancha->id)
            ->map(fn (Reserva $reserva): array => [
                'inicio' => $this->horaAMinutos($reserva->hora_inicio),
                'fin' => $this->horaAMinutos($reserva->hora_fin),
            ]);

        $ahora = Carbon::now();
        $esHoy = $fecha === $ahora->toDateString();
        $ahoraMinutos = ($ahora->hour * 60) + $ahora->minute;

        $slots = [];
        $inicioMinutos = $aperturaMinutos;

        while ($inicioMinutos + $duracion <= $cierreMinutos) {
            $finMinutos = $inicioMinutos + $duracion;
            $horaInicio = $this->minutosAHora($inicioMinutos);
            $horaFin = $this->minutosAHora($finMinutos);

            $ocupado = $ocupados->contains(
                fn (array $rango): bool => $inicioMinutos < $rango['fin'] && $finMinutos > $rango['inicio'],
            );

            $enPasado = $esHoy && $inicioMinutos <= $ahoraMinutos;

            $slots[] = [
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'disponible' => ! $ocupado && ! $enPasado,
            ];

            $inicioMinutos = $finMinutos;
        }

        return $slots;
    }

    private function horaAMinutos(string $hora): int
    {
        [$horas, $minutos] = array_map('intval', explode(':', $hora));

        return ($horas * 60) + $minutos;
    }

    private function minutosAHora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }
}
