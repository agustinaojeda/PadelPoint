<?php

use App\Cancha\Models\Cancha;
use App\Models\Admin;
use App\Reserva\Enums\EstadoReserva;
use App\Reserva\Mail\NuevaReservaPendiente;
use App\Reserva\Mail\ReservaConfirmada;
use App\Reserva\Models\Reserva;
use Illuminate\Support\Facades\Mail;

function reservaAdmin(): Admin
{
    return Admin::factory()->create();
}

/**
 * @return array<string, mixed>
 */
function reservaValida(Cancha $cancha, array $overrides = []): array
{
    $fecha = now();

    while (! $cancha->atiendeDia($fecha->dayOfWeek)) {
        $fecha->addDay();
    }

    return [
        'cancha_id' => $cancha->id,
        'fecha_turno' => $fecha->toDateString(),
        'hora_inicio' => '10:00',
        'hora_fin' => '11:30',
        'nombre_cliente' => 'Juan Pérez',
        'telefono_cliente' => '1122334455',
        'monto_total' => 12000,
        'observaciones' => null,
        ...$overrides,
    ];
}

function canchaPublica(array $overrides = []): Cancha
{
    return Cancha::factory()->create([
        'dias_disponibles' => [0, 1, 2, 3, 4, 5, 6],
        'hora_apertura' => '08:00',
        'hora_cierre' => '23:00',
        'duracion_turno' => 60,
        ...$overrides,
    ]);
}

/**
 * @return array<string, mixed>
 */
function reservaPublicaValida(Cancha $cancha, array $overrides = []): array
{
    return [
        'cancha_id' => $cancha->id,
        'fecha_turno' => now()->addDay()->toDateString(),
        'hora_inicio' => '10:00',
        'nombre_cliente' => 'Ana Gómez',
        'email_cliente' => 'cliente@example.com',
        'telefono_cliente' => '1122334455',
        'observaciones' => null,
        ...$overrides,
    ];
}

test('guests cannot access the admin reservas panel', function () {
    $this->get('/admin/reservas')->assertRedirect('/login');
});

test('guests can access the public reservations page', function () {
    $cancha = canchaPublica();

    $this->get('/reservas')
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('reservas/index')
                ->has('canchas')
                ->has('fechaSeleccionada')
                ->where('canchas.0.id', $cancha->id)
                ->has('canchas.0.slots'),
        );
});

test('public reservations default to today when the date is invalid', function () {
    $this->get('/reservas?fecha=inventada')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('fechaSeleccionada', now()->toDateString()));
});

test('guests can request a reserva with pending state', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reservas', [
        'cancha_id' => $cancha->id,
        'hora_inicio' => '10:00',
        'hora_fin' => '11:00',
        'estado' => EstadoReserva::Pendiente->value,
        'monto_total' => $cancha->precio,
    ]);
});

test('public reservations derive the amount from the cancha price', function () {
    $cancha = canchaPublica(['precio' => 15000]);

    $this->post('/reservas', reservaPublicaValida($cancha, ['monto_total' => 1]))->assertRedirect();

    $this->assertDatabaseHas('reservas', [
        'cancha_id' => $cancha->id,
        'monto_total' => 15000,
    ]);
});

test('overlapping public reservations are rejected', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha))->assertRedirect();

    $this->post('/reservas', reservaPublicaValida($cancha, ['hora_inicio' => '10:00']))
        ->assertSessionHasErrors('hora_inicio');

    expect(Reserva::query()->where('cancha_id', $cancha->id)->count())->toBe(1);
});

test('public reservations on a closed day are rejected', function () {
    $cancha = canchaPublica(['dias_disponibles' => [1, 2, 3, 4, 5]]);

    $this->post('/reservas', reservaPublicaValida($cancha, ['fecha_turno' => now()->next('Sunday')->toDateString()]))
        ->assertSessionHasErrors('fecha_turno');

    $this->assertDatabaseCount('reservas', 0);
});

test('public reservations for past dates are rejected', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha, ['fecha_turno' => now()->subDay()->toDateString()]))
        ->assertSessionHasErrors('fecha_turno');

    $this->assertDatabaseCount('reservas', 0);
});

test('public reservations with an off grid hour are rejected', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha, ['hora_inicio' => '10:30']))
        ->assertSessionHasErrors('hora_inicio');

    $this->assertDatabaseCount('reservas', 0);
});

test('public reservations on an unavailable cancha are rejected', function () {
    $cancha = canchaPublica(['esta_disponible' => false, 'desactivada_en' => now()]);

    $this->post('/reservas', reservaPublicaValida($cancha))
        ->assertSessionHasErrors('cancha_id');

    $this->assertDatabaseCount('reservas', 0);
});

test('public reservations require visitor contact data', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha, ['nombre_cliente' => '', 'email_cliente' => null]))
        ->assertSessionHasErrors(['nombre_cliente', 'email_cliente']);

    $this->assertDatabaseCount('reservas', 0);
});

test('admin can see the reservas management panel', function () {
    $this->actingAs(reservaAdmin())
        ->get('/admin/reservas')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/reservas/index'));
});

test('authenticated admin can create a reserva', function () {
    $cancha = Cancha::factory()->create();

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha))
        ->assertRedirect();

    $this->assertDatabaseHas('reservas', [
        'cancha_id' => $cancha->id,
        'estado' => EstadoReserva::Confirmada->value,
    ]);
});

test('overlapping reservas are rejected', function () {
    $cancha = Cancha::factory()->create();
    $admin = reservaAdmin();

    $this->actingAs($admin)->post('/admin/reservas', reservaValida($cancha))->assertRedirect();

    $this->actingAs($admin)
        ->post('/admin/reservas', reservaValida($cancha, [
            'hora_inicio' => '11:00',
            'hora_fin' => '12:30',
        ]))
        ->assertSessionHasErrors('hora_inicio');

    expect(Reserva::query()->where('cancha_id', $cancha->id)->count())->toBe(1);
});

test('reservas on an unavailable cancha are rejected', function () {
    $cancha = Cancha::factory()->desactivada()->create();

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha))
        ->assertSessionHasErrors('cancha_id');

    $this->assertDatabaseCount('reservas', 0);
});

test('admin can change the estado of a reserva', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/admin/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Cancelada->value])
        ->assertRedirect();

    expect($reserva->fresh()->estado)->toBe(EstadoReserva::Cancelada);
});

test('an invalid estado is rejected', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/admin/reservas/{$reserva->id}/estado", ['estado' => 'inventado'])
        ->assertSessionHasErrors('estado');
});

test('a completed reserva cannot change estado', function () {
    $reserva = Reserva::factory()->completada()->create();

    $this->actingAs(reservaAdmin())
        ->patch("/admin/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Cancelada->value])
        ->assertSessionHasErrors('estado');

    expect($reserva->fresh()->estado)->toBe(EstadoReserva::Completada);
});

test('admin can delete a non completed reserva', function () {
    $reserva = Reserva::factory()->create();

    $this->actingAs(reservaAdmin())
        ->delete("/admin/reservas/{$reserva->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('reservas', ['id' => $reserva->id]);
});

test('a completed reserva cannot be deleted', function () {
    $reserva = Reserva::factory()->completada()->create();

    $this->actingAs(reservaAdmin())
        ->delete("/admin/reservas/{$reserva->id}")
        ->assertSessionHasErrors('reserva');

    $this->assertDatabaseHas('reservas', ['id' => $reserva->id]);
});

test('admin panel loads reservas with their cancha', function () {
    $cancha = Cancha::factory()->create(['nombre' => 'Cancha Central']);
    $reserva = Reserva::factory()->create([
        'cancha_id' => $cancha->id,
        'fecha_turno' => now()->toDateString(),
    ]);

    $this->actingAs(reservaAdmin())
        ->get('/admin/reservas')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/reservas/index')
            ->where('reservas.0.id', $reserva->id)
            ->where('reservas.0.cancha.nombre', 'Cancha Central'));
});

test('admin reservas panel defaults to today when the date is invalid', function () {
    $this->actingAs(reservaAdmin())
        ->get('/admin/reservas?fecha=inventada')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('fechaSeleccionada', now()->toDateString()));
});

test('admin cannot create a reserva on a day the cancha does not attend', function () {
    $cancha = Cancha::factory()->create(['dias_disponibles' => [1]]);
    $fecha = now()->next('Sunday')->toDateString(); // domingo = 0, la cancha solo atiende lunes

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha, ['fecha_turno' => $fecha]))
        ->assertSessionHasErrors('fecha_turno');

    $this->assertDatabaseCount('reservas', 0);
});

test('reserva estado enum values are exposed', function () {
    expect(EstadoReserva::values())->toBe(['pendiente', 'confirmada', 'cancelada', 'completada']);
});

test('la reserva pública exige email', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha, ['email_cliente' => null]))
        ->assertSessionHasErrors('email_cliente');

    $this->assertDatabaseCount('reservas', 0);
});

test('la reserva pública acepta teléfono opcional', function () {
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha, ['telefono_cliente' => null]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $reserva = Reserva::query()->firstOrFail();

    expect($reserva->email_cliente)->toBe('cliente@example.com')
        ->and($reserva->telefono_cliente)->toBeNull();
});

test('la reserva de admin no exige email', function () {
    Mail::fake();

    $cancha = Cancha::factory()->create();

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha))
        ->assertRedirect();

    $this->assertDatabaseHas('reservas', [
        'cancha_id' => $cancha->id,
        'email_cliente' => null,
    ]);
    Mail::assertNotSent(ReservaConfirmada::class);
});

test('confirmar una reserva envía el mail', function () {
    Mail::fake();

    $reserva = Reserva::factory()->cancelada()->create(['email_cliente' => 'cliente@example.com']);

    $this->actingAs(reservaAdmin())
        ->patch("/admin/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Confirmada->value])
        ->assertRedirect();

    Mail::assertSent(ReservaConfirmada::class, fn ($mail) => $mail->hasTo('cliente@example.com'));
});

test('cancelar una reserva no envía mail', function () {
    Mail::fake();

    $reserva = Reserva::factory()->create(['email_cliente' => 'cliente@example.com']);

    $this->actingAs(reservaAdmin())
        ->patch("/admin/reservas/{$reserva->id}/estado", ['estado' => EstadoReserva::Cancelada->value])
        ->assertRedirect();

    Mail::assertNotSent(ReservaConfirmada::class);
});

test('el alta de admin con email envía el mail', function () {
    Mail::fake();

    $cancha = Cancha::factory()->create();

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha, ['email_cliente' => 'cliente@example.com']))
        ->assertRedirect();

    Mail::assertSent(ReservaConfirmada::class, fn ($mail) => $mail->hasTo('cliente@example.com'));
});

test('la reserva pública envía el aviso de reserva pendiente al admin', function () {
    Mail::fake();

    $admin = reservaAdmin();
    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha))
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(NuevaReservaPendiente::class, function (NuevaReservaPendiente $mail) use ($admin) {
        return $mail->hasTo($admin->correoAdmin)
            && $mail->hasReplyTo('cliente@example.com');
    });
});

test('el alta de admin no envía el aviso de reserva pendiente', function () {
    Mail::fake();

    $cancha = Cancha::factory()->create();

    $this->actingAs(reservaAdmin())
        ->post('/admin/reservas', reservaValida($cancha, ['email_cliente' => 'cliente@example.com']))
        ->assertRedirect();

    Mail::assertNotSent(NuevaReservaPendiente::class);
});

test('la reserva pública se crea aunque no haya admins para avisar', function () {
    Mail::fake();

    $cancha = canchaPublica();

    $this->post('/reservas', reservaPublicaValida($cancha))
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertNotSent(NuevaReservaPendiente::class);
});

test('el aviso de reserva pendiente oculta teléfono y observaciones vacíos', function () {
    $reserva = Reserva::factory()->create([
        'telefono_cliente' => null,
        'observaciones' => null,
        'estado' => EstadoReserva::Pendiente,
    ]);

    $html = (new NuevaReservaPendiente($reserva))->render();

    expect($html)->not->toContain('Teléfono')
        ->and($html)->not->toContain('Observaciones');
});

test('el aviso de reserva pendiente muestra teléfono y observaciones cuando existen', function () {
    $reserva = Reserva::factory()->create([
        'telefono_cliente' => '1122334455',
        'observaciones' => 'Llamar antes de venir.',
        'estado' => EstadoReserva::Pendiente,
    ]);

    $html = (new NuevaReservaPendiente($reserva))->render();

    expect($html)->toContain('Teléfono')
        ->and($html)->toContain('1122334455')
        ->and($html)->toContain('Observaciones')
        ->and($html)->toContain('Llamar antes de venir.');
});
