@extends('layouts.app')

@section('title', $hospedaje['nombre'])

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">

            {{-- Galería de fotos --}}
            @if(!empty($hospedaje['fotos']) && count($hospedaje['fotos']) > 0)
                <div id="carouselFotos" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($hospedaje['fotos'] as $index => $foto)
                            <button type="button" data-bs-target="#carouselFotos" data-bs-slide-to="{{ $index }}"
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded">
                        @foreach($hospedaje['fotos'] as $index => $foto)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ $foto }}" class="d-block w-100" style="height:400px; object-fit:cover;" alt="Foto {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    @if(count($hospedaje['fotos']) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotos" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselFotos" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
            @elseif($hospedaje['imagen'])
                <img src="{{ $hospedaje['imagen'] }}" class="img-fluid rounded mb-4 w-100" style="height:400px; object-fit:cover" alt="{{ $hospedaje['nombre'] }}">
            @else
                <div class="bg-light rounded mb-4 d-flex align-items-center justify-content-center" style="height:400px">
                    <i class="bi bi-house-fill display-1 text-muted"></i>
                </div>
            @endif

            <h1>{{ $hospedaje['nombre'] }}</h1>
            <p class="text-muted">
                <i class="bi bi-geo-alt"></i> {{ $hospedaje['ubicacion'] }}, {{ $hospedaje['departamento'] }}
                &nbsp;|&nbsp;
                <span class="badge bg-primary">{{ ucfirst($hospedaje['tipo']) }}</span>
                &nbsp;|&nbsp;
                <i class="bi bi-people"></i> Capacidad: {{ $hospedaje['capacidad'] }} personas
            </p>

            <div class="card mb-4">
                <div class="card-body">
                    <h5>Descripción</h5>
                    <p>{{ $hospedaje['descripcion'] }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5><i class="bi bi-calendar3 text-primary"></i> Disponibilidad</h5>
                    <p class="text-muted small">Las fechas en <span class="text-danger fw-bold">rojo</span> están ocupadas y no se pueden seleccionar.</p>
                    <input id="calendario" class="form-control" placeholder="Ver calendario de disponibilidad" readonly>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5><i class="bi bi-star-fill text-warning"></i> Calificaciones</h5>
                    @if(empty($hospedaje['calificaciones']))
                        <p class="text-muted">Aún no hay calificaciones.</p>
                    @else
                        @foreach($hospedaje['calificaciones'] as $cal)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $cal['cliente']['nombre'] }} {{ $cal['cliente']['apellido'] }}</strong>
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $cal['puntuacion'] ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="mb-0 text-muted small">{{ $cal['comentario'] }}</p>
                            <small class="text-muted">{{ $cal['creado_en'] }}</small>
                        </div>
                        @endforeach
                    @endif

                    @if($puedeCalificar)
                    <hr>
                    <h6>Deja tu calificación</h6>
                    <form action="{{ route('calificaciones.store', $hospedaje['id']) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Puntuación</label>
                            <select name="puntuacion" class="form-select">
                                <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                                <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                                <option value="3">⭐⭐⭐ Bueno</option>
                                <option value="2">⭐⭐ Regular</option>
                                <option value="1">⭐ Malo</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <textarea name="comentario" class="form-control" rows="3" placeholder="Escribe tu comentario..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-star"></i> Enviar calificación
                        </button>
                    </form>
                    @elseif(session('user_token') && session('user_data.rol') === 'cliente')
                    <hr>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Podrás calificar este hospedaje una vez que tu estancia haya concluido.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px">
                <div class="card-body">
                    <h3 class="precio text-center mb-4">${{ number_format($hospedaje['precio_noche'], 2) }}<small class="text-muted fs-6">/noche</small></h3>

                    <div class="text-center mb-3">
                        <span class="text-warning fs-5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $hospedaje['promedio_calificacion'] ? '-fill' : '' }}"></i>
                            @endfor
                        </span>
                        <span class="text-muted small">({{ $hospedaje['total_calificaciones'] }} reseñas)</span>
                    </div>

                    <p class="text-center">
                        <span class="badge bg-success">{{ ucfirst($hospedaje['estado']) }}</span>
                    </p>

                    @if(session('user_token') && session('user_data.rol') === 'cliente')
                    <form action="{{ route('reservas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="hospedaje_id" value="{{ $hospedaje['id'] }}">
                        <div class="mb-3">
                            <label class="form-label">Fecha de llegada</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de salida</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número de personas</label>
                            <input type="number" name="num_personas" class="form-control" min="1" max="{{ $hospedaje['capacidad'] }}" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notas (opcional)</label>
                            <textarea name="notas" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-calendar-check"></i> Reservar ahora
                        </button>
                    </form>
                    @elseif(!session('user_token'))
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Inicia sesión para reservar
                    </a>
                    @endif

                    <hr>
                    <p class="text-muted small mb-1"><i class="bi bi-person"></i> Propietario: {{ $hospedaje['propietario']['nombre'] ?? '' }} {{ $hospedaje['propietario']['apellido'] ?? '' }}</p>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar"></i> Publicado: {{ $hospedaje['creado_en'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-day.disabled {
        background-color: #ffcccc !important;
        color: #cc0000 !important;
        text-decoration: line-through;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
    const fechasOcupadas = @json($fechasOcupadas ?? []);

    flatpickr("#calendario", {
        mode: "range",
        inline: true,
        locale: "es",
        disable: fechasOcupadas,
        dateFormat: "Y-m-d",
    });

    flatpickr("#fecha_inicio", {
        locale: "es",
        minDate: "today",
        disable: fechasOcupadas,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates) {
            let nextDay = new Date(selectedDates[0]);
            nextDay.setDate(nextDay.getDate() + 1);
            fechaFinPicker.set('minDate', nextDay);
        }
    });

    const fechaFinPicker = flatpickr("#fecha_fin", {
        locale: "es",
        minDate: "today",
        disable: fechasOcupadas,
        dateFormat: "Y-m-d",
    });
</script>
@endsection