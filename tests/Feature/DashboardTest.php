<?php

use App\Cancha\Models\Cancha;
use App\Models\Admin;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Models\Reserva;

test('guests are redirected to the login page', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = Admin::factory()->create();

    $response = $this->actingAs($user)->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('the dashboard exposes every panel prop', function () {
    Cancha::factory()->count(2)->create();

    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/dashboard/index')
            ->hasAll(['metrics', 'franjasHorarias', 'ocupacionDias', 'proximasReservas', 'canchasResumen', 'balanceAnual', 'reservasPendientesHoy'])
            ->where('metrics.total_canchas', 2)
            ->has('franjasHorarias', 3)
            ->has('ocupacionDias', 7)
            ->has('balanceAnual', 12)
            ->where('balanceAnual.0.mes', 1)
            ->where('balanceAnual.11.mes', 12)
            ->where('ocupacionDias.0.dia_semana', 1)
            ->where('ocupacionDias.6.dia_semana', 7));
});

test('franjas horarias no longer carry presentation data', function () {
    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertInertia(fn ($page) => $page
            ->missing('franjasHorarias.0.label')
            ->missing('franjasHorarias.0.color')
            ->where('franjasHorarias.0.key', 'manana'));
});

test('dashboard metrics reflect confirmed reservas of the day', function () {
    Reserva::factory()->create([
        'fecha_turno' => now()->toDateString(),
        'monto_total' => 1000,
    ]);

    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertInertia(fn ($page) => $page
            ->where('metrics.reservas_hoy', 1)
            ->where('metrics.reservas_mes', 1)
            ->where('metrics.ingresos_hoy', fn ($valor) => (float) $valor === 1000.0));
});

test('dashboard limits proximas reservas to six', function () {
    Reserva::factory()->count(7)->create(['fecha_turno' => now()->toDateString()]);

    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertInertia(fn ($page) => $page->has('proximasReservas', 6));
});

test('dashboard counts only pending reservas for today', function () {
    Reserva::factory()->create([
        'fecha_turno' => now()->toDateString(),
        'estado' => EstadoReserva::Pendiente,
    ]);
    Reserva::factory()->create(['fecha_turno' => now()->toDateString()]);
    Reserva::factory()->create([
        'fecha_turno' => now()->addDay()->toDateString(),
        'estado' => EstadoReserva::Pendiente,
    ]);

    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertInertia(fn ($page) => $page->where('reservasPendientesHoy', 1));
});

test('dashboard percentages are zero without reservas', function () {
    $this->actingAs(Admin::factory()->create())
        ->get('/admin/dashboard')
        ->assertInertia(fn ($page) => $page
            ->where('reservasPendientesHoy', 0)
            ->where('franjasHorarias.0.porcentaje', 0)
            ->where('ocupacionDias.0.porcentaje', 0)
            ->where('balanceAnual.0.total', fn ($valor) => (float) $valor === 0.0));
});
