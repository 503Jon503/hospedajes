<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'hospedaje_id',
        'fecha_inicio',
        'fecha_fin',
        'num_personas',
        'total',
        'estado',
        'pago_estado',
        'stripe_payment_intent',
        'cliente_confirmo_llegada',
        'fecha_confirmacion_llegada',
        'notas',
    ];

    protected $casts = [
        'fecha_inicio'              => 'date',
        'fecha_fin'                 => 'date',
        'total'                     => 'decimal:2',
        'num_personas'              => 'integer',
        'cliente_confirmo_llegada'  => 'boolean',
        'fecha_confirmacion_llegada'=> 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hospedaje()
    {
        return $this->belongsTo(Hospedaje::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function calcularTotal()
    {
        $dias = $this->fecha_inicio->diffInDays($this->fecha_fin);
        return $dias * $this->hospedaje->precio_noche;
    }
}