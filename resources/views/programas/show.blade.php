@extends('layouts.admin')

@section('title', 'Ver Programa - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $programa->nombre }}</h4>
                    <div>
                        @if($programa->guia_operativa_pendiente && Auth::user()->hasRole(['SuperUsuario',
                        'Organo_Estatal_de_Control']))
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#validarGuiaModal">
                            <i class="fas fa-check-circle"></i> Validar Guía Operativa
                        </button>
                        @endif
                        <a href="{{ route('programas.edit', $programa) }}" class="btn btn-light btn-sm">Editar</a>
                        <a href="{{ route('programas.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-tinto">Información del Programa</h5>
                            <p><strong>Dependencia:</strong> {{ $programa->dependencia->dependencia }} ({{
                                $programa->dependencia->siglas }})</p>
                            <p><strong>Periodo:</strong> {{ $programa->periodo }}</p>
                            <p><strong>Fecha de Inicio de ejecución:</strong> {{
                                \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') }}</p>
                            <p><strong>Fecha de Término de ejecución:</strong> {{
                                \Carbon\Carbon::parse($programa->fecha_termino)->format('d/m/Y') }}</p>
                            <p><strong>Estado:</strong>
                                <span class="badge {{ $programa->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                                @if($programa->esta_activo)
                                <span class="badge bg-info">En Periodo</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-tinto">Archivos</h5>
                            @if($programa->archivo_pdf)
                            <p>
                                <strong>Programa PDF:</strong>
                                <a href="{{ asset('storage/' . $programa->archivo_pdf) }}" target="_blank"
                                    class="btn btn-outline-tinto btn-sm">
                                    Ver Programa
                                </a>
                            </p>
                            @else
                            <p class="text-muted">No hay archivo PDF cargado para este programa.</p>
                            @endif

                            @if($programa->reglas_operacion_pdf)
                            <p>
                                <strong>Reglas de Operación:</strong>
                                <a href="{{ asset('storage/' . $programa->reglas_operacion_pdf) }}" target="_blank"
                                    class="btn btn-outline-tinto btn-sm">
                                    Ver Reglas
                                </a>
                            </p>
                            @else
                            <p class="text-muted">No hay reglas de operación cargadas.</p>
                            @endif

                            <p>
                                <strong>Guía Operativa:</strong>
                                @if($programa->guia_operativa_pdf)
                                <a href="{{ asset('storage/' . $programa->guia_operativa_pdf) }}" target="_blank"
                                    class="btn btn-outline-tinto btn-sm">
                                    Ver Guía Operativa
                                </a>
                                <br>
                                <small class="text-muted">
                                    Estado:
                                    @if($programa->guia_operativa_validada)
                                    <span class="badge bg-success">Validada</span>
                                    <br>
                                    <strong>Validada por:</strong> {{ $programa->validador->name ?? 'N/A' }}<br>
                                    <strong>Fecha:</strong> {{ $programa->guia_operativa_fecha_validacion ?
                                    $programa->guia_operativa_fecha_validacion->format('d/m/Y H:i') : 'N/A' }}
                                    @elseif($programa->guia_operativa_observaciones)
                                    <span class="badge bg-danger">Observada</span>
                                    <br>
                                    <strong>Observaciones:</strong> {{ $programa->guia_operativa_observaciones }}
                                    <br>
                                    @if(Auth::user()->dependencia_id == $programa->dependencia_id ||
                                    Auth::user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control']))
                                    <button type="button" class="btn btn-warning btn-sm mt-2" data-bs-toggle="modal"
                                        data-bs-target="#editarGuiaModal">
                                        <i class="fas fa-edit"></i> Editar Guía Operativa
                                    </button>
                                    @endif
                                    @else
                                    <span class="badge bg-warning">Pendiente de validación</span>
                                    @endif
                                </small>
                                @else
                                <span class="text-muted">No hay guía operativa cargada.</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Después de la sección de archivos, agregar: -->
                    <div
                        class="alert {{ $programa->activo ? 'alert-success' : ($programa->guia_operativa_validada ? 'alert-warning' : 'alert-info') }} mt-3">
                        <i
                            class="fas {{ $programa->activo ? 'fa-check-circle' : ($programa->guia_operativa_validada ? 'fa-clock' : 'fa-hourglass-half') }}"></i>
                        <strong>Estado del Programa:</strong>
                        @if($programa->activo)
                        ✅ El programa está ACTIVO y puede operar normalmente.
                        @elseif($programa->guia_operativa_validada)
                        ⏳ La guía operativa está validada pero el programa está desactivado manualmente.
                        @elseif($programa->guia_operativa_observaciones)
                        ❌ La guía operativa fue observada. El programa no puede activarse hasta que se corrija.
                        @else
                        ⏰ El programa está pendiente de validación de la guía operativa. No estará activo hasta que sea
                        validado.
                        @endif
                    </div>
                    <!-- Información adicional del programa -->
                    <div class="mt-4">
                        <h5 class="text-tinto">Comités de Vigilancia Asociados</h5>
                        @if($programa->comitesVigilancia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    32<td>
                                    <th>Nombre del Comité</th>
                                    <th>Elementos</th>
                                    <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programa->comitesVigilancia as $comite)
                                    <tr>
                                        <td>{{ $comite->nombre }}</td>
                                        <td>{{ $comite->elementos->count() }}</td>
                                        <td>
                                            <span class="badge {{ $comite->activo ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $comite->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            No hay comités de vigilancia asociados a este programa.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para validar guía operativa -->
<div class="modal fade" id="validarGuiaModal" tabindex="-1" aria-labelledby="validarGuiaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('programas.validar-guia', $programa) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="validarGuiaModalLabel">
                        <i class="fas fa-check-circle"></i> Validar Guía Operativa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Decisión de Validación</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="validar" id="validarSi" value="1"
                                checked>
                            <label class="form-check-label" for="validarSi">
                                <span class="text-success">✓ Validar guía operativa</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="validar" id="validarNo" value="0">
                            <label class="form-check-label" for="validarNo">
                                <span class="text-danger">✗ Observar guía operativa</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="observacionesGroup" style="display: none;">
                        <label for="observaciones" class="form-label">Observaciones (requerido si se observa)</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                            placeholder="Indique los motivos de la observación y las correcciones necesarias..."></textarea>
                        <small class="form-text text-muted">Estas observaciones serán visibles para el creador del
                            programa.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Decisión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar guía operativa -->
<div class="modal fade" id="editarGuiaModal" tabindex="-1" aria-labelledby="editarGuiaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('programas.editar-guia', $programa) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editarGuiaModalLabel">
                        <i class="fas fa-edit"></i> Editar Guía Operativa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="guia_operativa_pdf" class="form-label">Nueva Guía Operativa (PDF)</label>
                        <input type="file" class="form-control" id="guia_operativa_pdf" name="guia_operativa_pdf"
                            accept=".pdf" required>
                        <small class="form-text text-muted">Tamaño máximo: 10MB. Solo archivos PDF.</small>
                    </div>

                    <div class="mb-3">
                        <label for="guia_operativa_observaciones" class="form-label">Comentarios sobre las correcciones
                            realizadas</label>
                        <textarea class="form-control" id="guia_operativa_observaciones"
                            name="guia_operativa_observaciones" rows="3"
                            placeholder="Describa las correcciones realizadas a la guía operativa...">{{ old('guia_operativa_observaciones', $programa->guia_operativa_observaciones) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Guía</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar campo de observaciones según selección
    const radioSi = document.getElementById('validarSi');
    const radioNo = document.getElementById('validarNo');
    const observacionesGroup = document.getElementById('observacionesGroup');

    function toggleObservaciones() {
        if (radioNo.checked) {
            observacionesGroup.style.display = 'block';
        } else {
            observacionesGroup.style.display = 'none';
        }
    }

    radioSi.addEventListener('change', toggleObservaciones);
    radioNo.addEventListener('change', toggleObservaciones);
    toggleObservaciones();
});
</script>

@endsection