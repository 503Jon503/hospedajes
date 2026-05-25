<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'rol'      => 'nullable|in:cliente,propietario',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'email.required'    => 'El correo es obligatorio',
            'email.unique'      => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed'=> 'Las contraseñas no coinciden',
        ];
    }
}