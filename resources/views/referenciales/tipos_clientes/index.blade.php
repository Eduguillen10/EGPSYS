@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Tipos de Cliente <a href="tipos_clientes/create"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.tipos_clientes.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Descripción del Tipo de Cliente</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($tiposClientes as $tipoCliente)
                        <tr>
                            <td>{{ $tipoCliente->idtipo_cliente }}</td>
                            <td>{{ $tipoCliente->descripcion }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $tipoCliente->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ URL('referenciales/tipos_clientes/'.$tipoCliente->idtipo_cliente.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $tipoCliente->idtipo_cliente }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.tipos_clientes.modal')
                    @endforeach
                </table>
            </div>
            {{ $tiposClientes->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
