@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Empleados <a href="{{ url('referenciales/empleados/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.empleados.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    {{-- Encabezados de la tabla --}}
                    <thead>
                        <th>ID</th>
                        <th>Ciudad</th>
                        <th>Cargo</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>CI</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>

                    {{-- Datos de la tabla --}}
                    @foreach ($empleados as $empleado)
                        <tr>
                            <td>{{ $empleado->idempleado }}</td>
                            <td>{{ $empleado->ciudad }}</td>
                            <td>{{ $empleado->cargo }}</td>
                            <td>{{ $empleado->nombre }}</td>
                            <td>{{ $empleado->apellido }}</td>
                            <td>{{ $empleado->ci }}</td>
                            <td>{{ $empleado->direccion }}</td>
                            <td>{{ $empleado->telefono }}</td>
                            <td>{{ $empleado->estado }}</td>
                            <td>
                            <a href="{{ route('empleados.edit', $empleado->idempleado) }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $empleado->idempleado }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.empleados.modal')
                    @endforeach
                </table>
            </div>
            {{ $empleados->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
