<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospedaje extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'descripcion',
        'ubicacion',
        'departamento',
        'precio_noche',
        'capacidad',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'precio_noche' => 'decimal:2',
        'capacidad'    => 'integer',
    ];

    public function propietario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function fotos()
    {
        return $this->hasMany(FotoHospedaje::class)->orderBy('orden');
    }

    public function promedioCalificacion()
    {
        return $this->calificaciones()->avg('puntuacion');
    }

    public function scopeDisponible($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeFiltrarPorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeFiltrarPorPrecio($query, $min, $max)
    {
        return $query->whereBetween('precio_noche', [$min, $max]);
    }

    public function scopeFiltrarPorUbicacion($query, $ubicacion)
    {
        return $query->where('ubicacion', 'like', "%$ubicacion%")
                     ->orWhere('departamento', 'like', "%$ubicacion%");
    }
}