@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Sucursales <a href="{{ URL('referenciales/sucursales/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.sucursales.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Empresa</th>
                        <th>Nombre de la Sucursal</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($sucursales as $sucursal)
                        <tr>
                            <td>{{ $sucursal->idsucursal }}</td>
                            <td>{{ $sucursal->empresa_descripcion }}</td>
                            <td>{{ $sucursal->descripcion }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $sucursal->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ URL('referenciales/sucursales/'.$sucursal->idsucursal.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $sucursal->idsucursal }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.sucursales.modal')
                    @endforeach
                </table>
            </div>
            {{ $sucursales->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
