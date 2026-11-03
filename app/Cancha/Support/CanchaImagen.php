<?php

namespace App\Cancha\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CanchaImagen
{
    public const DEFAULT_PATH = 'images/cancha-default.webp';

    private const DISK = 'public';

    private const DIRECTORY = 'canchas';

    /**
     * Guarda la imagen subida y devuelve la ruta persistible en la base de datos.
     */
    public static function store(UploadedFile $imagen): string
    {
        $path = $imagen->store(self::DIRECTORY, self::DISK);

        return 'storage/'.$path;
    }

    /**
     * Devuelve la URL pública de la imagen o la imagen por defecto.
     */
    public static function url(?string $ruta): string
    {
        if (self::isDefault($ruta)) {
            return asset(self::DEFAULT_PATH);
        }

        return asset($ruta);
    }

    public static function deleteIfCustom(?string $ruta): void
    {
        if (self::isDefault($ruta)) {
            return;
        }

        $relativePath = self::relativePath($ruta);

        if (Storage::disk(self::DISK)->exists($relativePath)) {
            Storage::disk(self::DISK)->delete($relativePath);
        }
    }

    public static function isDefault(?string $ruta): bool
    {
        return $ruta === null || $ruta === '' || str_contains($ruta, 'cancha-default');
    }

    private static function relativePath(?string $ruta): string
    {
        return str_replace('storage/', '', (string) $ruta);
    }
}
