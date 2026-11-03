<?php

namespace App\Reserva\Models;

use App\Cancha\Models\Cancha;
use App\Reserva\Enums\EstadoReserva;
use Database\Factories\ReservaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected static function newFactory(): ReservaFactory
    {
        return ReservaFactory::new();
    }

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

    /**
     * Reservas que bloquean un turno (todas menos las canceladas).
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', '!=', EstadoReserva::Cancelada->value);
    }

    public function scopeDelDia(Builder $query, string $fecha): Builder
    {
        return $query->where('fecha_turno', $fecha);
    }

    public function scopeEnRangoFechas(Builder $query, string $desde, string $hasta): Builder
    {
        return $query->whereBetween('fecha_turno', [$desde, $hasta]);
    }
}
