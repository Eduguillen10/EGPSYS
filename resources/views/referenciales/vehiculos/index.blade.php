@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Vehículos <a href="vehiculos/create"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.vehiculos.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>ID</th>
                        <th>Número de Chapa</th>
                        <th>Color</th>
                        <th>Chasis</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($vehiculos as $vehiculo)
                    <tr>
                        <td>{{ $vehiculo->idvehiculo }}</td>
                        <td>{{ $vehiculo->nrochapa }}</td>
                        <td>{{ $vehiculo->color }}</td>
                        <td>{{ $vehiculo->chasis }}</td>
                        <td>{{ $vehiculo->modelo }}</td>
                        <td>@include('referenciales.partials.estado', ['estado' => $vehiculo->estado ?? 'Activo'])</td>
                        <td>
                            <a href="{{ URL('referenciales/vehiculos/'.$vehiculo->idvehiculo.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $vehiculo->idvehiculo }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('referenciales.vehiculos.modal')
                    @endforeach
                </table>
            </div>
            {{ $vehiculos->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
