<?php

use App\Cancha\Models\Cancha;
use App\Models\Admin;
use App\Reserva\Models\Reserva;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function adminAutenticado(): Admin
{
    return Admin::factory()->create();
}

function imagenDePrueba(string $nombre = 'cancha.png'): UploadedFile
{
    $contenido = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
    );

    $ruta = tempnam(sys_get_temp_dir(), 'cancha');

    file_put_contents($ruta, $contenido);

    return new UploadedFile($ruta, $nombre, 'image/png', null, true);
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

test('duracion_turno must fit within the cancha schedule', function () {
    $this->actingAs(adminAutenticado())
        ->post('/admin/canchas', [
            'nombre' => 'Cancha Corta',
            'precio' => 10000,
            'duracion_turno' => 90,
            'cantidad_jugadores' => 4,
            'hora_apertura' => '08:00',
            'hora_cierre' => '09:00',
            'dias_disponibles' => [1, 2],
        ])
        ->assertSessionHasErrors('duracion_turno');

    $this->assertDatabaseCount('canchas', 0);
});

test('dias_disponibles cannot contain duplicates', function () {
    $this->actingAs(adminAutenticado())
        ->post('/admin/canchas', [
            'nombre' => 'Cancha Duplicada',
            'precio' => 10000,
            'duracion_turno' => 60,
            'cantidad_jugadores' => 4,
            'hora_apertura' => '08:00',
            'hora_cierre' => '23:00',
            'dias_disponibles' => [1, 1, 2],
        ])
        ->assertSessionHasErrors('dias_disponibles.1');

    $this->assertDatabaseCount('canchas', 0);
});

test('updating a cancha replaces its image and deletes the old file', function () {
    Storage::fake('public');

    $admin = adminAutenticado();

    $this->actingAs($admin)->post('/admin/canchas', [
        'nombre' => 'Cancha con Imagen',
        'precio' => 10000,
        'duracion_turno' => 60,
        'cantidad_jugadores' => 4,
        'hora_apertura' => '08:00',
        'hora_cierre' => '23:00',
        'dias_disponibles' => [1, 2],
        'imagen' => imagenDePrueba('vieja.png'),
    ])->assertRedirect();

    $cancha = Cancha::query()->firstOrFail();
    $rutaVieja = $cancha->getRawOriginal('imagen_url');

    Storage::disk('public')->assertExists(str_replace('storage/', '', $rutaVieja));

    $this->actingAs($admin)->put("/admin/canchas/{$cancha->id}", [
        'nombre' => 'Cancha con Imagen',
        'precio' => 10000,
        'duracion_turno' => 60,
        'cantidad_jugadores' => 4,
        'hora_apertura' => '08:00',
        'hora_cierre' => '23:00',
        'dias_disponibles' => [1, 2],
        'imagen' => imagenDePrueba('nueva.png'),
    ])->assertRedirect();

    $rutaNueva = $cancha->fresh()->getRawOriginal('imagen_url');

    expect($rutaNueva)->not->toBe($rutaVieja);
    Storage::disk('public')->assertMissing(str_replace('storage/', '', $rutaVieja));
    Storage::disk('public')->assertExists(str_replace('storage/', '', $rutaNueva));
});

test('permanently deleting a cancha removes its image from disk', function () {
    Storage::fake('public');

    $cancha = Cancha::factory()->create(['imagen_url' => 'storage/canchas/vieja.png']);
    Storage::disk('public')->put('canchas/vieja.png', 'contenido');

    $this->actingAs(adminAutenticado())
        ->delete("/admin/canchas/{$cancha->id}/eliminar")
        ->assertRedirect();

    $this->assertDatabaseMissing('canchas', ['id' => $cancha->id]);
    Storage::disk('public')->assertMissing('canchas/vieja.png');
});

test('home page only lists available canchas', function () {
    $disponible = Cancha::factory()->create();
    Cancha::factory()->desactivada()->create();

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('home/index')
            ->has('canchas', 1)
            ->where('canchas.0.id', $disponible->id));
});

test('admin canchas panel paginates nine canchas per page', function () {
    Cancha::factory()->count(10)->create();

    $this->actingAs(adminAutenticado())
        ->get('/admin/canchas')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('canchas.data', 9)
            ->where('canchas.total', 10));
});

test('cancha imagen_url falls back to the default image', function () {
    $cancha = Cancha::factory()->create(['imagen_url' => null]);

    expect($cancha->imagen_url)->toBe(asset('images/cancha-default.webp'));
});

test('cancha imagen_url returns an absolute url for a stored image', function () {
    $cancha = Cancha::factory()->create(['imagen_url' => 'storage/canchas/foto.png']);

    expect($cancha->imagen_url)->toBe(asset('storage/canchas/foto.png'));
});
