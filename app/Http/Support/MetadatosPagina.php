<?php

namespace App\Http\Support;

use Illuminate\Http\Request;

class MetadatosPagina
{
    private const IMAGEN = 'images/og-padelpoint.jpg';

    /**
     * Resuelve los metadatos de compartir de la página solicitada.
     *
     * @return array{titulo: string, descripcion: string, url: string, imagen: string, imagenAncho: int, imagenAlto: int, indexable: bool}
     */
    public function para(Request $request): array
    {
        $pagina = $this->pagina($request);

        return [
            'titulo' => $pagina['titulo'],
            'descripcion' => $pagina['descripcion'],
            'url' => $request->url(),
            'imagen' => asset(self::IMAGEN),
            'imagenAncho' => 1200,
            'imagenAlto' => 630,
            'indexable' => $pagina['indexable'],
        ];
    }

    /**
     * @return array{titulo: string, descripcion: string, indexable: bool}
     */
    private function pagina(Request $request): array
    {
        return match (true) {
            $request->routeIs('home') => [
                'titulo' => 'PadelPoint | Reservá tu cancha de pádel online',
                'descripcion' => 'Elegí cancha, fecha y horario, y reservá tu turno de pádel online en segundos. Consultá la disponibilidad actualizada sin crear una cuenta.',
                'indexable' => true,
            ],
            $request->routeIs('reservas.index') => [
                'titulo' => 'Reservar turno | PadelPoint',
                'descripcion' => 'Consultá la disponibilidad de canchas de pádel y reservá tu turno online en segundos. No necesitás crear una cuenta.',
                'indexable' => true,
            ],
            default => [
                'titulo' => config('app.name', 'PadelPoint'),
                'descripcion' => 'Reservá tu cancha de pádel online en segundos.',
                'indexable' => false,
            ],
        };
    }
}
