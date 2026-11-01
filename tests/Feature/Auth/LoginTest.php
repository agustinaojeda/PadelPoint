<?php

use App\Models\Admin;

test('guests are redirected from the root to the login page', function () {
    $this->get('/')->assertRedirect('/login');
});

test('authenticated admins are redirected from the root to the dashboard', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin)->get('/')->assertRedirect('/dashboard');
});

test('admin can log in with valid credentials', function () {
    $admin = Admin::factory()->create([
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'secret-password',
    ]);

    $this->post('/login', [
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'secret-password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($admin);
});

test('admin cannot log in with invalid credentials', function () {
    Admin::factory()->create([
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'secret-password',
    ]);

    $this->post('/login', [
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'incorrecta',
    ])->assertSessionHasErrors('correoAdmin');

    $this->assertGuest();
});

test('admin can log out', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin)->post('/logout')->assertRedirect('/');

    $this->assertGuest();
});
