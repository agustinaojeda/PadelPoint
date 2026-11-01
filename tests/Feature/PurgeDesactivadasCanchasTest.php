<?php

use App\Models\Cancha;
use App\Models\Reserva;

test('the purge command deletes old deactivated canchas without reservas', function () {
    $cancha = Cancha::factory()->create([
        'esta_disponible' => false,
        'desactivada_en' => now()->subDays(31),
    ]);

    $this->artisan('app:purge-desactivadas-canchas')->assertSuccessful();

    $this->assertDatabaseMissing('canchas', ['id' => $cancha->id]);
});

test('the purge command keeps deactivated canchas that still have reservas', function () {
    $cancha = Cancha::factory()->create([
        'esta_disponible' => false,
        'desactivada_en' => now()->subDays(31),
    ]);

    Reserva::factory()->create(['cancha_id' => $cancha->id]);

    $this->artisan('app:purge-desactivadas-canchas')->assertSuccessful();

    $this->assertDatabaseHas('canchas', ['id' => $cancha->id]);
});

test('the purge command keeps recently deactivated canchas', function () {
    $cancha = Cancha::factory()->create([
        'esta_disponible' => false,
        'desactivada_en' => now()->subDays(5),
    ]);

    $this->artisan('app:purge-desactivadas-canchas')->assertSuccessful();

    $this->assertDatabaseHas('canchas', ['id' => $cancha->id]);
});
