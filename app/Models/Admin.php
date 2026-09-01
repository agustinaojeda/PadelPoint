<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';

    protected $fillable = [
        'nombreAdmin',
        'correoAdmin',
        'contrasenaAdmin',
    ];

    protected $hidden = [
        'contrasenaAdmin',
    ];

    public function getAuthPasswordName()
    {
        return 'contrasenaAdmin';
    }
}
