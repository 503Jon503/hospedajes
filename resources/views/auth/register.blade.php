@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="text-center mb-4">
                    <i class="bi bi-person-plus-fill text-primary"></i> Crear Cuenta
                </h3>
                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif
                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de cuenta</label>
                        <select name="rol" class="form-select" required>
                            <option value="cliente">Cliente (quiero reservar)</option>
                            <option value="propietario">Propietario (quiero publicar)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-check"></i> Crear Cuenta
                    </button>
                </form>
                <hr>
                <p class="text-center mb-0">
                    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection