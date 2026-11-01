<?php

namespace App\Http\Controllers;

use App\Actions\Reserva\ChangeEstadoReserva;
use App\Actions\Reserva\CreateReserva;
use App\Actions\Reserva\DeleteReserva;
use App\Enums\EstadoReserva;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateEstadoReservaRequest;
use App\Models\Cancha;
use App\Models\Reserva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservaController extends Controller
{
    public function __construct(
        private CreateReserva $createReserva,
        private ChangeEstadoReserva $changeEstadoReserva,
        private DeleteReserva $deleteReserva,
    ) {}

    public function index(Request $request): Response
    {
        $fecha = $request->input('fecha', now()->toDateString());

        $canchas = Cancha::query()->where('esta_disponible', true)->get();

        $reservas = Reserva::query()
            ->with('cancha')
            ->where('fecha_turno', $fecha)
            ->get();

        return Inertia::render('reservas/index', [
            'canchas' => $canchas,
            'reservas' => $reservas,
            'fechaSeleccionada' => $fecha,
        ]);
    }

    public function store(StoreReservaRequest $request): RedirectResponse
    {
        ($this->createReserva)($request->validated());

        return back()->with('success', 'Reserva registrada con éxito.');
    }

    public function cambiarEstado(Reserva $reserva, UpdateEstadoReservaRequest $request): RedirectResponse
    {
        ($this->changeEstadoReserva)(
            $reserva,
            EstadoReserva::from($request->validated('estado')),
        );

        return back()->with('success', 'Estado de reserva actualizado.');
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        ($this->deleteReserva)($reserva);

        return back()->with('success', 'Reserva eliminada con éxito.');
    }
}
