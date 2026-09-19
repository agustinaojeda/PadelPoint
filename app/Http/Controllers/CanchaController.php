<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Cancha;
use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;

class CanchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCanchaRequest $request)
    {
        $validated = $request->validated();
        $validated['esta_disponible'] = true;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('canchas', 'public'); //le da un nombre unico
            $validated['imagen_url'] = 'storage/' . $path;
        } else {
            $validated['imagen_url'] = 'images/cancha-default.webp';
        }

        unset($validated['imagen']);

        Cancha::create($validated);

        return redirect()->back()->with('success', 'Cancha creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCanchaRequest $request, Cancha $cancha)
    {
        $validated = $request->validated();

        if ($request->hasFile('imagen')) {
            //elimina imagen anterior si no es la default
            if ($cancha->imagen_url && $cancha->imagen_url !== 'images/cancha-default.jpg') {
                $oldPath = str_replace('storage/', '', $cancha->imagen_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('imagen')->store('canchas', 'public');
            $validated['imagen_url'] = 'storage/' . $path;
        } else {
            unset($validated['imagen_url']);
        }

        unset($validated['imagen']);

        $cancha->update($validated);

        return redirect()->back()->with('success', 'Cancha actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage. 
     * baja fisica de eliminar no se hace, solo se cambia el estado no disponible
     */
    public function destroy(Cancha $cancha)
    {
        $cancha->update([
        'esta_disponible' => false,
        'desactivada_en' => now(), 
    ]);

        return redirect()->back()->with('success', 'Cancha desactivada con éxito.');
    }

    /**elimina completamente la cancha */
    public function eliminar(Cancha $cancha)
    {
        if ($cancha->imagen_url && $cancha->imagen_url !== 'images/cancha-default.jpg') {
            $path = str_replace('storage/', '', $cancha->imagen_url);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $cancha->delete();

        return redirect()->back();
    }

    public function activar(Cancha $cancha)
{
    $cancha->update([
        'esta_disponible' => true,
        'desactivada_en' => null, 
    ]);

    return back()->with('success', 'Cancha activada correctamente.');
}
}
