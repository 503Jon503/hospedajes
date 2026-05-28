<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id',
        'remitente_id',
        'destinatario_id',
        'mensaje',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}