@extends('layouts.app')

@section('title', 'Denuncias - SICS')

@section('content')
<div class="container  mt-4">
    <div class="text-center">
        <p>
            <a href="https://sidec.zacatecas.gob.mx/" target="_blank"><img src="{{ asset('storage/imgs/sidec.jpg') }}"
                    alt="Sistema de Atención Ciudadana" width="100%" class="rounded shadow bg-body-tertiary"></a>
        </p>
        <p>
        <h5 class="alert alert-primary">El Sistema de Denuncia Ciudadana (SIDEC) del Estado de Zacatecas, es el medio
            para que cualquier ciudadano,
            pueda denunciar principalmente actos de corrupción que involucren a servidores públicos.</h5>
        </p>
    </div>
</div>
@endsection

@section('scripts')

@endsection