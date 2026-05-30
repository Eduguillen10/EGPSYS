@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <h3>Listado de Destinatarios de Remision <a href="{{ url('referenciales/destinatarios_remision/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.destinatarios_remision.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($destinatarios as $destinatario)
                    <tr>
                        <td>{{ $destinatario->iddestinatario_remision }}</td>
                        <td>{{ $destinatario->nombre }}</td>
                        <td>{{ $destinatario->documento ?? '-' }}</td>
                        <td>{{ $destinatario->direccion ?? '-' }}</td>
                        <td>{{ $destinatario->telefono ?? '-' }}</td>
                        <td>{{ $destinatario->email ?? '-' }}</td>
                        <td>@include('referenciales.partials.estado', ['estado' => $destinatario->estado ?? 'Activo'])</td>
                        <td>
                            <a href="{{ url('referenciales/destinatarios_remision/'.$destinatario->iddestinatario_remision.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $destinatario->iddestinatario_remision }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('referenciales.destinatarios_remision.modal')
                @endforeach
            </table>
        </div>
        {{ $destinatarios->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
