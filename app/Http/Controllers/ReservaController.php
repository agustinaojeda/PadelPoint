<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', now()->format('Y-m-d'));

        $ahoraFecha = now()->format('Y-m-d');
        $ahoraHora = now()->format('H:i:s');

        Reserva::where('estado', 'confirmada')
            ->where(function ($query) use ($ahoraFecha, $ahoraHora) {
                $query->where('fecha_turno', '<', $ahoraFecha)
                    ->orWhere(function ($q) use ($ahoraFecha, $ahoraHora) {
                        $q->where('fecha_turno', '=', $ahoraFecha)
                          ->where('hora_fin', '<=', $ahoraHora);
                    });
            })
            ->update(['estado' => 'completada']);

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cancha_id' => 'required|exists:canchas,id',
            'fecha_turno' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'nombre_cliente' => 'required|string|max:255',
            'telefono_cliente' => 'required|string|max:50',
            'monto_total' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $solapamiento = Reserva::where('cancha_id', $validated['cancha_id'])
            ->where('fecha_turno', $validated['fecha_turno'])
            ->where('hora_inicio', $validated['hora_inicio'])
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($solapamiento) {
            return back()->withErrors(['hora_inicio' => 'Este turno ya fue reservado por otro usuario.']);
        }

        Reserva::create([
            ...$validated,
            'estado' => 'confirmada',
        ]);

        return redirect()->back()->with('success', 'Reserva registrada con éxito.');
    }

    public function cambiarEstado(Reserva $reserva, Request $request)
{
    if ($reserva->estado === 'completada') {
        return back()->withErrors(['estado' => 'No se puede modificar el estado de una reserva completada.']);
    }

    $validated = $request->validate([
        'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
    ]);

    $reserva->update(['estado' => $validated['estado']]);

    return redirect()->back()->with('success', 'Estado de reserva actualizado.');
}

public function destroy(Reserva $reserva)
{
    if ($reserva->estado === 'completada') {
        return back()->withErrors(['reserva' => 'No se puede eliminar una reserva que ya fue completada.']);
    }

    $reserva->delete();

    return redirect()->back()->with('success', 'Reserva eliminada.');
}
}