<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Muestra la página principal pública.
     */
    public function __invoke(): Response
    {
        return Inertia::render('home/index', [
            'canchas' => Cancha::query()->disponibles()->orderBy('nombre')->get(),
        ]);
    }
}
