<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Cancha extends Model
{
    use HasFactory;

    protected $table = 'canchas';

    protected $fillable = [
        'nombre',
        'superficie',
        'es_techada',
        'esta_disponible',
        'precio',
        'imagen_url',
        'descripcion',
        'duracion_turno',
        'cantidad_jugadores',
        'hora_apertura',
        'hora_cierre',
        'dias_disponibles',
        'desactivada_en',
    ];

    protected $casts = [
        'es_techada' => 'boolean',
        'esta_disponible' => 'boolean',
        'desactivada_en' => 'datetime',
        'precio' => 'float',
        'duracion_turno' => 'integer',
        'cantidad_jugadores' => 'integer',
        'dias_disponibles' => 'array',
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * retorna siempre una URL completa y accesible para la imagen
     */
    protected function imagenUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value && Storage::disk('public')->exists(str_replace('storage/', '', $value))) {
                    return asset($value);
                }

                return asset('images/cancha-default.webp');
            }
        );
    }

    /**
     * helper para eliminar la imagen fisica del disco si no es la por defecto
     */
    public function eliminarImagenFisica(): void
    {
        $rawPath = $this->attributes['imagen_url'] ?? null;

        if ($rawPath && !str_contains($rawPath, 'cancha-default')) {
            $relativePath = str_replace('storage/', '', $rawPath);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }
}