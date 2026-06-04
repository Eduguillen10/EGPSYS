@extends('layouts.admin')
@section('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Tipo Ajuste</h3>
        <div class="form-group">
            <label>Descripcion</label>
            <p>{{ $tipo->descripcion }}</p>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <p>{{ $tipo->estado }}</p>
        </div>
        <a href="{{ url('referenciales/tipo_ajuste') }}" class="btn btn-default">Volver</a>
    </div>
</div>
@endsection
