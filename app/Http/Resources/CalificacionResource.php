<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'puntuacion'  => $this->puntuacion,
            'comentario'  => $this->comentario,
            'cliente'     => new UserResource($this->whenLoaded('cliente')),
            'creado_en'   => $this->created_at->format('d/m/Y'),
        ];
    }
}