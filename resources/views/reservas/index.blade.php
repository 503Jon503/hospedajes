@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-calendar-check text-primary"></i> Mis Reservas</h2>

    @if(empty($reservas['data']))
        <div class="text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <p class="text-muted mt-3">No tienes reservas aún</p>
            <a href="{{ route('hospedajes.index') }}" class="btn btn-primary">Buscar hospedajes</a>
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Hospedaje</th>
                    <th>Cliente</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Personas</th>
                    <th>Total</th>
                    <th>Notas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservas['data'] as $reserva)
                <tr>
                    <td>{{ $reserva['id'] }}</td>
                    <td>
                        <strong>{{ $reserva['hospedaje']['nombre'] ?? 'N/A' }}</strong><br>
                        <small class="text-muted">{{ $reserva['hospedaje']['ubicacion'] ?? '' }}</small>
                    </td>
                    <td>
                        <i class="bi bi-person-circle text-primary"></i>
                        {{ $reserva['cliente']['nombre'] ?? 'N/A' }}
                        {{ $reserva['cliente']['apellido'] ?? '' }}
                    </td>
                    <td>{{ $reserva['fecha_inicio'] }}</td>
                    <td>{{ $reserva['fecha_fin'] }}</td>
                    <td>{{ $reserva['num_personas'] }}</td>
                    <td><strong>${{ number_format($reserva['total'], 2) }}</strong></td>
                    <td>
                        @if($reserva['notas'])
                            <span class="text-muted small">{{ $reserva['notas'] }}</span>
                        @else
                            <span class="text-muted small fst-italic">Sin notas</span>
                        @endif
                    </td>
                    <td>
                        @if($reserva['estado'] === 'confirmada')
                            <span class="badge bg-success">Confirmada</span>
                        @elseif($reserva['estado'] === 'pendiente')
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @else
                            <span class="badge bg-danger">Cancelada</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($reserva['estado'] !== 'cancelada')
                            <form action="{{ route('reservas.cancelar', $reserva['id']) }}" method="POST"
                                onsubmit="return confirm('¿Cancelar esta reserva?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </button>
                            </form>
                            @endif

                            @if(session('user_data.rol') === 'propietario' && $reserva['estado'] === 'pendiente')
                            <form action="{{ route('reservas.confirmar', $reserva['id']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-check-circle"></i> Confirmar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection