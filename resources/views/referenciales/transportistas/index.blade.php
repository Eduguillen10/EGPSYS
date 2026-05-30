@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <h3>Listado de Transportistas <a href="{{ url('referenciales/transportistas/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.transportistas.search')
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
                @foreach ($transportistas as $transportista)
                    <tr>
                        <td>{{ $transportista->idtransportista }}</td>
                        <td>{{ $transportista->nombre }}</td>
                        <td>{{ $transportista->documento ?? '-' }}</td>
                        <td>{{ $transportista->direccion ?? '-' }}</td>
                        <td>{{ $transportista->telefono ?? '-' }}</td>
                        <td>{{ $transportista->email ?? '-' }}</td>
                        <td>@include('referenciales.partials.estado', ['estado' => $transportista->estado ?? 'Activo'])</td>
                        <td>
                            <a href="{{ url('referenciales/transportistas/'.$transportista->idtransportista.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $transportista->idtransportista }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('referenciales.transportistas.modal')
                @endforeach
            </table>
        </div>
        {{ $transportistas->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
