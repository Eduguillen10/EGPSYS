@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Proveedores <a href="{{ url('referenciales/proveedores/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.proveedores.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    {{-- Encabezados de la tabla --}}
                    <thead>
                        <th>ID</th>
                        <th>Razón Social</th>
                        <th>Ciudad</th>
                        <th>RUC</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>

                    {{-- Datos de la tabla --}}
                    @foreach ($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->idproveedor }}</td>
                            <td>{{ $proveedor->razonsocial }}</td>
                            <td>{{ $proveedor->ciudad }}</td>
                            <td>{{ $proveedor->ruc }}</td>
                            <td>{{ $proveedor->direccion }}</td>
                            <td>{{ $proveedor->telefono }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $proveedor->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ route('proveedores.edit', $proveedor->idproveedor) }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $proveedor->idproveedor }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.proveedores.modal')
                    @endforeach
                </table>
            </div>
            {{ $proveedores->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
