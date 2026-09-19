<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'cancha_id',
        'fecha_turno',
        'hora_inicio',
        'hora_fin',
        'nombre_cliente',
        'telefono_cliente',
        'monto_total',
        'estado',
    ];

    protected $casts = [
        'fecha_turno' => 'date:Y-m-d',
        'monto_total' => 'float',
    ];

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class);
    }
}