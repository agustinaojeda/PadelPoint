<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;
use App\Models\Cancha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CanchaController extends Controller
{
    /**
     * muestra el listado paginado de canchas
     */
    public function index(): Response
    {
        $canchas = Cancha::query()
            ->orderBy('esta_disponible', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return Inertia::render('canchas/index', [
            'canchas' => $canchas,
        ]);
    }

    /**
     * guarda una nueva cancha en la bd
     */
    public function store(StoreCanchaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['esta_disponible'] = true;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('canchas', 'public');
            $validated['imagen_url'] = 'storage/' . $path;
        } else {
            $validated['imagen_url'] = 'images/cancha-default.webp';
        }

        unset($validated['imagen']);

        Cancha::create($validated);

        return back()->with('success', 'Cancha creada con éxito.');
    }

    /**
     * actualiza la cancha existente
     */
    public function update(UpdateCanchaRequest $request, Cancha $cancha): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('imagen')) {
            // elimina la imagen previa si no es la default
            $cancha->eliminarImagenFisica();

            $path = $request->file('imagen')->store('canchas', 'public');
            $validated['imagen_url'] = 'storage/' . $path;
        } else {
            unset($validated['imagen_url']);
        }

        unset($validated['imagen']);

        $cancha->update($validated);

        return back()->with('success', 'Cancha actualizada con éxito.');
    }

    /**
     * baja logica 
     */
    public function destroy(Cancha $cancha): RedirectResponse
    {
        $cancha->update([
            'esta_disponible' => false,
            'desactivada_en' => now(),
        ]);

        return back()->with('success', 'Cancha desactivada con éxito.');
    }

    /**
     * reactivar cancha deshabilitada
     */
    public function activar(Cancha $cancha): RedirectResponse
    {
        $cancha->update([
            'esta_disponible' => true,
            'desactivada_en' => null,
        ]);

        return back()->with('success', 'Cancha activada correctamente.');
    }

    /**
     * eliminacion def de la cancha.
     */
    public function eliminar(Cancha $cancha): RedirectResponse
    {
        $cancha->eliminarImagenFisica();
        $cancha->delete();

        return back()->with('success', 'Cancha eliminada definitivamente.');
    }
}