@extends('layouts.app')

@section('title', 'Pago Exitoso')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 text-center">
                <i class="bi bi-check-circle-fill display-1 text-success mb-3"></i>
                <h3 class="mb-2">¡Pago realizado exitosamente!</h3>
                <p class="text-muted mb-4">Tu pago está retenido de forma segura. Se liberará al propietario cuando llegues al hospedaje.</p>

                <div class="alert alert-success text-start">
                    <p class="mb-1"><strong>Hospedaje:</strong> {{ $reserva->hospedaje->nombre }}</p>
                    <p class="mb-1"><strong>Llegada:</strong> {{ $reserva->fecha_inicio->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Salida:</strong> {{ $reserva->fecha_fin->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Total pagado:</strong> ${{ number_format($reserva->total, 2) }} USD</p>
                    <p class="mb-0"><strong>Estado del pago:</strong> <span class="badge bg-warning text-dark">Retenido</span></p>
                </div>

                <div class="alert alert-info text-start">
                    <i class="bi bi-info-circle"></i> El propietario liberará el pago cuando confirmes tu llegada al hospedaje.
                </div>

                <a href="{{ route('reservas.index') }}" class="btn btn-primary w-100">
                    <i class="bi bi-calendar-check"></i> Ver mis reservas
                </a>
            </div>
        </div>
    </div>
</div>
@endsection