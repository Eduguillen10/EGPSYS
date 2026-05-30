@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Destinatario {{ $destinatario->nombre }}</h3>
        <p><b>Documento:</b> {{ $destinatario->documento ?? '-' }}</p>
        <p><b>Direccion:</b> {{ $destinatario->direccion ?? '-' }}</p>
        <p><b>Telefono:</b> {{ $destinatario->telefono ?? '-' }}</p>
        <p><b>Email:</b> {{ $destinatario->email ?? '-' }}</p>
        <p><b>Estado:</b> @include('referenciales.partials.estado', ['estado' => $destinatario->estado ?? 'Activo'])</p>
        <a href="{{ url('referenciales/destinatarios_remision') }}" class="btn btn-default">Volver</a>
    </div>
</div>
@endsection
