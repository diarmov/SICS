@extends('layouts.admin')

@section('title', 'Dependencias - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Gestión de Dependencias</h2>
                    <a href="{{ route('dependencias.create') }}" class="btn btn-tinto">Nueva Dependencia</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Dependencia</th>
                                    <th>Siglas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dependencias as $dependencia)
                                <tr>
                                    <td>{{ $dependencia->dependencia }}</td>
                                    <td>{{ $dependencia->siglas }}</td>
                                    <td>
                                        <span class="badge {{ $dependencia->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $dependencia->activo ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('dependencias.edit', $dependencia) }}"
                                                class="btn btn-primary btn-sm">Editar</a>
                                            <form action="{{ route('dependencias.destroy', $dependencia) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($dependencias->isEmpty())
                    <div class="alert alert-info">
                        No hay dependencias registradas.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection