<?php

namespace App\Models;

use App\Support\CanchaImagen;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cancha extends Model
{
    use HasFactory;

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

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'es_techada' => 'boolean',
            'esta_disponible' => 'boolean',
            'desactivada_en' => 'datetime',
            'precio' => 'float',
            'duracion_turno' => 'integer',
            'cantidad_jugadores' => 'integer',
            'dias_disponibles' => 'array',
        ];
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Retorna siempre una URL completa y accesible para la imagen.
     */
    protected function imagenUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => CanchaImagen::url($value),
        );
    }

    /**
     * Elimina la imagen física del disco si no es la imagen por defecto.
     */
    public function eliminarImagenFisica(): void
    {
        CanchaImagen::deleteFor($this);
    }
}
