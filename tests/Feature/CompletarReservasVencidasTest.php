<?php

use App\Enums\EstadoReserva;
use App\Models\Reserva;

test('the command completes expired confirmed reservas', function () {
    $vencida = Reserva::factory()->create([
        'fecha_turno' => now()->subDay()->toDateString(),
        'estado' => EstadoReserva::Confirmada,
    ]);

    $this->artisan('app:completar-reservas-vencidas')->assertSuccessful();

    expect($vencida->fresh()->estado)->toBe(EstadoReserva::Completada);
});

test('the command keeps future reservas confirmed', function () {
    $futura = Reserva::factory()->create([
        'fecha_turno' => now()->addDay()->toDateString(),
        'estado' => EstadoReserva::Confirmada,
    ]);

    $this->artisan('app:completar-reservas-vencidas')->assertSuccessful();

    expect($futura->fresh()->estado)->toBe(EstadoReserva::Confirmada);
});
