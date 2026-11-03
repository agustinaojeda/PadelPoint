<?php

use App\Models\Admin;
use App\Models\Cancha;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;

function adminAutenticado(): Admin
{
    return Admin::factory()->create();
}

test('guests cannot access canchas', function () {
    $this->get('/admin/canchas')->assertRedirect('/login');
});

test('authenticated admin can list canchas', function () {
    Cancha::factory()->count(2)->create();

    $this->actingAs(adminAutenticado())
        ->get('/admin/canchas')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/canchas/index'));
});

test('authenticated admin can create a cancha', function () {
    $this->actingAs(adminAutenticado())
        ->post('/admin/canchas', [
            'nombre' => 'Cancha Central',
            'superficie' => 'Césped sintético',
            'es_techada' => true,
            'precio' => 12000,
            'duracion_turno' => 90,
            'cantidad_jugadores' => 4,
            'hora_apertura' => '08:00',
            'hora_cierre' => '23:00',
            'dias_disponibles' => [1, 2, 3],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('canchas', [
        'nombre' => 'Cancha Central',
        'esta_disponible' => true,
        'imagen_url' => 'images/cancha-default.webp',
    ]);
});

test('authenticated admin can update a cancha', function () {
    $cancha = Cancha::factory()->create();

    $this->actingAs(adminAutenticado())
        ->put("/admin/canchas/{$cancha->id}", [
            'nombre' => 'Cancha Renombrada',
            'superficie' => 'Cemento',
            'es_techada' => false,
            'precio' => 9000,
            'duracion_turno' => 60,
            'cantidad_jugadores' => 4,
            'hora_apertura' => '09:00',
            'hora_cierre' => '22:00',
            'dias_disponibles' => [1, 2],
        ])
        ->assertRedirect();

    expect($cancha->fresh()->nombre)->toBe('Cancha Renombrada');
});

test('admin can deactivate and reactivate a cancha', function () {
    $cancha = Cancha::factory()->create();
    $admin = adminAutenticado();

    $this->actingAs($admin)->delete("/admin/canchas/{$cancha->id}")->assertRedirect();

    expect($cancha->fresh())
        ->esta_disponible->toBeFalse()
        ->desactivada_en->not->toBeNull();

    $this->actingAs($admin)->patch("/admin/canchas/{$cancha->id}/activar")->assertRedirect();

    expect($cancha->fresh())
        ->esta_disponible->toBeTrue()
        ->desactivada_en->toBeNull();
});

test('a cancha with reservas cannot be permanently deleted', function () {
    $cancha = Cancha::factory()->create();
    Reserva::factory()->create(['cancha_id' => $cancha->id]);

    $this->actingAs(adminAutenticado())
        ->delete("/admin/canchas/{$cancha->id}/eliminar")
        ->assertSessionHasErrors('cancha');

    $this->assertDatabaseHas('canchas', ['id' => $cancha->id]);
});

test('a cancha without reservas can be permanently deleted', function () {
    $cancha = Cancha::factory()->create();

    $this->actingAs(adminAutenticado())
        ->delete("/admin/canchas/{$cancha->id}/eliminar")
        ->assertRedirect();

    $this->assertDatabaseMissing('canchas', ['id' => $cancha->id]);
});

test('cancha estado enum values are exposed', function () {
    expect(EstadoReserva::values())->toBe(['pendiente', 'confirmada', 'cancelada', 'completada']);
});
