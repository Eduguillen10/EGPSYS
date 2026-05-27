@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Tipos de Arqueo <a href="tipo_arqueo/create"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.tipo_arqueo.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Descripción del Tipo de Arqueo</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($tipoArqueos as $tipoArqueo)
                    <tr>
                        <td>{{ $tipoArqueo->idtipoarqueo }}</td>
                        <td>{{ $tipoArqueo->descripcion }}</td>
                        <td>@include('referenciales.partials.estado', ['estado' => $tipoArqueo->estado ?? 'Activo'])</td>
                        <td>
                            <a href="{{ URL('referenciales/tipo_arqueo/'.$tipoArqueo->idtipoarqueo.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $tipoArqueo->idtipoarqueo }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('referenciales.tipo_arqueo.modal')
                    @endforeach
                </table>
            </div>
            {{ $tipoArqueos->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
