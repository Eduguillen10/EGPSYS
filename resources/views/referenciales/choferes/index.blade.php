@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Choferes <a href="{{ url('referenciales/choferes/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.choferes.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>CI</th>
                        <th>RUC</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($choferes as $chofer)
                        <tr>
                            <td>{{ $chofer->idchofer }}</td>
                            <td>{{ $chofer->nombre }}</td>
                            <td>{{ $chofer->apellido }}</td>
                            <td>{{ $chofer->ci }}</td>
                            <td>{{ $chofer->ruc }}</td>
                            <td>{{ $chofer->direccion }}</td>
                            <td>{{ $chofer->telefono }}</td>
                            <td>{{ $chofer->email }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $chofer->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ url('referenciales/choferes/'.$chofer->idchofer.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $chofer->idchofer }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.choferes.modal')
                    @endforeach
                </table>
            </div>
            {{ $choferes->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
