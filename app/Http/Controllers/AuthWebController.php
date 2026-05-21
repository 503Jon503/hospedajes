<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthWebController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8000/api/v1';

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

        $response = Http::post("{$this->apiUrl}/login", [
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            session(['user_token' => $data['token']]);
            session(['user_data'  => $data['user']]);
            return redirect()->route('home')->with('success', '¡Bienvenido ' . $data['user']['nombre'] . '!');
        }

        return back()->with('error', 'Credenciales incorrectas')->withInput();
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
            'nombre'             => 'required|string',
            'apellido'           => 'required|string',
            'email'              => 'required|email',
            'telefono'           => 'nullable|string',
            'rol'                => 'required|in:cliente,propietario',
            'password'           => 'required|min:8|confirmed',
        ]);

        $response = Http::post("{$this->apiUrl}/register", $request->all());

        if ($response->successful()) {
            $data = $response->json();
            session(['user_token' => $data['token']]);
            session(['user_data'  => $data['user']]);
            return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente!');
        }

        $errors = $response->json('errors', []);
        return back()->withErrors($errors)->withInput();
    }

    public function logout(Request $request)
    {
        Http::withToken(session('user_token'))->post("{$this->apiUrl}/logout");
        session()->flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente');
    }
}