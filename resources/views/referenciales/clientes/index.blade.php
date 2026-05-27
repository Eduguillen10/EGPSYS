@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Clientes <a href="{{ url('referenciales/clientes/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.clientes.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>ID</th>
                        <th>Clasificación</th>
                        <th>Nombre</th>
                        <th>Tipo Documento</th>
                        <th>Número de Documento</th>
                        <th>Nacionalidad</th>
                        <th>Ciudad</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Total Ventas (Gs.)</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->idcliente }}</td>
                            <td>
                                <strong>{{ $cliente->clasificacion }}</strong> 
                                @if($cliente->modo_clasificacion == 'manual')
                                    <span class="badge badge-info">(Manual)</span>
                                @endif
                            </td>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->tipo_documento }}</td>
                            <td>{{ $cliente->num_documento }}</td>
                            <td>{{ $cliente->nacionalidad }}</td>
                            <td>{{ $cliente->ciudad }}</td>
                            <td>{{ $cliente->direccion }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ number_format($cliente->total_ventas, 0, ',', '.') }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $cliente->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ url('referenciales/clientes/'.$cliente->idcliente.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $cliente->idcliente }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.clientes.modal')
                    @endforeach
                </table>
            </div>
            {{ $clientes->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
