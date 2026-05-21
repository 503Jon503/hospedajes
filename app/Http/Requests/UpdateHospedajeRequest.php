<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHospedajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->esPropietario() || auth()->user()->esAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre'       => 'sometimes|string|max:150',
            'tipo'         => 'sometimes|in:hotel,rancho,casa,apartamento',
            'descripcion'  => 'sometimes|string',
            'ubicacion'    => 'sometimes|string|max:200',
            'departamento' => 'sometimes|string|max:100',
            'precio_noche' => 'sometimes|numeric|min:1',
            'capacidad'    => 'sometimes|integer|min:1',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'estado'       => 'sometimes|in:disponible,no_disponible',
        ];
    }
}