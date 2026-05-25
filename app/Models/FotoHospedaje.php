<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoHospedaje extends Model
{
    use HasFactory;

    protected $table = 'foto_hospedajes';

    protected $fillable = [
        'hospedaje_id',
        'ruta',
        'orden',
    ];

    public function hospedaje()
    {
        return $this->belongsTo(Hospedaje::class);
    }
}