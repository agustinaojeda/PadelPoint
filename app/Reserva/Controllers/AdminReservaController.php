<?php

namespace App\Reserva\Controllers;

use App\Http\Controllers\Controller;
use App\Reserva\Actions\ChangeEstadoReserva;
use App\Reserva\Actions\CreateReserva;
use App\Reserva\Actions\DeleteReserva;
use App\Reserva\Models\Reserva;
use App\Reserva\Queries\GetGrillaReservas;
use App\Reserva\Requests\StoreReservaRequest;
use App\Reserva\Requests\UpdateEstadoReservaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReservaController extends Controller
{
    public function __construct(
        private CreateReserva $createReserva,
        private ChangeEstadoReserva $changeEstadoReserva,
        private DeleteReserva $deleteReserva,
    ) {}

    public function index(Request $request, GetGrillaReservas $getGrillaReservas): Response
    {
        return Inertia::render('admin/reservas/index', $getGrillaReservas($request->query('fecha')));
    }

    public function store(StoreReservaRequest $request): RedirectResponse
    {
        ($this->createReserva)($request->validated());

        return back()->with('success', 'Reserva registrada con éxito.');
    }

    public function cambiarEstado(Reserva $reserva, UpdateEstadoReservaRequest $request): RedirectResponse
    {
        ($this->changeEstadoReserva)($reserva, $request->estado());

        return back()->with('success', 'Estado de reserva actualizado.');
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        ($this->deleteReserva)($reserva);

        return back()->with('success', 'Reserva eliminada con éxito.');
    }
}
