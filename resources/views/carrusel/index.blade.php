@extends('layouts.admin')

@section('title', 'Administrar Carrusel - SICS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Administrar Carrusel</h2>
            <p class="text-muted">Gestiona las imágenes que aparecen en el carrusel de la página principal.</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('carrusel.create') }}" class="btn btn-tinto">
                <i class="fas fa-plus"></i> Agregar Imagen
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-tinto text-white">
            <h5 class="mb-0">Imágenes del Carrusel</h5>
        </div>
        <div class="card-body">
            @if($carruseles->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <p>No hay imágenes en el carrusel. ¡Agrega la primera!</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>URL</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carruseles as $item)
                        <tr>
                            <td>{{ $item->orden }}</td>
                            <td>
                                <img src="{{ Storage::url($item->imagen) }}" alt="{{ $item->titulo }}"
                                    style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">
                            </td>
                            <td>{{ $item->titulo }}</td>
                            <td>
                                @if($item->url)
                                <a href="{{ $item->url }}" target="_blank" class="text-truncate"
                                    style="max-width: 200px; display: inline-block;">
                                    {{ $item->url }}
                                </a>
                                @else
                                <span class="text-muted">Sin enlace</span>
                                @endif
                            </td>
                            <td>
                                @if($item->activo)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('carrusel.edit', $item) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('carrusel.toggle-status', $item) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-toggle-{{ $item->activo ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('carrusel.destroy', $item) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection