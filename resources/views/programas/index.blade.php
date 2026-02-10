@extends('layouts.admin')

@section('title', 'Programas - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Gestión de Programas</h2>
                    <a href="{{ route('programas.create') }}" class="btn btn-tinto">Nuevo Programa</a>
                </div>

                <!-- ALERTAS DE ÉXITO/ERROR -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-ban"></i> No se puede eliminar
                            </h6>
                            <p class="mb-2">{{ session('error') }}</p>
                            <hr>
                            <p class="mb-0 small">
                                <strong><i class="fas fa-lightbulb"></i> Solución:</strong>
                                Para eliminar este programa, primero debe eliminar todos los comités de vigilancia
                                asociados.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dependencia</th>
                                    <th>Periodo</th>
                                    <th>Tipo de Apoyo</th>
                                    <th>Beneficiarios</th>
                                    <th>Monto Vigilado</th>
                                    <th>Informes</th>
                                    {{-- <th>Comités</th> --}}
                                    <th>Inicio</th>
                                    <th>Término</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programas as $programa)
                                <tr>
                                    <td>{{ $programa->nombre }}</td>
                                    <td>{{ $programa->dependencia->siglas }}</td>
                                    <td>{{ $programa->periodo }}</td>
                                    <td>{{ $programa->tipoApoyo->nombre ?? 'N/A' }}</td>
                                    <td>{{ number_format($programa->numero_beneficiarios) }}</td>
                                    <td>${{ number_format($programa->monto_vigilado, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $programa->informes->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $programa->informes->count() }}/{{ $programa->numero_informes }}
                                        </span>
                                    </td>
                                    {{-- <td>
                                        @if($programa->comitesVigilancia->count() > 0)
                                        <span class="badge bg-warning"
                                            title="{{ $programa->comitesVigilancia->count() }} comité(s) de vigilancia">
                                            <i class="fas fa-users"></i> {{ $programa->comitesVigilancia->count() }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Sin comités</span>
                                        @endif
                                    </td> --}}
                                    <td>{{ \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($programa->fecha_termino)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $programa->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        @if($programa->esta_activo)
                                        <span class="badge bg-info">En Periodo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('programas.show', $programa) }}"
                                                class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('programas.informes', $programa) }}"
                                                class="btn btn-warning btn-sm" title="Informes">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <a href="{{ route('programas.edit', $programa) }}"
                                                class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Formulario de eliminación con confirmación -->
                                            @if($programa->comitesVigilancia->count() == 0 &&
                                            $programa->informes->count() == 0)
                                            <form action="{{ route('programas.destroy', $programa) }}" method="POST"
                                                class="d-inline delete-form" data-nombre="{{ $programa->nombre }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirmDelete(this);" title="Eliminar programa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <button type="button" class="btn btn-danger btn-sm disabled"
                                                title="No se puede eliminar (tiene elementos asociados)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($programas->isEmpty())
                    <div class="alert alert-info">
                        No hay programas registrados.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar el programa <strong id="programaNombre"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Función para mostrar modal de confirmación
    function confirmDelete(button) {
        const form = button.closest('form');
        const nombre = form.getAttribute('data-nombre');

        // Mostrar modal de confirmación
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        document.getElementById('programaNombre').textContent = nombre;

        // Configurar evento del botón de confirmación
        document.getElementById('confirmDeleteBtn').onclick = function() {
            form.submit();
        };

        modal.show();
        return false; // Prevenir el comportamiento por defecto
    }

    // Mostrar alertas de SweetAlert2 para mensajes de sesión
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'warning',
            title: 'No se puede eliminar',
            html: `{{ session('error') }}<br><br>
                  <small><strong><i class="fas fa-lightbulb"></i> Solución:</strong>
                  Para eliminar este programa, primero debe eliminar todos los comités de vigilancia e informes asociados.</small>`,
            showConfirmButton: true,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6'
        });
        @endif

        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- CSS adicional para mejorar la apariencia -->
<style>
    .alert {
        border-left: 4px solid transparent;
    }

    .alert-success {
        border-left-color: #28a745;
    }

    .alert-danger {
        border-left-color: #dc3545;
    }

    .btn-group .btn {
        margin-right: 3px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }

    .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection