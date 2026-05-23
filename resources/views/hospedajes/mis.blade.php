@extends('layouts.app')

@section('title', 'Mis Hospedajes')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-house-gear text-primary"></i> Mis Hospedajes</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('hospedajes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Publicar nuevo
            </a>
        </div>
    </div>

    @if(empty($hospedajes['data']))
        <div class="text-center py-5">
            <i class="bi bi-house-x display-1 text-muted"></i>
            <p class="text-muted mt-3">No tienes hospedajes publicados aún</p>
            <a href="{{ route('hospedajes.create') }}" class="btn btn-primary">Publicar mi primer hospedaje</a>
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
                    <h5 class="card-title">{{ $hospedaje['nombre'] }}</h5>
                    <p class="text-muted small">
                        <i class="bi bi-geo-alt"></i> {{ $hospedaje['ubicacion'] }}
                    </p>
                    <p class="precio">${{ number_format($hospedaje['precio_noche'], 2) }}/noche</p>
                    <span class="badge {{ $hospedaje['estado'] === 'disponible' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($hospedaje['estado']) }}
                    </span>
                </div>
                <div class="card-footer bg-white border-0 pb-3 d-flex gap-2">
                    <a href="{{ route('hospedajes.show', $hospedaje['id']) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('hospedajes.edit', $hospedaje['id']) }}" class="btn btn-outline-warning btn-sm flex-fill">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('hospedajes.destroy', $hospedaje['id']) }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de eliminar este hospedaje?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection