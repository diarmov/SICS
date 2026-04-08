@extends('layouts.admin')

@section('title', 'Agregar Imagen al Carrusel - SICS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Agregar Imagen al Carrusel</h2>
            <p class="text-muted">Agrega una nueva imagen que aparecerá en el carrusel de la página principal.</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('carrusel.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-tinto text-white">
            <h5 class="mb-0">Datos de la Imagen</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('carrusel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                        name="titulo" value="{{ old('titulo') }}" required>
                    @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen *</label>
                    <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen"
                        name="imagen" accept="image/*" required>
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
                    @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">URL (Opcional)</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url"
                        value="{{ old('url') }}" placeholder="https://ejemplo.com">
                    <small class="form-text text-muted">Si se proporciona, al hacer clic en la imagen se abrirá este
                        enlace.</small>
                    @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="orden" class="form-label">Orden</label>
                    <input type="number" class="form-control @error('orden') is-invalid @enderror" id="orden"
                        name="orden" value="{{ old('orden', 0) }}" min="0">
                    <small class="form-text text-muted">Define el orden de aparición (menor número = más a la
                        izquierda).</small>
                    @error('orden')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-tinto">
                        <i class="fas fa-save"></i> Guardar Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection