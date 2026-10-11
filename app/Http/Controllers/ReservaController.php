<?php

namespace App\Http\Controllers;

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
    public function index(Request $request): Response
    {
        $fecha = $request->input('fecha', now()->toDateString());

        Reserva::marcarVencidasComoCompletadas();

        $canchas = Cancha::where('esta_disponible', true)->get();

        $reservas = Reserva::with('cancha')
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
        $validated = $request->validated();

        if (Reserva::tieneSolapamiento(
            $validated['cancha_id'],
            $validated['fecha_turno'],
            $validated['hora_inicio'],
            $validated['hora_fin']
        )) {
            return back()->withErrors(['hora_inicio' => 'El rango horario seleccionado se solapa con una reserva existente.']);
        }

        Reserva::create([
            ...$validated,
            'estado' => 'confirmada',
        ]);

        return back()->with('success', 'Reserva registrada con éxito.');
    }

    public function cambiarEstado(Reserva $reserva, UpdateEstadoReservaRequest $request): RedirectResponse
    {
        if ($reserva->estado === 'completada') {
            return back()->withErrors(['estado' => 'No se puede modificar el estado de una reserva completada.']);
        }

        $reserva->update(['estado' => $request->validated()['estado']]);

        return back()->with('success', 'Estado de reserva actualizado.');
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        if ($reserva->estado === 'completada') {
            return back()->withErrors(['reserva' => 'No se puede eliminar una reserva que ya fue completada.']);
        }

        $reserva->delete();

        return back()->with('success', 'Reserva eliminada con éxito.');
    }
}