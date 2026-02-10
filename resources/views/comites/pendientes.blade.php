@extends('layouts.admin')

@section('title', 'Comités Pendientes de Validación - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Comités Pendientes de Validación</h2>
                    <a href="{{ route('comites.index') }}" class="btn btn-secondary">Ver Todos los Comités</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="card-body">
                    @if($comites->isEmpty())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ¡Excelente! No hay comités pendientes de validación.
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Hay {{ $comites->count() }} comité(s) pendiente(s)
                        de validación.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dependencia</th>
                                    <th>Programa</th>
                                    <th>Ubicación</th>
                                    <th>Elementos</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comites as $comite)
                                @php
                                $elementosConINE = $comite->elementos->where('archivo_ine', '!=', null)->count();
                                $totalElementos = $comite->elementos->count();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $comite->nombre }}</strong>
                                        @if($comite->archivo_minuta)
                                        <span class="badge bg-success ms-2" title="Tiene minuta">
                                            <i class="fas fa-file-pdf"></i>
                                        </span>
                                        @endif
                                        @if($comite->lista_asistencia)
                                        <span class="badge bg-info ms-2" title="Tiene lista de asistencia">
                                            <i class="fas fa-clipboard-list"></i>
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ $comite->dependencia->siglas }}</td>
                                    <td>{{ $comite->programa->nombre }}</td>
                                    <td>
                                        {{ $comite->municipio->nombre ?? 'N/A' }}, {{ $comite->estado->nombre ?? 'N/A'
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $elementosConINE == $totalElementos ? 'bg-success' : ($elementosConINE > 0 ? 'bg-warning' : 'bg-danger') }}"
                                            title="{{ $elementosConINE }} de {{ $totalElementos }} con INE">
                                            {{ $totalElementos }} elementos
                                        </span>
                                    </td>
                                    <td>{{ $comite->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comites.show', $comite) }}" class="btn btn-info btn-sm"
                                                title="Revisar">
                                                <i class="fas fa-search"></i> Revisar
                                            </a>
                                            <form action="{{ route('comites.validar', $comite) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('¿Estás seguro de validar este comité?')"
                                                    title="Validar comité">
                                                    <i class="fas fa-check-circle"></i> Validar
                                                </button>
                                            </form>
                                        </div>
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
    </div>
</div>
@endsection