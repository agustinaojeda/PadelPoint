<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombreAdmin' => ['required', 'string', 'max:120'],
            'correoAdmin' => ['required', 'string', 'email', 'max:255', 'unique:admins,correoAdmin'],
            'contrasenaAdmin' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::create([
            'nombreAdmin' => $request->nombreAdmin,
            'correoAdmin' => $request->correoAdmin,
            'contrasenaAdmin' => Hash::make($request->contrasenaAdmin),
        ]);

        event(new Registered($admin));

        Auth::guard('admin')->login($admin);

        return to_route('dashboard');
    }
}