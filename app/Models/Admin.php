<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'nombreAdmin',
        'correoAdmin',
        'contrasenaAdmin',
    ];

    protected $hidden = [
        'contrasenaAdmin',
        'remember_token',
    ];

    public function getAuthPasswordName()
    {
        return 'contrasenaAdmin';
    }

    public function getEmailForVerification()
    {
        return $this->correoAdmin;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correoAdmin;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correoAdmin;
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
}
}
