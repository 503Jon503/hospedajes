@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-calendar-check text-primary"></i> 
        @if(session('user_data.rol') === 'propietario')
            Solicitudes y Reservas
        @else
            Mis Reservas
        @endif
    </h2>

    @if(empty($reservas['data']))
        <div class="text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <p class="text-muted mt-3">No hay reservas aún</p>
            <a href="{{ route('hospedajes.index') }}" class="btn btn-primary">Buscar hospedajes</a>
        </div>
    @else

    @if(session('user_data.rol') === 'propietario')
        @php
            $porHospedaje = collect($reservas['data'])->groupBy(fn($r) => $r['hospedaje']['nombre'] ?? 'Sin hospedaje');
        @endphp

        @foreach($porHospedaje as $nombreHospedaje => $reservasHospedaje)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-house-fill"></i> {{ $nombreHospedaje }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Fechas</th>
                                <th>Personas</th>
                                <th>Total</th>
                                <th>Notas</th>
                                <th>Solicitud</th>
                                <th>Pago</th>
                                <th>Llegada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservasHospedaje as $reserva)
                            <tr>
                                <td>{{ $reserva['id'] }}</td>
                                <td>
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <strong>{{ $reserva['cliente']['nombre'] ?? 'N/A' }} {{ $reserva['cliente']['apellido'] ?? '' }}</strong><br>
                                    <small class="text-muted">{{ $reserva['cliente']['email'] ?? '' }}</small><br>
                                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $reserva['cliente']['telefono'] ?? 'Sin teléfono' }}</small>
                                </td>
                                <td>
                                    <small><i class="bi bi-calendar-event"></i> {{ $reserva['fecha_inicio'] }}</small><br>
                                    <small><i class="bi bi-calendar-check"></i> {{ $reserva['fecha_fin'] }}</small>
                                </td>
                                <td><i class="bi bi-people"></i> {{ $reserva['num_personas'] }}</td>
                                <td><strong class="text-success">${{ number_format($reserva['total'], 2) }}</strong></td>
                                <td>
                                    @if($reserva['notas'])
                                        <small class="text-muted">{{ $reserva['notas'] }}</small>
                                    @else
                                        <small class="text-muted fst-italic">Sin notas</small>
                                    @endif
                                </td>
                                <td>
                                    @if($reserva['estado_propietario'] === 'aceptada')
                                        <span class="badge bg-success">Aceptada</span>
                                    @elseif($reserva['estado_propietario'] === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
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
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Confirmada</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        @if($reserva['estado_propietario'] === 'pendiente')
                                        <form action="{{ route('reservas.aceptar', $reserva['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle"></i> Aceptar
                                            </button>
                                        </form>
                                        <form action="{{ route('reservas.rechazar', $reserva['id']) }}" method="POST"
                                            onsubmit="return confirm('¿Rechazar esta solicitud?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle"></i> Rechazar
                                            </button>
                                        </form>
                                        @endif

                                        @if($reserva['estado'] !== 'cancelada' && $reserva['pago_estado'] === 'retenido')
                                        <form action="{{ route('reservas.reembolsar', $reserva['id']) }}" method="POST"
                                            onsubmit="return confirm('¿Cancelar y reembolsar al cliente?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-arrow-counterclockwise"></i> Cancelar y reembolsar
                                            </button>
                                        </form>
                                        @endif

                                        @if($reserva['estado_propietario'] !== 'rechazada' && $reserva['estado'] !== 'cancelada')
                                        <a href="{{ route('chat.show', $reserva['id']) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-chat-dots"></i> Chat
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

    @else
        {{-- Vista cliente --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Hospedaje</th>
                        <th>Fechas</th>
                        <th>Personas</th>
                        <th>Total</th>
                        <th>Notas</th>
                        <th>Solicitud</th>
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
                            <small><i class="bi bi-calendar-event"></i> {{ $reserva['fecha_inicio'] }}</small><br>
                            <small><i class="bi bi-calendar-check"></i> {{ $reserva['fecha_fin'] }}</small>
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
                            @if($reserva['estado_propietario'] === 'aceptada')
                                <span class="badge bg-success">Aceptada ✅</span>
                            @elseif($reserva['estado_propietario'] === 'rechazada')
                                <span class="badge bg-danger">Rechazada ❌</span>
                            @else
                                <span class="badge bg-warning text-dark">Esperando respuesta...</span>
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
                                @if($reserva['estado_propietario'] === 'aceptada' && $reserva['pago_estado'] === 'pendiente')
                                <a href="{{ route('pagos.checkout', $reserva['id']) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-credit-card"></i> Pagar
                                </a>
                                @endif

                                @if($reserva['pago_estado'] === 'retenido' && !$reserva['cliente_confirmo_llegada'])
                                <form action="{{ route('pagos.confirmarLlegada', $reserva['id']) }}" method="POST"
                                    onsubmit="return confirm('¿Confirmar que ya llegaste? Esto liberará el pago automáticamente.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="bi bi-geo-alt-fill"></i> Confirmar llegada
                                    </button>
                                </form>
                                @endif

                                @if($reserva['estado'] !== 'cancelada' && $reserva['pago_estado'] === 'pendiente')
                                <form action="{{ route('reservas.cancelar', $reserva['id']) }}" method="POST"
                                    onsubmit="return confirm('¿Cancelar esta reserva?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </form>
                                @endif

                                @if($reserva['estado_propietario'] !== 'rechazada' && $reserva['estado'] !== 'cancelada')
                                <a href="{{ route('chat.show', $reserva['id']) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-chat-dots"></i> Chat
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    @endif
</div>
@endsection