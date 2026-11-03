<?php

namespace App\Http\Controllers;

use App\Cancha\Queries\GetCanchasPublicas;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Muestra la página principal pública.
     */
    public function __invoke(GetCanchasPublicas $getCanchasPublicas): Response
    {
        return Inertia::render('home/index', [
            'canchas' => $getCanchasPublicas(),
        ]);
    }
}
