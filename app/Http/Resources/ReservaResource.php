<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'fecha_inicio' => $this->fecha_inicio->format('d/m/Y'),
            'fecha_fin'    => $this->fecha_fin->format('d/m/Y'),
            'num_personas' => $this->num_personas,
            'total'        => $this->total,
            'estado'       => $this->estado,
            'notas'        => $this->notas,
            'cliente'      => new UserResource($this->whenLoaded('cliente')),
            'hospedaje'    => new HospedajeResource($this->whenLoaded('hospedaje')),
            'creado_en'    => $this->created_at->format('d/m/Y'),
        ];
    }
}