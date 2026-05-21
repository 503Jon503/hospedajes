<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospedajeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nombre'              => $this->nombre,
            'tipo'                => $this->tipo,
            'descripcion'         => $this->descripcion,
            'ubicacion'           => $this->ubicacion,
            'departamento'        => $this->departamento,
            'precio_noche'        => $this->precio_noche,
            'capacidad'           => $this->capacidad,
            'imagen'              => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'estado'              => $this->estado,
            'promedio_calificacion' => round($this->promedioCalificacion(), 1),
            'total_calificaciones'  => $this->calificaciones->count(),
            'propietario'         => new UserResource($this->whenLoaded('propietario')),
            'calificaciones'      => CalificacionResource::collection($this->whenLoaded('calificaciones')),
            'creado_en'           => $this->created_at->format('d/m/Y'),
        ];
    }
}