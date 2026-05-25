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
                    <th>Fechas</th>
                    <th>Personas</th>
                    <th>Total</th>
                    <th>Notas</th>
                    <th>Estado</th>
                    <th>Pago</th>
                    <th>Llegada</th>
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
                    <td>
                        <small><i class="bi bi-arrow-right"></i> {{ $reserva['fecha_inicio'] }}</small><br>
                        <small><i class="bi bi-arrow-left"></i> {{ $reserva['fecha_fin'] }}</small>
                    </td>
                    <td>{{ $reserva['num_personas'] }}</td>
                    <td><strong>${{ number_format($reserva['total'], 2) }}</strong></td>
                    <td>
                        @if($reserva['notas'])
                            <small class="text-muted">{{ $reserva['notas'] }}</small>
                        @else
                            <small class="text-muted fst-italic">Sin notas</small>
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
                        @if($reserva['pago_estado'] === 'retenido')
                            <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Retenido</span>
                        @elseif($reserva['pago_estado'] === 'liberado')
                            <span class="badge bg-success"><i class="bi bi-unlock-fill"></i> Liberado</span>
                        @elseif($reserva['pago_estado'] === 'reembolsado')
                            <span class="badge bg-info">Reembolsado</span>
                        @else
                            <span class="badge bg-secondary">Sin pago</span>
                        @endif
                    </td>
                    <td>
                        @if($reserva['cliente_confirmo_llegada'])
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Confirmada</span><br>
                            <small class="text-muted">{{ $reserva['fecha_confirmacion_llegada'] }}</small>
                        @else
                            <span class="badge bg-secondary">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            @if(session('user_data.rol') === 'cliente' && $reserva['pago_estado'] === 'pendiente' && $reserva['estado'] !== 'cancelada')
                            <a href="{{ route('pagos.checkout', $reserva['id']) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-credit-card"></i> Pagar
                            </a>
                            @endif

                            @if(session('user_data.rol') === 'cliente' && $reserva['pago_estado'] === 'retenido' && !$reserva['cliente_confirmo_llegada'])
                            <form action="{{ route('pagos.confirmarLlegada', $reserva['id']) }}" method="POST"
                                onsubmit="return confirm('¿Confirmar que ya llegaste al hospedaje?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="bi bi-geo-alt-fill"></i> Confirmar llegada
                                </button>
                            </form>
                            @endif

                            @if(session('user_data.rol') === 'propietario' && $reserva['pago_estado'] === 'retenido' && $reserva['cliente_confirmo_llegada'])
                            <form action="{{ route('pagos.liberar', $reserva['id']) }}" method="POST"
                                onsubmit="return confirm('¿Liberar el pago al propietario?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-unlock-fill"></i> Liberar pago
                                </button>
                            </form>
                            @endif

                            @if($reserva['estado'] !== 'cancelada')
                            <form action="{{ route('reservas.cancelar', $reserva['id']) }}" method="POST"
                                onsubmit="return confirm('¿Cancelar esta reserva?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle"></i>
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