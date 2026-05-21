<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'puntuacion'  => 'required|integer|min:1|max:5',
            'comentario'  => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'puntuacion.required' => 'La puntuación es obligatoria',
            'puntuacion.min'      => 'La puntuación mínima es 1',
            'puntuacion.max'      => 'La puntuación máxima es 5',
        ];
    }
}