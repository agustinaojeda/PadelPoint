<?php

namespace App\Reserva\Controllers;

use App\Http\Controllers\Controller;
use App\Reserva\Actions\CreateReservaPublica;
use App\Reserva\Queries\GetDisponibilidadCanchas;
use App\Reserva\Requests\StoreReservaPublicaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ReservaController extends Controller
{
    public function __construct(
        private GetDisponibilidadCanchas $getDisponibilidadCanchas,
        private CreateReservaPublica $createReservaPublica,
    ) {}

    public function index(Request $request): Response
    {
        $fechaSolicitada = $request->query('fecha');

        $fecha = is_string($fechaSolicitada) && Carbon::hasFormat($fechaSolicitada, 'Y-m-d')
            ? $fechaSolicitada
            : now()->toDateString();

        return Inertia::render('reservas/index', [
            'canchas' => ($this->getDisponibilidadCanchas)($fecha),
            'fechaSeleccionada' => $fecha,
            'canchaDestacada' => $request->integer('cancha_id') ?: null,
        ]);
    }

    public function store(StoreReservaPublicaRequest $request): RedirectResponse
    {
        ($this->createReservaPublica)($request->validated());

        return back()->with('success', '¡Reserva solicitada! Te contactaremos para confirmarla.');
    }
}
