<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'apellido'  => $this->apellido,
            'email'     => $this->email,
            'telefono'  => $this->telefono,
            'rol'       => $this->rol,
            'creado_en' => $this->created_at->format('d/m/Y'),
        ];
    }
}