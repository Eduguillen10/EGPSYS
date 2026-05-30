@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Transportista {{ $transportista->nombre }}</h3>
        <p><b>Documento:</b> {{ $transportista->documento ?? '-' }}</p>
        <p><b>Direccion:</b> {{ $transportista->direccion ?? '-' }}</p>
        <p><b>Telefono:</b> {{ $transportista->telefono ?? '-' }}</p>
        <p><b>Email:</b> {{ $transportista->email ?? '-' }}</p>
        <p><b>Estado:</b> @include('referenciales.partials.estado', ['estado' => $transportista->estado ?? 'Activo'])</p>
        <a href="{{ url('referenciales/transportistas') }}" class="btn btn-default">Volver</a>
    </div>
</div>
@endsection
