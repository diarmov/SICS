@extends('layouts.admin')

@section('title', 'Ver Comité - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $comite->nombre }}</h4>
                        @if($comite->estaValidado())
                        <small class="text-light">
                            <i class="fas fa-check-circle"></i>
                            Validado por {{ $comite->validador->name ?? 'Administrador' }}
                            el {{ optional($comite->fecha_validacion)->format('d/m/Y H:i') ?? 'Fecha no disponible' }}
                        </small>
                        @else
                        <small class="text-warning">
                            <i class="fas fa-clock"></i> Pendiente de validación
                        </small>
                        @endif
                    </div>
                    <div>
                        <!-- Botones de validación para SuperUsuario y AdministradorCS -->
                        @if(Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                        @if(!$comite->estaValidado())
                        <form action="{{ route('comites.validar', $comite) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                onclick="return confirm('¿Validar este comité? Esto confirmará que todos los documentos son correctos.')">
                                <i class="fas fa-check-circle"></i> Validar Comité
                            </button>
                        </form>
                        @else
                        <form action="{{ route('comites.invalidar', $comite) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm"
                                onclick="return confirm('¿Invalidar este comité? Esto marcará el comité como no validado.')">
                                <i class="fas fa-times-circle"></i> Invalidar
                            </button>
                        </form>
                        @endif
                        @endif

                        <a href="{{ route('comites.edit', $comite) }}" class="btn btn-light btn-sm">Editar</a>
                        <a href="{{ route('comites.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resumen de validación -->
                    <div class="alert {{ $comite->estaValidado() ? 'alert-success' : 'alert-warning' }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($comite->estaValidado())
                                <i class="fas fa-check-circle fa-2x"></i>
                                @else
                                <i class="fas fa-clock fa-2x"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-1">
                                    @if($comite->estaValidado())
                                    ✅ Comité Validado
                                    @else
                                    ⏳ Pendiente de Validación
                                    @endif
                                </h5>
                                <p class="mb-0">
                                    @if($comite->estaValidado())
                                    Este comité ha sido revisado y validado por un administrador.
                                    @else
                                    Este comité está pendiente de revisión y validación por un administrador.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Resto del contenido existente... -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-tinto">Información del Comité</h5>
                            <!-- ... contenido existente ... -->
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-tinto">Estado de Documentación</h5>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Minuta del Comité
                                    @if($comite->archivo_minuta)
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check"></i> Subido
                                    </span>
                                    @else
                                    <span class="badge bg-danger rounded-pill">
                                        <i class="fas fa-times"></i> Faltante
                                    </span>
                                    @endif
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Lista de Asistencia
                                    @if($comite->lista_asistencia)
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check"></i> Subido
                                    </span>
                                    @else
                                    <span class="badge bg-warning rounded-pill">
                                        <i class="fas fa-exclamation"></i> Opcional
                                    </span>
                                    @endif
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Material de Difusión
                                    @if(count($comite->material_difusion) > 0)
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check"></i> {{ count($comite->material_difusion) }} archivo(s)
                                    </span>
                                    @else
                                    <span class="badge bg-warning rounded-pill">
                                        <i class="fas fa-exclamation"></i> Opcional
                                    </span>
                                    @endif
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Fotografías de Reunión
                                    @if(count($comite->fotografias_reunion) > 0)
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check"></i> {{ count($comite->fotografias_reunion) }} foto(s)
                                    </span>
                                    @else
                                    <span class="badge bg-warning rounded-pill">
                                        <i class="fas fa-exclamation"></i> Opcional
                                    </span>
                                    @endif
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    INEs de Elementos
                                    @php
                                    $elementosConINE = $comite->elementos->where('archivo_ine', '!=', null)->count();
                                    $totalElementos = $comite->elementos->count();
                                    @endphp
                                    <span
                                        class="badge {{ $elementosConINE == $totalElementos ? 'bg-success' : ($elementosConINE > 0 ? 'bg-warning' : 'bg-danger') }} rounded-pill">
                                        {{ $elementosConINE }}/{{ $totalElementos }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection