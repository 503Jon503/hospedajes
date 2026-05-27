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
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Credenciales incorrectas')->withInput();
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
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'rol'      => 'required|in:cliente,propietario',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
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

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente!');
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
            ->with('success', 'Sesión cerrada exitosamente')
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
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'telefono'        => 'nullable|string|max:20',
            'cuenta_bancaria' => 'nullable|string|max:50',
            'banco'           => 'nullable|string|max:100',
            'nombre_cuenta'   => 'nullable|string|max:100',
        ];

        if ($request->password) {
            $rules['password'] = 'min:8|confirmed';
        }

        $request->validate($rules);

        $data = $request->only(['nombre', 'apellido', 'telefono', 'cuenta_bancaria', 'banco', 'nombre_cuenta']);

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

        return redirect()->route('perfil')->with('success', 'Perfil actualizado exitosamente');
    }
}