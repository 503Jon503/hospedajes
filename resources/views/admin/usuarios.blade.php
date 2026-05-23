@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-people text-primary"></i> Gestionar Usuarios</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al panel
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Registrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>
                                <i class="bi bi-person-circle text-primary"></i>
                                {{ $usuario->nombre }} {{ $usuario->apellido }}
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                            <td>
                                <form action="{{ route('admin.usuarios.rol', $usuario->id) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <select name="rol" class="form-select form-select-sm" style="width:130px">
                                        <option value="cliente" {{ $usuario->rol == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                        <option value="propietario" {{ $usuario->rol == 'propietario' ? 'selected' : '' }}>Propietario</option>
                                        <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($usuario->id !== session('user_data.id'))
                                <form action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-secondary">Tú</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection