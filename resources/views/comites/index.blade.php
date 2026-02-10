@extends('layouts.admin')

@section('title', 'Comités de Vigilancia - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Gestión de Comités de Vigilancia</h2>
                    <div>
                        {{-- @if(Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                        <a href="{{ route('comites.pendientes') }}" class="btn btn-warning me-2">
                            <i class="fas fa-clock"></i> Pendientes de Validación
                        </a>
                        @endif --}}
                        <a href="{{ route('comites.create') }}" class="btn btn-tinto">Nuevo Comité</a>
                    </div>
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
                                    <th>Validación</th> <!-- Nueva columna -->
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
                                        @if($comite->estaValidado())
                                        <span class="badge bg-success"
                                            title="Validado por {{ $comite->validador->name ?? 'Administrador' }} el {{ $comite->fecha_validacion->format('d/m/Y H:i') }}">
                                            <i class="fas fa-check-circle"></i> Validado
                                        </span>
                                        @else
                                        <span class="badge bg-warning" title="Pendiente de validación">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                        @endif
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

@endsection