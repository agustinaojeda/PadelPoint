<?php

namespace Database\Factories;

use App\Cancha\Models\Cancha;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserva>
 */
class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $horaInicio = fake()->randomElement(['08:00', '09:30', '11:00', '14:00', '16:00', '18:00', '20:00']);

        return [
            'cancha_id' => Cancha::factory(),
            'fecha_turno' => now()->toDateString(),
            'hora_inicio' => $horaInicio,
            'hora_fin' => date('H:i', strtotime($horaInicio.' +90 minutes')),
            'nombre_cliente' => fake()->name(),
            'telefono_cliente' => fake()->numerify('##########'),
            'monto_total' => fake()->randomFloat(2, 5000, 20000),
            'estado' => EstadoReserva::Confirmada,
            'observaciones' => null,
        ];
    }

    public function completada(): static
    {
        return $this->state(fn (): array => [
            'estado' => EstadoReserva::Completada,
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (): array => [
            'estado' => EstadoReserva::Cancelada,
        ]);
    }
}
