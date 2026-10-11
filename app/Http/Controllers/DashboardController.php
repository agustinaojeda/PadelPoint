<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Reserva::marcarVencidasComoCompletadas();

        return Inertia::render('Dashboard', Reserva::obtenerDatosDashboard());
    }
}