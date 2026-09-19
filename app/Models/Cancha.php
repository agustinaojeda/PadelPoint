<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = ['imagen_url'];

    public function getImagenUrlAttribute(): string
    {
        if ($this->attributes['imagen_url'] ?? false) {
            return asset($this->attributes['imagen_url']);
        }

        // si no tiene foto, se carga la default
        return asset('images/cancha-default.webp');
    }
}
