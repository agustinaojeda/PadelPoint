<?php

namespace App\Cancha\Controllers;

use App\Cancha\Actions\ActivateCancha;
use App\Cancha\Actions\CreateCancha;
use App\Cancha\Actions\DeactivateCancha;
use App\Cancha\Actions\DeleteCancha;
use App\Cancha\Actions\UpdateCancha;
use App\Cancha\Models\Cancha;
use App\Cancha\Queries\GetCanchasAdmin;
use App\Cancha\Requests\StoreCanchaRequest;
use App\Cancha\Requests\UpdateCanchaRequest;
use App\Http\Controllers\Controller;
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
    public function index(GetCanchasAdmin $getCanchasAdmin): Response
    {
        return Inertia::render('admin/canchas/index', [
            'canchas' => $getCanchasAdmin(),
        ]);
    }

    /**
     * Guarda una nueva cancha en la base de datos.
     */
    public function store(StoreCanchaRequest $request): RedirectResponse
    {
        ($this->createCancha)($request->safe()->except('imagen'), $request->file('imagen'));

        return back()->with('success', 'Cancha creada con éxito.');
    }

    /**
     * Actualiza la cancha existente.
     */
    public function update(UpdateCanchaRequest $request, Cancha $cancha): RedirectResponse
    {
        ($this->updateCancha)($cancha, $request->safe()->except('imagen'), $request->file('imagen'));

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
