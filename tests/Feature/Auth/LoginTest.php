<?php

use App\Models\Admin;

test('guests can see the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('home/index'));
});

test('authenticated admins can see the home page', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin)
        ->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('home/index'));
});

test('admin can log in with valid credentials', function () {
    $admin = Admin::factory()->create([
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'secret-password',
    ]);

    $this->post('/login', [
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'secret-password',
    ])->assertRedirect('/admin/dashboard');

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
