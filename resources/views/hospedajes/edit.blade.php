@extends('layouts.app')

@section('title', 'Editar Hospedaje')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-4">
                    <i class="bi bi-pencil-square text-warning"></i> Editar Hospedaje
                </h3>
                <form action="{{ route('hospedajes.update', $hospedaje['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre del hospedaje</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $hospedaje['nombre'] }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="hotel" {{ $hospedaje['tipo'] == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                <option value="rancho" {{ $hospedaje['tipo'] == 'rancho' ? 'selected' : '' }}>Rancho</option>
                                <option value="casa" {{ $hospedaje['tipo'] == 'casa' ? 'selected' : '' }}>Casa</option>
                                <option value="apartamento" {{ $hospedaje['tipo'] == 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacidad (personas)</label>
                            <input type="number" name="capacidad" class="form-control" min="1" value="{{ $hospedaje['capacidad'] }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4" required>{{ $hospedaje['descripcion'] }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-control" value="{{ $hospedaje['ubicacion'] }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Departamento</label>
                            <select name="departamento" class="form-select" required>
                                <option value="Ahuachapán" {{ $hospedaje['departamento'] == 'Ahuachapán' ? 'selected' : '' }}>Ahuachapán</option>
                                <option value="Cabañas" {{ $hospedaje['departamento'] == 'Cabañas' ? 'selected' : '' }}>Cabañas</option>
                                <option value="Chalatenango" {{ $hospedaje['departamento'] == 'Chalatenango' ? 'selected' : '' }}>Chalatenango</option>
                                <option value="Cuscatlán" {{ $hospedaje['departamento'] == 'Cuscatlán' ? 'selected' : '' }}>Cuscatlán</option>
                                <option value="La Libertad" {{ $hospedaje['departamento'] == 'La Libertad' ? 'selected' : '' }}>La Libertad</option>
                                <option value="La Paz" {{ $hospedaje['departamento'] == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                <option value="La Unión" {{ $hospedaje['departamento'] == 'La Unión' ? 'selected' : '' }}>La Unión</option>
                                <option value="Morazán" {{ $hospedaje['departamento'] == 'Morazán' ? 'selected' : '' }}>Morazán</option>
                                <option value="San Miguel" {{ $hospedaje['departamento'] == 'San Miguel' ? 'selected' : '' }}>San Miguel</option>
                                <option value="San Salvador" {{ $hospedaje['departamento'] == 'San Salvador' ? 'selected' : '' }}>San Salvador</option>
                                <option value="San Vicente" {{ $hospedaje['departamento'] == 'San Vicente' ? 'selected' : '' }}>San Vicente</option>
                                <option value="Santa Ana" {{ $hospedaje['departamento'] == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                                <option value="Sonsonate" {{ $hospedaje['departamento'] == 'Sonsonate' ? 'selected' : '' }}>Sonsonate</option>
                                <option value="Usulután" {{ $hospedaje['departamento'] == 'Usulután' ? 'selected' : '' }}>Usulután</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio por noche ($)</label>
                            <input type="number" name="precio_noche" class="form-control" min="1" step="0.01" value="{{ $hospedaje['precio_noche'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="disponible" {{ $hospedaje['estado'] == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="no_disponible" {{ $hospedaje['estado'] == 'no_disponible' ? 'selected' : '' }}>No disponible</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen