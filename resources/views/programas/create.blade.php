@extends('layouts.admin')

@section('title', 'Crear Programa - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Crear Nuevo Programa</h4>
                </div>
                <div class="card-body">
                    <!-- Mostrar errores de validación generales -->
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error al guardar el programa</strong>
                        </h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('programas.store') }}" method="POST" enctype="multipart/form-data"
                        id="programaForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dependencia_id" class="form-label">Dependencia <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('dependencia_id') is-invalid @enderror"
                                        id="dependencia_id" name="dependencia_id" required>
                                        <option value="">Seleccionar dependencia</option>
                                        @foreach($dependencias as $dependencia)
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id')==$dependencia->
                                            id ? 'selected' : '' }}>
                                            {{ $dependencia->dependencia }} ({{ $dependencia->siglas }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('dependencia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Programa <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_apoyo_id" class="form-label">Tipo de Apoyo <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('tipo_apoyo_id') is-invalid @enderror"
                                        id="tipo_apoyo_id" name="tipo_apoyo_id" required>
                                        <option value="">Seleccionar tipo de apoyo</option>
                                        @foreach($tiposApoyo as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_apoyo_id')==$tipo->id ? 'selected'
                                            : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tipo_apoyo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_beneficiarios" class="form-label">Número de Beneficiarios <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('numero_beneficiarios') is-invalid @enderror"
                                        id="numero_beneficiarios" name="numero_beneficiarios"
                                        value="{{ old('numero_beneficiarios', 0) }}" min="0" required>
                                    @error('numero_beneficiarios')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monto_vigilado" class="form-label">Monto Vigilado ($) <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('monto_vigilado') is-invalid @enderror"
                                        id="monto_vigilado" name="monto_vigilado" value="{{ old('monto_vigilado', 0) }}"
                                        min="0" step="0.01" required>
                                    @error('monto_vigilado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio de ejecución <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                        id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                                        required>
                                    @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_termino" class="form-label">Fecha de Término de ejecución <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fecha_termino') is-invalid @enderror"
                                        id="fecha_termino" name="fecha_termino" value="{{ old('fecha_termino') }}"
                                        required>
                                    @error('fecha_termino')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="periodo" class="form-label">Periodo (Año) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('periodo') is-invalid @enderror"
                                        id="periodo" name="periodo" min="2000" max="2030"
                                        value="{{ old('periodo', date('Y')) }}" required>
                                    @error('periodo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_informes" class="form-label">Número de Informes a Entregar <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('numero_informes') is-invalid @enderror"
                                        id="numero_informes" name="numero_informes" min="0" max="12"
                                        value="{{ old('numero_informes', 0) }}" required>
                                    <small class="form-text text-muted">Número máximo de informes que se deben entregar
                                        para este programa.</small>
                                    @error('numero_informes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Archivos PDF con validación en tiempo real -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="archivo_pdf" class="form-label">Programa de trabajo en materia de
                                        Contraloría Social (PDF)</label>
                                    <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror"
                                        id="archivo_pdf" name="archivo_pdf" accept=".pdf"
                                        onchange="validarPDF(this, 'archivo_pdf_error')">
                                    <small class="form-text text-muted">Tamaño máximo: 10MB. Solo archivos PDF.</small>
                                    <div id="archivo_pdf_error" class="invalid-feedback"></div>
                                    @error('archivo_pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reglas_operacion_pdf" class="form-label">Reglas de Operación
                                        (PDF)</label>
                                    <input type="file"
                                        class="form-control @error('reglas_operacion_pdf') is-invalid @enderror"
                                        id="reglas_operacion_pdf" name="reglas_operacion_pdf" accept=".pdf"
                                        onchange="validarPDF(this, 'reglas_operacion_pdf_error')">
                                    <small class="form-text text-muted">Archivo PDF de las reglas de operación. Tamaño
                                        máximo: 10MB.</small>
                                    <div id="reglas_operacion_pdf_error" class="invalid-feedback"></div>
                                    @error('reglas_operacion_pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="guia_operativa_pdf" class="form-label">Guía Operativa (PDF)</label>
                                    <input type="file"
                                        class="form-control @error('guia_operativa_pdf') is-invalid @enderror"
                                        id="guia_operativa_pdf" name="guia_operativa_pdf" accept=".pdf"
                                        onchange="validarPDF(this, 'guia_operativa_pdf_error')">
                                    <small class="form-text text-muted">Archivo PDF de la guía operativa. Tamaño máximo:
                                        10MB.</small>
                                    <div id="guia_operativa_pdf_error" class="invalid-feedback"></div>
                                    @error('guia_operativa_pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Importante:</strong> La guía operativa deberá ser validada por el <b>Órgano Estatal
                                de
                                Control</b> para que el programa quede activo.
                            Hasta que no sea validada, el programa permanecerá en estado "Pendiente".
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                                old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Programa Activo</label>
                            <small class="form-text text-muted text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Nota: El programa solo estará realmente activo después de que la guía operativa sea
                                validada.
                            </small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-tinto" id="btnSubmit">
                                <i class="fas fa-save"></i> Guardar Programa
                            </button>
                            <a href="{{ route('programas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para validar PDF en tiempo real
function validarPDF(input, errorDivId) {
    const errorDiv = document.getElementById(errorDivId);
    const maxSize = 10 * 1024 * 1024; // 10MB en bytes

    // Limpiar errores previos
    errorDiv.innerHTML = '';
    input.classList.remove('is-invalid');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = file.size;
        const fileType = file.type;

        // Validar extensión
        const extension = fileName.split('.').pop().toLowerCase();
        if (extension !== 'pdf') {
            errorDiv.innerHTML = `❌ El archivo debe ser PDF. Extensión detectada: .${extension}`;
            input.classList.add('is-invalid');
            return false;
        }

        // Validar tipo MIME
        if (fileType !== 'application/pdf' && fileType !== 'application/x-pdf') {
            errorDiv.innerHTML = `❌ El archivo no es un PDF válido. Tipo detectado: ${fileType}`;
            input.classList.add('is-invalid');
            return false;
        }

        // Validar tamaño
        if (fileSize > maxSize) {
            const sizeMB = (fileSize / (1024 * 1024)).toFixed(2);
            errorDiv.innerHTML = `❌ El archivo excede el tamaño máximo de 10MB. Tamaño actual: ${sizeMB}MB`;
            input.classList.add('is-invalid');
            return false;
        }

        // Validar que el archivo no esté corrupto (verificación básica)
        const reader = new FileReader();
        reader.onload = function(e) {
            // Verificar los primeros bytes para PDF (%PDF)
            const header = new Uint8Array(e.target.result.slice(0, 4));
            const pdfHeader = [37, 80, 68, 70]; // %PDF en ASCII
            let isValidPDF = true;

            for (let i = 0; i < pdfHeader.length; i++) {
                if (header[i] !== pdfHeader[i]) {
                    isValidPDF = false;
                    break;
                }
            }

            if (!isValidPDF) {
                errorDiv.innerHTML = '❌ El archivo no es un PDF válido o está corrupto.';
                input.classList.add('is-invalid');
            }
        };
        reader.readAsArrayBuffer(file.slice(0, 4));

        return true;
    }

    return true;
}

// Validar todos los archivos antes de enviar el formulario
document.getElementById('programaForm').addEventListener('submit', function(e) {
    const archivos = [
        { input: document.getElementById('archivo_pdf'), errorId: 'archivo_pdf_error' },
        { input: document.getElementById('reglas_operacion_pdf'), errorId: 'reglas_operacion_pdf_error' },
        { input: document.getElementById('guia_operativa_pdf'), errorId: 'guia_operativa_pdf_error' }
    ];

    let isValid = true;

    archivos.forEach(item => {
        if (item.input.files && item.input.files[0]) {
            if (!validarPDF(item.input, item.errorId)) {
                isValid = false;
            }
        }
    });

    if (!isValid) {
        e.preventDefault();
        // Scroll al primer error
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Mostrar mensaje de error general
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            html: 'Por favor, corrige los errores en los archivos PDF antes de continuar.',
            confirmButtonText: 'Entendido'
        });
    }
});
</script>

@endsection