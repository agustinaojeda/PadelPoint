<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Muestra la página de cambio de contraseña.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Actualiza la contraseña del administrador autenticado.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'contrasenaAdmin' => $validated['password'],
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
