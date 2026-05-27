@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-4">
                    <i class="bi bi-person-circle text-primary"></i> Mi Perfil
                </h3>

                <form action="{{ route('perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="text-muted mb-3">Información personal</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $user->nombre }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" value="{{ $user->apellido }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <small class="text-muted">El correo no se puede cambiar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $user->telefono }}">
                        </div>
                    </div>

                    @if($user->rol === 'propietario')
                    <hr>
                    <h5 class="text-muted mb-3"><i class="bi bi-bank"></i> Cuenta bancaria para recibir pagos</h5>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Registra tu cuenta bancaria para recibir los pagos de tus reservas cuando sean liberados.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banco</label>
                        <select name="banco" class="form-select">
                            <option value="">Seleccionar banco...</option>
                            <option value="Banco Agrícola" {{ $user->banco == 'Banco Agrícola' ? 'selected' : '' }}>Banco Agrícola</option>
                            <option value="Banco Davivienda" {{ $user->banco == 'Banco Davivienda' ? 'selected' : '' }}>Banco Davivienda</option>
                            <option value="Banco Cuscatlán" {{ $user->banco == 'Banco Cuscatlán' ? 'selected' : '' }}>Banco Cuscatlán</option>
                            <option value="Banco Atlántida" {{ $user->banco == 'Banco Atlántida' ? 'selected' : '' }}>Banco Atlántida</option>
                            <option value="Banco de América Central" {{ $user->banco == 'Banco de América Central' ? 'selected' : '' }}>Banco de América Central</option>
                            <option value="Banco Promerica" {{ $user->banco == 'Banco Promerica' ? 'selected' : '' }}>Banco Promerica</option>
                            <option value="Banco G&T Continental" {{ $user->banco == 'Banco G&T Continental' ? 'selected' : '' }}>Banco G&T Continental</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de cuenta</label>
                            <input type="text" name="cuenta_bancaria" class="form-control" value="{{ $user->cuenta_bancaria }}" placeholder="Ej: 1234567890">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del titular</label>
                            <input type="text" name="nombre_cuenta" class="form-control" value="{{ $user->nombre_cuenta }}" placeholder="Nombre completo del titular">
                        </div>
                    </div>
                    @if($user->cuenta_bancaria)
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Cuenta registrada: <strong>{{ $user->banco }}</strong> - {{ $user->cuenta_bancaria }} a nombre de {{ $user->nombre_cuenta }}
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No tienes cuenta bancaria registrada. Los pagos liberados no podrán ser transferidos.
                    </div>
                    @endif
                    @endif

                    <hr>
                    <h5 class="text-muted mb-3">Cambiar contraseña</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection