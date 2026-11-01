<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

test('admin can update their password', function () {
    $admin = Admin::factory()->create([
        'contrasenaAdmin' => 'current-password',
    ]);

    $this->actingAs($admin)
        ->put('/settings/password', [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect();

    expect(Hash::check('new-password', $admin->fresh()->contrasenaAdmin))->toBeTrue();
});

test('password update requires the current password', function () {
    $admin = Admin::factory()->create([
        'contrasenaAdmin' => 'current-password',
    ]);

    $this->actingAs($admin)
        ->put('/settings/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('current-password', $admin->fresh()->contrasenaAdmin))->toBeTrue();
});
