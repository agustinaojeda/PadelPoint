<?php

namespace App\Cancha\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CanchaImagen
{
    public const DEFAULT_PATH = 'images/cancha-default.webp';

    private const DIRECTORY = 'canchas';

    /**
     * Guarda la imagen subida y devuelve la ruta persistible en la base de datos.
     */
    public static function store(UploadedFile $imagen): string
    {
        return $imagen->store(self::DIRECTORY, self::disk());
    }

    /**
     * Devuelve la URL pública de la imagen o la imagen por defecto.
     */
    public static function url(?string $ruta): string
    {
        if (self::isDefault($ruta)) {
            return asset(self::DEFAULT_PATH);
        }

        return Storage::disk(self::disk())->url($ruta);
    }

    public static function deleteIfCustom(?string $ruta): void
    {
        if (self::isDefault($ruta)) {
            return;
        }

        $disco = Storage::disk(self::disk());

        if ($disco->exists($ruta)) {
            $disco->delete($ruta);
        }
    }

    public static function isDefault(?string $ruta): bool
    {
        return $ruta === null || $ruta === '' || str_contains($ruta, 'cancha-default');
    }

    private static function disk(): string
    {
        return (string) (config('filesystems.canchas_disk') ?: 'public');
    }
}
