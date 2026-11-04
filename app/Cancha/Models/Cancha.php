<?php

namespace App\Cancha\Models;

use App\Cancha\Support\CanchaImagen;
use App\Reserva\Models\Reserva;
use Database\Factories\CanchaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cancha extends Model
{
    use HasFactory;

    protected static function newFactory(): CanchaFactory
    {
        return CanchaFactory::new();
    }

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

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('esta_disponible', true);
    }

    /**
     * Indica si la cancha atiende el día de la semana indicado (0 = domingo).
     */
    public function atiendeDia(int $diaSemana): bool
    {
        $dias = array_map('intval', $this->dias_disponibles ?? []);

        return in_array($diaSemana, $dias, true);
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
}
