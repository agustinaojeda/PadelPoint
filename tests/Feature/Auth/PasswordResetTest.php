<?php

use App\Models\Admin;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('envía el link de restablecimiento', function () {
    Notification::fake();

    $admin = Admin::factory()->create(['correoAdmin' => 'admin@padelpoint.com']);

    $this->post('/forgot-password', ['email' => 'admin@padelpoint.com'])
        ->assertRedirect();

    Notification::assertSentTo($admin, ResetPassword::class);
    $this->assertDatabaseHas('password_reset_tokens', ['email' => 'admin@padelpoint.com']);
});

test('restablece la contraseña con un token válido', function () {
    $admin = Admin::factory()->create(['correoAdmin' => 'admin@padelpoint.com']);
    $token = Password::broker('admins')->createToken($admin);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'admin@padelpoint.com',
        'password' => 'nueva-clave',
        'password_confirmation' => 'nueva-clave',
    ])->assertRedirect(route('login'));

    expect(Hash::check('nueva-clave', $admin->fresh()->contrasenaAdmin))->toBeTrue();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'admin@padelpoint.com']);
});

test('rechaza un token inválido', function () {
    $admin = Admin::factory()->create([
        'correoAdmin' => 'admin@padelpoint.com',
        'contrasenaAdmin' => 'clave-original',
    ]);

    Password::broker('admins')->createToken($admin);

    $this->from('/reset-password/token-invalido')
        ->post('/reset-password', [
            'token' => 'token-invalido',
            'email' => 'admin@padelpoint.com',
            'password' => 'nueva-clave',
            'password_confirmation' => 'nueva-clave',
        ])
        ->assertSessionHasErrors('email');

    expect(Hash::check('clave-original', $admin->fresh()->contrasenaAdmin))->toBeTrue();
});

test('limita los pedidos de link', function () {
    Admin::factory()->create(['correoAdmin' => 'admin@padelpoint.com']);

    foreach (range(1, 6) as $intento) {
        $this->post('/forgot-password', ['email' => 'admin@padelpoint.com'])
            ->assertRedirect();
    }

    $this->post('/forgot-password', ['email' => 'admin@padelpoint.com'])
        ->assertStatus(429);
});

test('el mail de reset está en español', function () {
    $admin = Admin::factory()->create();

    $mail = (new ResetPassword('token-de-prueba'))->toMail($admin);

    expect($mail->subject)->toContain('Restablecé')
        ->and((string) $mail->render())->toContain('Restablecé tu contraseña');
});
