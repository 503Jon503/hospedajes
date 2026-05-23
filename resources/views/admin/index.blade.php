@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-speedometer2 text-primary"></i> Panel de Administración</h2>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                <h2 class="fw-bold">{{ $totalUsuarios }}</h2>
                <p class="text-muted mb-0">Usuarios registrados</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="bi bi-house-fill display-4 text-success mb-3"></i>
                <h2 class="fw-bold">{{ $totalHospedajes }}</h2>
                <p class="text-muted mb-0">Hospedajes publicados</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="bi bi-calendar-check-fill display-4 text-warning mb-3"></i>
                <h2 class="fw-bold">{{ $totalReservas }}</h2>
                <p class="text-muted mb-0">Reservas totales</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="bi bi-clock-fill display-4 text-danger mb-3"></i>
                <h2 class="fw-bold">{{ $reservasPendientes }}</h2>
                <p class="text-muted mb-0">Reservas pendientes</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-people display-4 text-primary mb-3"></i>
                <h5>Gestionar Usuarios</h5>
                <p class="text-muted small">Ver, cambiar roles y eliminar usuarios</p>
                <a href="{{ route('admin.usuarios') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> Ir a usuarios
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-house display-4 text-success mb-3"></i>
                <h5>Gestionar Hospedajes</h5>
                <p class="text-muted small">Ver y eliminar hospedajes publicados</p>
                <a href="{{ route('admin.hospedajes') }}" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> Ir a hospedajes
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-calendar display-4 text-warning mb-3"></i>
                <h5>Gestionar Reservas</h5>
                <p class="text-muted small">Ver todas las reservas del sistema</p>
                <a href="{{ route('admin.reservas') }}" class="btn btn-warning">
                    <i class="bi bi-arrow-right"></i> Ir a reservas
                </a>
            </div>
        </div>
    </div>
</div>
@endsection