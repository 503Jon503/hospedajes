<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'tipo',
        'url',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper para crear notificaciones fácilmente
    public static function enviar($userId, $titulo, $mensaje, $tipo = 'info', $url = null)
    {
        return self::create([
            'user_id' => $userId,
            'titulo'  => $titulo,
            'mensaje' => $mensaje,
            'tipo'    => $tipo,
            'url'     => $url,
            'leida'   => false,
        ]);
    }
}