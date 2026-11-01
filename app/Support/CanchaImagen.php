<?php

namespace App\Support;

use App\Models\Cancha;
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
        if ($ruta !== null && Storage::disk(self::DISK)->exists(self::relativePath($ruta))) {
            return asset($ruta);
        }

        return asset(self::DEFAULT_PATH);
    }

    /**
     * Elimina del disco la imagen de la cancha si no es la imagen por defecto.
     */
    public static function deleteFor(Cancha $cancha): void
    {
        self::deleteIfCustom($cancha->getRawOriginal('imagen_url'));
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
        return $ruta === null || str_contains($ruta, 'cancha-default');
    }

    private static function relativePath(?string $ruta): string
    {
        return str_replace('storage/', '', (string) $ruta);
    }
}
