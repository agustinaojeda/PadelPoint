<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombreAdmin',
        'correoAdmin',
        'contrasenaAdmin',
    ];

    protected $hidden = [
        'contrasenaAdmin',
        'remember_token',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'contrasenaAdmin' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasenaAdmin';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correoAdmin;
    }

    public function routeNotificationForMail(mixed $notification = null): string
    {
        return $this->correoAdmin;
    }
}
