<?php

namespace App\Http\Controllers;

use App\Actions\Cancha\ActivateCancha;
use App\Actions\Cancha\CreateCancha;
use App\Actions\Cancha\DeactivateCancha;
use App\Actions\Cancha\DeleteCancha;
use App\Actions\Cancha\UpdateCancha;
use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;
use App\Models\Cancha;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CanchaController extends Controller
{
    public function __construct(
        private CreateCancha $createCancha,
        private UpdateCancha $updateCancha,
        private DeactivateCancha $deactivateCancha,
        private ActivateCancha $activateCancha,
        private DeleteCancha $deleteCancha,
    ) {}

    /**
     * Muestra el listado paginado de canchas.
     */
    public function index(): Response
    {
        $canchas = Cancha::query()
            ->orderBy('esta_disponible', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return Inertia::render('admin/canchas/index', [
            'canchas' => $canchas,
        ]);
    }

    /**
     * Guarda una nueva cancha en la base de datos.
     */
    public function store(StoreCanchaRequest $request): RedirectResponse
    {
        ($this->createCancha)($request->validated(), $request->file('imagen'));

        return back()->with('success', 'Cancha creada con éxito.');
    }

    /**
     * Actualiza la cancha existente.
     */
    public function update(UpdateCanchaRequest $request, Cancha $cancha): RedirectResponse
    {
        ($this->updateCancha)($cancha, $request->validated(), $request->file('imagen'));

        return back()->with('success', 'Cancha actualizada con éxito.');
    }

    /**
     * Baja lógica de la cancha.
     */
    public function destroy(Cancha $cancha): RedirectResponse
    {
        ($this->deactivateCancha)($cancha);

        return back()->with('success', 'Cancha desactivada con éxito.');
    }

    /**
     * Reactiva una cancha deshabilitada.
     */
    public function activar(Cancha $cancha): RedirectResponse
    {
        ($this->activateCancha)($cancha);

        return back()->with('success', 'Cancha activada correctamente.');
    }

    /**
     * Eliminación definitiva de la cancha.
     */
    public function eliminar(Cancha $cancha): RedirectResponse
    {
        ($this->deleteCancha)($cancha);

        return back()->with('success', 'Cancha eliminada definitivamente.');
    }
}
