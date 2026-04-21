<div class="small">
    <div class="row">
        <div class="col-6">
            <div class="border rounded p-2 mb-2">
                <small class="text-muted">Total Comités</small>
                <h5 class="mb-0">{{ number_format($estadisticas['total_comites']) }}</h5>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 mb-2">
                <small class="text-muted">Beneficiarios</small>
                <h5 class="mb-0">{{ number_format($estadisticas['total_beneficiarios']) }}</h5>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 mb-2">
                <small class="text-muted">Monto Vigilado</small>
                <h5 class="mb-0">${{ number_format($estadisticas['total_monto_vigilado'], 2) }}</h5>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 mb-2">
                <small class="text-muted">Material Difusión</small>
                <h5 class="mb-0">{{ number_format($estadisticas['total_material_difusion']) }}</h5>
            </div>
        </div>
    </div>
    <div class="progress mt-2">
        <div class="progress-bar bg-success"
            style="width: {{ $estadisticas['total_comites'] > 0 ? ($estadisticas['total_validados'] / $estadisticas['total_comites']) * 100 : 0 }}%">
            Validados: {{ $estadisticas['total_validados'] }}
        </div>
        <div class="progress-bar bg-warning"
            style="width: {{ $estadisticas['total_comites'] > 0 ? ($estadisticas['total_pendientes'] / $estadisticas['total_comites']) * 100 : 0 }}%">
            Pendientes: {{ $estadisticas['total_pendientes'] }}
        </div>
    </div>
</div>