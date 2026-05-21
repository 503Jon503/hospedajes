<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHospedajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->esPropietario() || auth()->user()->esAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:hotel,rancho,casa,apartamento',
            'descripcion'  => 'required|string',
            'ubicacion'    => 'required|string|max:200',
            'departamento' => 'required|string|max:100',
            'precio_noche' => 'required|numeric|min:1',
            'capacidad'    => 'required|integer|min:1',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'estado'       => 'nullable|in:disponible,no_disponible',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El nombre es obligatorio',
            'tipo.required'         => 'El tipo de hospedaje es obligatorio',
            'tipo.in'               => 'El tipo debe ser hotel, rancho, casa o apartamento',
            'descripcion.required'  => 'La descripción es obligatoria',
            'ubicacion.required'    => 'La ubicación es obligatoria',
            'departamento.required' => 'El departamento es obligatorio',
            'precio_noche.required' => 'El precio por noche es obligatorio',
            'precio_noche.min'      => 'El precio debe ser mayor a 0',
            'capacidad.required'    => 'La capacidad es obligatoria',
            'capacidad.min'         => 'La capacidad debe ser al menos 1',
            'imagen.image'          => 'El archivo debe ser una imagen',
            'imagen.max'            => 'La imagen no debe superar 2MB',
        ];
    }
}