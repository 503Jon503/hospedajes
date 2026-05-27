@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-bell text-primary"></i> Notificaciones</h2>

    @if($notificaciones->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bell-slash display-1 text-muted"></i>
            <p class="text-muted mt-3">No tienes notificaciones</p>
        </div>
    @else
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($notificaciones as $notificacion)
            <div class="card mb-3 {{ $notificacion->leida ? '' : 'border-primary' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3 align-items-start">
                            @if($notificacion->tipo === 'success')
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @elseif($notificacion->tipo === 'danger')
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            @elseif($notificacion->tipo === 'warning')
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                            @else
                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                            @endif
                            <div>
                                <h6 class="mb-1 {{ $notificacion->leida ? 'text-muted' : 'fw-bold' }}">
                                    {{ $notificacion->titulo }}
                                    @if(!$notificacion->leida)
                                        <span class="badge bg-primary ms-1">Nueva</span>
                                    @endif
                                </h6>
                                <p class="mb-1 text-muted small">{{ $notificacion->mensaje }}</p>
                                <small class="text-muted">{{ $notificacion->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if($notificacion->url)
                            <a href="{{ $notificacion->url }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-arrow-right"></i> Ver
                            </a>
                            @endif
                            <form action="{{ route('notificaciones.eliminar', $notificacion->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection