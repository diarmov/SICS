@extends('layouts.admin')

@section('title', 'Comités de Vigilancia - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Gestión de Comités de Vigilancia</h2>
                    <a href="{{ route('comites.create') }}" class="btn btn-tinto">Nuevo Comité</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dependencia</th>
                                    <th>Programa</th>
                                    <th>Estado</th>
                                    <th>Municipio</th>
                                    <th>Localidad</th>
                                    <th>Elementos</th>
                                    <th>Con INE</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comites as $comite)
                                @php
                                $elementosConINE = $comite->elementos->where('archivo_ine', '!=', null)->count();
                                @endphp
                                <tr>
                                    <td>{{ $comite->nombre }}</td>
                                    <td>{{ $comite->dependencia->siglas }}</td>
                                    <td>{{ $comite->programa->nombre }}</td>
                                    <td>{{ $comite->estado->nombre ?? 'N/A' }}</td>
                                    <td>{{ $comite->municipio->nombre ?? 'N/A' }}</td>
                                    <td>{{ $comite->localidad->nombre ?? 'N/A' }}</td>
                                    <td>{{ $comite->elementos->count() }}</td>
                                    <td>
                                        <span class="badge {{ $elementosConINE > 0 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $elementosConINE }}/{{ $comite->elementos->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $comite->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $comite->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comites.show', $comite) }}" class="btn btn-info btn-sm"
                                                title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comites.edit', $comite) }}"
                                                class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('comites.destroy', $comite) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Estás seguro?')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($comites->isEmpty())
                    <div class="alert alert-info">
                        No hay comités de vigilancia registrados.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregar Font Awesome para los íconos si no los tienes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection