@extends('layouts.app')

@section('title', 'Gestionar Hospedajes')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-house text-success"></i> Gestionar Hospedajes</h2>
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
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Ubicación</th>
                            <th>Precio/noche</th>
                            <th>Propietario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hospedajes as $hospedaje)
                        <tr>
                            <td>{{ $hospedaje->id }}</td>
                            <td><strong>{{ $hospedaje->nombre }}</strong></td>
                            <td><span class="badge bg-primary">{{ ucfirst($hospedaje->tipo) }}</span></td>
                            <td>
                                <small>{{ $hospedaje->ubicacion }}</small><br>
                                <small class="text-muted">{{ $hospedaje->departamento }}</small>
                            </td>
                            <td><strong>${{ number_format($hospedaje->precio_noche, 2) }}</strong></td>
                            <td>
                                <i class="bi bi-person-circle text-primary"></i>
                                {{ $hospedaje->propietario->nombre ?? 'N/A' }}
                                {{ $hospedaje->propietario->apellido ?? '' }}
                            </td>
                            <td>
                                <span class="badge {{ $hospedaje->estado === 'disponible' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($hospedaje->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('hospedajes.show', $hospedaje->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.hospedajes.eliminar', $hospedaje->id) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar este hospedaje?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
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