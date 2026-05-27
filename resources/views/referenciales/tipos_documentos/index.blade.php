@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Tipos de Documento <a href="tipos_documentos/create"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.tipos_documentos.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Descripción del Tipo de Documento</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($tiposDocumentos as $tipoDocumento)
                        <tr>
                            <td>{{ $tipoDocumento->idtipodocumento }}</td>
                            <td>{{ $tipoDocumento->descripcion }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $tipoDocumento->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ URL('referenciales/tipos_documentos/'.$tipoDocumento->idtipodocumento.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $tipoDocumento->idtipodocumento }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.tipos_documentos.modal')
                    @endforeach
                </table>
            </div>
            {{ $tiposDocumentos->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
