<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancha extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombreCancha',
        'esTechada',
        'estaActiva',
        'precio',
        'fechaDesactivacion'
    ];

    protected $casts = [
        'esTechada' => 'boolean',
        'estaActiva' => 'boolean',
        'precio' => 'float',
        'fechaDesactivacion' => 'datetime',
    ];
}
