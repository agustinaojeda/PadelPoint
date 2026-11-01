<?php

namespace Database\Factories;

use App\Models\Cancha;
use App\Support\CanchaImagen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cancha>
 */
class CanchaFactory extends Factory
{
    protected $model = Cancha::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Cancha '.fake()->unique()->bothify('##-??'),
            'superficie' => fake()->randomElement(['Césped sintético', 'Cemento', 'Parquet']),
            'es_techada' => fake()->boolean(),
            'esta_disponible' => true,
            'precio' => fake()->randomFloat(2, 5000, 20000),
            'imagen_url' => CanchaImagen::DEFAULT_PATH,
            'descripcion' => fake()->sentence(),
            'duracion_turno' => 90,
            'cantidad_jugadores' => 4,
            'hora_apertura' => '08:00',
            'hora_cierre' => '23:00',
            'dias_disponibles' => [1, 2, 3, 4, 5],
            'desactivada_en' => null,
        ];
    }

    public function desactivada(): static
    {
        return $this->state(fn (): array => [
            'esta_disponible' => false,
            'desactivada_en' => now(),
        ]);
    }
}
