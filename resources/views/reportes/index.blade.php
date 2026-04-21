{{-- resources/views/reportes/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Generar Reportes - SICS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Generador de Reportes
                    </h4>
                </div>
                <div class="card-body">
                    <form id="reporteForm" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">Filtros de Búsqueda</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Dependencia -->
                                        <div class="mb-3">
                                            <label for="dependencia_id" class="form-label">
                                                <i class="fas fa-building"></i> Dependencia
                                            </label>
                                            <select name="dependencia_id" id="dependencia_id" class="form-select">
                                                <option value="">Todas las dependencias</option>
                                                @foreach($dependencias as $dependencia)
                                                <option value="{{ $dependencia->id }}">{{ $dependencia->dependencia }}
                                                    ({{ $dependencia->siglas }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Programa -->
                                        <div class="mb-3">
                                            <label for="programa_id" class="form-label">
                                                <i class="fas fa-folder-open"></i> Programa
                                            </label>
                                            <select name="programa_id" id="programa_id" class="form-select">
                                                <option value="">Todos los programas</option>
                                                @foreach($programas as $programa)
                                                <option value="{{ $programa->id }}">{{ $programa->nombre }} ({{
                                                    $programa->dependencia->siglas ?? 'N/A' }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Comité de Vigilancia -->
                                        <div class="mb-3">
                                            <label for="comite_id" class="form-label">
                                                <i class="fas fa-users"></i> Comité de Vigilancia
                                            </label>
                                            <select name="comite_id" id="comite_id" class="form-select">
                                                <option value="">Todos los comités</option>
                                            </select>
                                            <small class="text-muted">Selecciona dependencia y/o programa para cargar
                                                comités</small>
                                        </div>

                                        <!-- Estado de Validación -->
                                        <div class="mb-3">
                                            <label for="estado_validacion" class="form-label">
                                                <i class="fas fa-check-circle"></i> Estado de Validación
                                            </label>
                                            <select name="estado_validacion" id="estado_validacion" class="form-select">
                                                <option value="todos">Todos</option>
                                                <option value="validados">Solo Validados</option>
                                                <option value="pendientes">Solo Pendientes</option>
                                            </select>
                                        </div>

                                        <!-- Rango de Fechas -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="fecha_inicio" class="form-label">
                                                    <i class="fas fa-calendar-alt"></i> Fecha Inicio
                                                </label>
                                                <input type="date" name="fecha_inicio" id="fecha_inicio"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="fecha_fin" class="form-label">
                                                    <i class="fas fa-calendar-alt"></i> Fecha Fin
                                                </label>
                                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">Información del Reporte</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Información incluida en el reporte:</strong>
                                            <ul class="mt-2 mb-0">
                                                <li>Listado completo de comités según filtros</li>
                                                <li>Total de comités de vigilancia</li>
                                                <li>Total de personas beneficiadas</li>
                                                <li>Total de monto vigilado</li>
                                                <li>Conteo por tipo de apoyo</li>
                                                <li>Materiales de difusión por comité</li>
                                                <li>Detalle de elementos por comité</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="fas fa-clock"></i>
                                            <strong>Tiempo de generación:</strong>
                                            <span id="tiempoEstimado">Dependiendo de la cantidad de datos</span>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-6">
                                                <button type="submit" formaction="{{ route('reportes.pdf') }}"
                                                    class="btn btn-danger w-100" id="btnPDF">
                                                    <i class="fas fa-file-pdf"></i> Generar PDF
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" formaction="{{ route('reportes.excel') }}"
                                                    class="btn btn-success w-100" id="btnExcel">
                                                    <i class="fas fa-file-excel"></i> Generar Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vista previa rápida -->
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">Vista Previa de Datos</h5>
                                    </div>
                                    <div class="card-body" id="vistaPrevia">
                                        <p class="text-muted text-center">
                                            <i class="fas fa-chart-line fa-2x"></i><br>
                                            Selecciona filtros y genera un reporte para ver estadísticas
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    var generando = false;

    function cargarComites() {
        var dependenciaId = $('#dependencia_id').val();
        var programaId = $('#programa_id').val();

        if (dependenciaId || programaId) {
            $.ajax({
                url: '{{ route("reportes.comites") }}',
                type: 'GET',
                data: {
                    dependencia_id: dependenciaId,
                    programa_id: programaId
                },
                success: function(data) {
                    var $comiteSelect = $('#comite_id');
                    $comiteSelect.empty();
                    $comiteSelect.append('<option value="">Todos los comités</option>');

                    $.each(data, function(index, comite) {
                        $comiteSelect.append('<option value="' + comite.id + '">' + comite.nombre + '</option>');
                    });
                }
            });
        }
    }

    // Generar PDF vía AJAX
    $('#btnPDF').on('click', function(e) {
        e.preventDefault();

        if (generando) {
            alert('Por favor espera a que termine la generación actual');
            return;
        }

        generando = true;
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando PDF...');

        var formData = $('#reporteForm').serialize();

        $.ajax({
            url: '{{ route("reportes.pdf") }}',
            type: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Crear link de descarga
                var blob = new Blob([data], {type: 'application/pdf'});
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                link.href = url;
                link.download = 'reporte_comites_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                // Restaurar botón
                $btn.prop('disabled', false).html(originalText);
                generando = false;
            },
            error: function(xhr) {
                alert('Error al generar el PDF: ' + xhr.statusText);
                $btn.prop('disabled', false).html(originalText);
                generando = false;
            }
        });
    });

    // Generar Excel vía AJAX
    $('#btnExcel').on('click', function(e) {
        e.preventDefault();

        if (generando) {
            alert('Por favor espera a que termine la generación actual');
            return;
        }

        generando = true;
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando Excel...');

        var formData = $('#reporteForm').serialize();

        $.ajax({
            url: '{{ route("reportes.excel") }}',
            type: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Determinar el tipo de contenido
                var contentType = xhr.getResponseHeader('Content-Type');
                var extension = 'xlsx';

                if (contentType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    extension = 'xlsx';
                } else if (contentType === 'application/vnd.ms-excel') {
                    extension = 'xls';
                }

                // Crear link de descarga
                var blob = new Blob([data], {type: contentType});
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                link.href = url;
                link.download = 'reporte_comites_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.' + extension;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                // Restaurar botón
                $btn.prop('disabled', false).html(originalText);
                generando = false;
            },
            error: function(xhr) {
                alert('Error al generar el Excel: ' + xhr.statusText);
                $btn.prop('disabled', false).html(originalText);
                generando = false;
            }
        });
    });

    $('#dependencia_id').change(cargarComites);
    $('#programa_id').change(cargarComites);

    // Vista previa
    function cargarVistaPrevia() {
        var filtros = {
            dependencia_id: $('#dependencia_id').val(),
            programa_id: $('#programa_id').val(),
            comite_id: $('#comite_id').val(),
            estado_validacion: $('#estado_validacion').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val()
        };

        $.ajax({
            url: '{{ route("reportes.preview") }}',
            type: 'GET',
            data: filtros,
            success: function(data) {
                $('#vistaPrevia').html(data);
            },
            error: function() {
                $('#vistaPrevia').html('<p class="text-danger text-center">Error al cargar vista previa</p>');
            }
        });
    }

    var timeout;
    $('#dependencia_id, #programa_id, #comite_id, #estado_validacion, #fecha_inicio, #fecha_fin').on('change', function() {
        clearTimeout(timeout);
        timeout = setTimeout(cargarVistaPrevia, 500);
    });

    cargarVistaPrevia();
});
</script>
@endsection