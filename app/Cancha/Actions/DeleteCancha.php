<?php

namespace App\Cancha\Actions;

use App\Cancha\Models\Cancha;
use App\Cancha\Support\CanchaImagen;
use App\Reserva\Enums\EstadoReserva;
use Illuminate\Validation\ValidationException;

class DeleteCancha
{
    /**
     * Elimina definitivamente una cancha sin reservas. Si tiene reservas,
     * la oculta del panel conservando el historial y borra su imagen.
     *
     * @return bool Indica si la cancha fue ocultada en lugar de eliminada.
     *
     * @throws ValidationException Si la cancha tiene reservas por efectuar.
     */
    public function __invoke(Cancha $cancha): bool
    {
        if ($this->tieneReservasPorEfectuar($cancha)) {
            throw ValidationException::withMessages([
                'cancha' => 'Esta cancha tiene reservas por efectuar. Cancelalas o reprogramalas antes de eliminarla.',
            ]);
        }

        $rutaImagen = $cancha->getRawOriginal('imagen_url');

        if ($cancha->reservas()->exists()) {
            $cancha->update([
                'esta_disponible' => false,
                'desactivada_en' => $cancha->desactivada_en ?? now(),
                'oculta_en' => now(),
                'imagen_url' => null,
            ]);

            CanchaImagen::deleteIfCustom($rutaImagen);

            return true;
        }

        $cancha->delete();

        CanchaImagen::deleteIfCustom($rutaImagen);

        return false;
    }

    /**
     * Indica si la cancha tiene reservas pendientes o confirmadas de hoy en adelante.
     */
    private function tieneReservasPorEfectuar(Cancha $cancha): bool
    {
        return $cancha->reservas()
            ->whereIn('estado', [
                EstadoReserva::Pendiente->value,
                EstadoReserva::Confirmada->value,
            ])
            ->where('fecha_turno', '>=', now()->toDateString())
            ->exists();
    }
}
