@extends('layouts.app')

@section('title', 'Gestionar Reservas')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-calendar text-warning"></i> Gestionar Reservas</h2>
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
                    <thead class="table-warning">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Hospedaje</th>
                            <th>Fecha inicio</th>
                            <th>Fecha fin</th>
                            <th>Personas</th>
                            <th>Total</th>
                            <th>Notas</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->id }}</td>
                            <td>
                                <i class="bi bi-person-circle text-primary"></i>
                                {{ $reserva->cliente->nombre ?? 'N/A' }}
                                {{ $reserva->cliente->apellido ?? '' }}
                            </td>
                            <td>
                                <strong>{{ $reserva->hospedaje->nombre ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $reserva->hospedaje->ubicacion ?? '' }}</small>
                            </td>
                            <td>{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
                            <td>{{ $reserva->fecha_fin->format('d/m/Y') }}</td>
                            <td>{{ $reserva->num_personas }}</td>
                            <td><strong>${{ number_format($reserva->total, 2) }}</strong></td>
                            <td>
                                <small class="text-muted">{{ $reserva->notas ?? 'Sin notas' }}</small>
                            </td>
                            <td>
                                @if($reserva->estado === 'confirmada')
                                    <span class="badge bg-success">Confirmada</span>
                                @elseif($reserva->estado === 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @else
                                    <span class="badge bg-danger">Cancelada</span>
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