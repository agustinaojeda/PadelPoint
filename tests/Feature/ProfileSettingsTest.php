<?php

use App\Models\Admin;

test('authenticated users can update their profile settings', function () {
    /** @var Admin $user */
    $user = Admin::factory()->create();

    $response = $this->actingAs($user)->patch('/settings/profile', [
        'name' => 'Nuevo nombre',
        'email' => 'nuevo@padelpoint.com',
    ]);

    $response->assertRedirect('/settings/profile');
    $this->assertDatabaseHas('admins', [
        'id' => $user->id,
        'nombreAdmin' => 'Nuevo nombre',
        'correoAdmin' => 'nuevo@padelpoint.com',
    ]);
});

test('authenticated users can delete their account', function () {
    /** @var Admin $user */
    $user = Admin::factory()->create();

    $response = $this->actingAs($user)->delete('/settings/profile', [
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseMissing('admins', [
        'id' => $user->id,
    ]);
});
