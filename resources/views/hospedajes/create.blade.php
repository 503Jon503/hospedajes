@extends('layouts.app')

@section('title', 'Publicar Hospedaje')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-4">
                    <i class="bi bi-plus-circle text-primary"></i> Publicar Hospedaje
                </h3>
                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif
                <form action="{{ route('hospedajes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del hospedaje</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="hotel">Hotel</option>
                                <option value="rancho">Rancho</option>
                                <option value="casa">Casa</option>
                                <option value="apartamento">Apartamento</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacidad (personas)</label>
                            <input type="number" name="capacidad" class="form-control" min="1" value="{{ old('capacidad') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Departamento</label>
                            <select name="departamento" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="Ahuachapán">Ahuachapán</option>
                                <option value="Cabañas">Cabañas</option>
                                <option value="Chalatenango">Chalatenango</option>
                                <option value="Cuscatlán">Cuscatlán</option>
                                <option value="La Libertad">La Libertad</option>
                                <option value="La Paz">La Paz</option>
                                <option value="La Unión">La Unión</option>
                                <option value="Morazán">Morazán</option>
                                <option value="San Miguel">San Miguel</option>
                                <option value="San Salvador">San Salvador</option>
                                <option value="San Vicente">San Vicente</option>
                                <option value="Santa Ana">Santa Ana</option>
                                <option value="Sonsonate">Sonsonate</option>
                                <option value="Usulután">Usulután</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio por noche ($)</label>
                        <input type="number" name="precio_noche" class="form-control" min="1" step="0.01" value="{{ old('precio_noche') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen principal</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                        <small class="text-muted">Esta será la imagen que aparece en la lista de hospedajes.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fotos adicionales</label>
                        <input type="file" name="fotos[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar varias fotos a la vez (Ctrl+Click).</small>
                    </div>
                    <div id="preview-fotos" class="row g-2 mb-3"></div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Publicar
                        </button>
                        <a href="{{ route('hospedajes.mis') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelector('input[name="fotos[]"]').addEventListener('change', function(e) {
        const preview = document.getElementById('preview-fotos');
        preview.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML += `
                    <div class="col-md-3">
                        <img src="${e.target.result}" class="img-fluid rounded" style="height:100px; width:100%; object-fit:cover;">
                    </div>`;
            }
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection