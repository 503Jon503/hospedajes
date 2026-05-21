<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HospedajesES')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 12px; }
        .card-img-top { border-radius: 12px 12px 0 0; height: 200px; object-fit: cover; }
        .badge-tipo { font-size: 0.75rem; }
        .precio { font-size: 1.3rem; font-weight: bold; color: #198754; }
        .hero { background: linear-gradient(135deg, #0d6efd, #0dcaf0); color: white; padding: 80px 0; }
        .footer { background: #212529; color: #adb5bd; padding: 30px 0; margin-top: 60px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-house-heart-fill"></i> HospedajesES
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hospedajes.index') }}">Hospedajes</a>
                </li>
                @if(session('user_data.rol') === 'propietario' || session('user_data.rol') === 'admin')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hospedajes.mis') }}">Mis Hospedajes</a>
                </li>
                @endif
                @if(session('user_token'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reservas.index') }}">Mis Reservas</a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav">
                @if(session('user_token'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ session('user_data.nombre') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ session('user_data.rol') }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-light btn-sm ms-2" href="{{ route('register') }}">Registrarse</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<main>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="container text-center">
        <p class="mb-0">&copy; 2026 HospedajesES. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>