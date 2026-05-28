<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user_token')) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Correo o contraseña incorrectos.')->withInput();
        }

        $token = $user->createToken('web_token')->plainTextToken;

        session(['user_token' => $token]);
        session(['user_data'  => [
            'id'              => $user->id,
            'nombre'          => $user->nombre,
            'apellido'        => $user->apellido,
            'email'           => $user->email,
            'telefono'        => $user->telefono,
            'rol'             => $user->rol,
            'cuenta_bancaria' => $user->cuenta_bancaria,
            'banco'           => $user->banco,
            'nombre_cuenta'   => $user->nombre_cuenta,
        ]]);

        return redirect()->route('home')->with('success', '¡Bienvenido ' . $user->nombre . '!');
    }

    public function showRegister()
    {
        if (session()->has('user_token')) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre'            => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            ],
            'apellido'          => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            ],
            'email'             => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                'unique:users,email',
            ],
            'telefono'          => [
                'nullable',
                'string',
                'min:8',
                'max:15',
                'regex:/^[0-9\+\-\s]+$/',
            ],
            'rol'               => 'required|in:cliente,propietario',
            'password'          => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'nombre.min'                => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'                => 'El nombre no puede tener más de 50 caracteres.',
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios, sin números ni símbolos.',
            'apellido.required'         => 'El apellido es obligatorio.',
            'apellido.min'              => 'El apellido debe tener al menos 2 caracteres.',
            'apellido.max'              => 'El apellido no puede tener más de 50 caracteres.',
            'apellido.regex'            => 'El apellido solo puede contener letras y espacios, sin números ni símbolos.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'Ingresa un correo electrónico válido (ejemplo: nombre@correo.com).',
            'email.unique'              => 'Este correo ya está registrado. Intenta iniciar sesión.',
            'email.max'                 => 'El correo no puede tener más de 100 caracteres.',
            'telefono.min'              => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max'              => 'El teléfono no puede tener más de 15 dígitos.',
            'telefono.regex'            => 'El teléfono solo puede contener números, +, - y espacios.',
            'rol.required'              => 'Debes seleccionar un tipo de cuenta.',
            'rol.in'                    => 'El tipo de cuenta seleccionado no es válido.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max'              => 'La contraseña no puede tener más de 64 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.regex'            => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
        ]);

        $user = User::create([
            'nombre'   => trim($request->nombre),
            'apellido' => trim($request->apellido),
            'email'    => strtolower(trim($request->email)),
            'telefono' => $request->telefono,
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('web_token')->plainTextToken;

        session(['user_token' => $token]);
        session(['user_data'  => [
            'id'              => $user->id,
            'nombre'          => $user->nombre,
            'apellido'        => $user->apellido,
            'email'           => $user->email,
            'telefono'        => $user->telefono,
            'rol'             => $user->rol,
            'cuenta_bancaria' => null,
            'banco'           => null,
            'nombre_cuenta'   => null,
        ]]);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente! Bienvenido ' . $user->nombre . '.');
    }

    public function logout(Request $request)
    {
        $user = User::where('id', session('user_data.id'))->first();
        if ($user) {
            $user->tokens()->delete();
        }
        session()->flush();
        session()->regenerate();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada exitosamente.')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
    }

    public function perfil()
    {
        $user = User::findOrFail(session('user_data.id'));
        return view('auth.perfil', compact('user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = User::findOrFail(session('user_data.id'));

        $rules = [
            'nombre'          => [
                'required', 'string', 'min:2', 'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            ],
            'apellido'        => [
                'required', 'string', 'min:2', 'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
            ],
            'telefono'        => [
                'nullable', 'string', 'min:8', 'max:15',
                'regex:/^[0-9\+\-\s]+$/',
            ],
            'cuenta_bancaria' => 'nullable|string|max:50',
            'banco'           => 'nullable|string|max:100',
            'nombre_cuenta'   => 'nullable|string|max:100',
        ];

        if ($request->password) {
            $rules['password'] = [
                'min:8', 'max:64', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ];
        }

        $request->validate($rules, [
            'nombre.regex'    => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex'  => 'El apellido solo puede contener letras y espacios.',
            'telefono.regex'  => 'El teléfono solo puede contener números, +, - y espacios.',
            'password.regex'  => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $data = $request->only(['nombre', 'apellido', 'telefono', 'cuenta_bancaria', 'banco', 'nombre_cuenta']);
        $data['nombre']   = trim($data['nombre']);
        $data['apellido'] = trim($data['apellido']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        session(['user_data' => [
            'id'              => $user->id,
            'nombre'          => $user->nombre,
            'apellido'        => $user->apellido,
            'email'           => $user->email,
            'telefono'        => $user->telefono,
            'rol'             => $user->rol,
            'cuenta_bancaria' => $user->cuenta_bancaria,
            'banco'           => $user->banco,
            'nombre_cuenta'   => $user->nombre_cuenta,
        ]]);

        return redirect()->route('perfil')->with('success', 'Perfil actualizado exitosamente.');
    }
}