<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'hospedaje_id' => 'required|exists:hospedajes,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'num_personas' => 'required|integer|min:1',
            'notas'        => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'hospedaje_id.required' => 'El hospedaje es obligatorio',
            'hospedaje_id.exists'   => 'El hospedaje no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria',
            'fecha_fin.after'       => 'La fecha de fin debe ser posterior a la de inicio',
            'num_personas.required' => 'El número de personas es obligatorio',
            'num_personas.min'      => 'Debe haber al menos 1 persona',
        ];
    }
}