<?php

use App\Enums\EstadoReserva;
use App\Models\Admin;
use App\Models\Cancha;
use App\Models\Reserva;

function reservaAdmin(): Admin
{
    return Admin::factory()->create();
}

/**
 * @return array<string, mixed>
 */
function reservaValida(Cancha $cancha, array $overrides = []): array
{
    return [
        'cancha_id' => $cancha->id,
        'fecha_turno' => now()->toDateString(),
        'hora_inicio' => '10:00',
        'hora_fin' => '11:30',
        'nombre_cliente' => 'Juan Pérez',
        'telefono_cliente' => '1122334455',
        'monto_total' => 12000,
        'observaciones' => null,
        ...$overrides,
    ];
}

test('guests cannot access reservas', function () {
    $this->get('/reservas')->assertRedirect('/login');
});

test('authenticated admin can create a reserva', function () {
    $cancha = Cancha::factory()->create();

    $this->actingAs(reservaAdmin())
        ->post('/reservas', reservaValida($cancha))
        ->assertRedirect();

    $this->assertDatabaseHas('reservas', [
        'cancha_id' => $cancha->id,
        'estado' => EstadoReserva::Confirmada->value,
    ]);
});

test('overlapping reservas are rejected', function () {
    $cancha = Cancha::factory()->create();
    $admin = reservaAdmin();

    $this->actingAs($admin)->post('/reservas', reservaValida($cancha))->assertRedirect();

    $this->actingAs($admin)
        ->post('/reservas', reservaValida($cancha, [
            'hora_inicio' => '11:00',
            'hora_fin' => '12:30',
        ]))
        ->assertSessionHasErrors('hora_inicio');

    expect(Reserva::query()->where('cancha_id', $cancha->id)->count())->toBe(1);
});

test('reservas on an unavailable cancha are rejected', function () {
    $cancha = Cancha::factory()->desactivada()->create();

    $this->actingAs(reservaAdmin())
        ->post('/reservas', reservaValida($cancha))
        ->assertSessionHasErrors('cancha_id');

    $this->assertDatabaseCount('reservas', 0);
});

test('admin can change the estado of a reserva', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Cancelada->value])
        ->assertRedirect();

    expect($reserva->fresh()->estado)->toBe(EstadoReserva::Cancelada);
});

test('an invalid estado is rejected', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/reservas/{$reserva->id}/estado", ['estado' => 'inventado'])
        ->assertSessionHasErrors('estado');
});

test('a completed reserva cannot change estado', function () {
    $reserva = Reserva::factory()->completada()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Cancelada->value])
        ->assertSessionHasErrors('estado');

    expect($reserva->fresh()->estado)->toBe(EstadoReserva::Completada);
});

test('admin can delete a non completed reserva', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->delete("/reservas/{$reserva->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('reservas', ['id' => $reserva->id]);
});

test('a completed reserva cannot be deleted', function () {
    $reserva = Reserva::factory()->completada()->create();

    $this->actingAs(reservaAdmin())
        ->delete("/reservas/{$reserva->id}")
        ->assertSessionHasErrors('reserva');

    $this->assertDatabaseHas('reservas', ['id' => $reserva->id]);
});
