<?php

namespace App\Models;

use App\Enums\EstadoReserva;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cancha_id',
        'fecha_turno',
        'hora_inicio',
        'hora_fin',
        'nombre_cliente',
        'telefono_cliente',
        'monto_total',
        'estado',
        'observaciones',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'fecha_turno' => 'date:Y-m-d',
            'monto_total' => 'float',
            'estado' => EstadoReserva::class,
        ];
    }

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class);
    }

    public function scopeConfirmadasOCompletadas(Builder $query): Builder
    {
        return $query->whereIn('estado', [
            EstadoReserva::Confirmada->value,
            EstadoReserva::Completada->value,
        ]);
    }

    public function scopeEnRangoFechas(Builder $query, string $desde, string $hasta): Builder
    {
        return $query->whereBetween('fecha_turno', [$desde, $hasta]);
    }
}
