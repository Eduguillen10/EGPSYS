@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Entidades Emisoras <a href="{{ URL('referenciales/entidademisora/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.entidademisora.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Descripción de la Entidad Emisora</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($entidademisoras as $entidademisora)
                        <tr>
                            <td>{{ $entidademisora->identidademisora }}</td>
                            <td>{{ $entidademisora->descripcion }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $entidademisora->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ URL('referenciales/entidademisora/'.$entidademisora->identidademisora.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $entidademisora->identidademisora }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.entidademisora.modal')
                    @endforeach
                </table>
            </div>
            {{ $entidademisoras->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
