<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(fn (Admin $admin, string $token) => (new MailMessage)
            ->subject('Restablecé tu contraseña de PadelPoint')
            ->markdown('emails.auth.restablecer-password', [
                'url' => url(route('password.reset', [
                    'token' => $token,
                    'email' => $admin->getEmailForPasswordReset(),
                ], false)),
                'expira' => config('auth.passwords.admins.expire'),
            ]));
    }
}
