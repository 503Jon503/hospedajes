@extends('layouts.app')

@section('title', 'Hospedajes')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-house-fill text-primary"></i> Todos los hospedajes</h2>
        </div>
        @if(session('user_data.rol') === 'propietario' || session('user_data.rol') === 'admin')
        <div class="col-md-4 text-end">
            <a href="{{ route('hospedajes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Publicar hospedaje
            </a>
        </div>
        @endif
    </div>

    <form action="{{ route('hospedajes.index') }}" method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="{{ request('buscar') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="ubicacion" class="form-control" placeholder="Ubicación" value="{{ request('ubicacion') }}">
        </div>
        <div class="col-md-2">
            <select name="tipo" class="form-select">
                <option value="">Todos</option>
                <option value="hotel" {{ request('tipo') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                <option value="rancho" {{ request('tipo') == 'rancho' ? 'selected' : '' }}>Rancho</option>
                <option value="casa" {{ request('tipo') == 'casa' ? 'selected' : '' }}>Casa</option>
                <option value="apartamento" {{ request('tipo') == 'apartamento' ? 'selected' : '' }}>Apartamento</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Filtrar
            </button>
        </div>
        <div class="col-md-1">
            <a href="{{ route('hospedajes.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x"></i>
            </a>
        </div>
    </form>

    @if(empty($hospedajes['data']))
        <div class="text-center py-5">
            <i class="bi bi-house-x display-1 text-muted"></i>
            <p class="text-muted mt-3">No se encontraron hospedajes</p>
        </div>
    @else
    <div class="row g-4">
        @foreach($hospedajes['data'] as $hospedaje)
        <div class="col-md-4">
            <div class="card h-100">
                @if($hospedaje['imagen'])
                    <img src="{{ $hospedaje['imagen'] }}" class="card-img-top" alt="{{ $hospedaje['nombre'] }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px">
                        <i class="bi bi-house-fill display-3 text-muted"></i>
                    </div>
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">{{ $hospedaje['nombre'] }}</h5>
                        <span class="badge bg-primary">{{ ucfirst($hospedaje['tipo']) }}</span>
                    </div>
                    <p class="text-muted small">
                        <i class="bi bi-geo-alt"></i> {{ $hospedaje['ubicacion'] }}, {{ $hospedaje['departamento'] }}
                    </p>
                    <p class="card-text text-muted small">{{ Str::limit($hospedaje['descripcion'], 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="precio">${{ number_format($hospedaje['precio_noche'], 2) }}/noche</span>
                        <span class="text-warning">
                            <i class="bi bi-star-fill"></i> {{ $hospedaje['promedio_calificacion'] ?? '0' }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="{{ route('hospedajes.show', $hospedaje['id']) }}" class="btn btn-outline-primary w-100">
                        Ver detalles
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection