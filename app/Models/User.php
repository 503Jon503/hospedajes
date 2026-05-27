<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'rol',
        'cuenta_bancaria',
        'banco',
        'nombre_cuenta',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function hospedajes()
    {
        return $this->hasMany(Hospedaje::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function esAdmin()
    {
        return $this->rol === 'admin';
    }

    public function esPropietario()
    {
        return $this->rol === 'propietario';
    }

    public function esCliente()
    {
        return $this->rol === 'cliente';
    }
}