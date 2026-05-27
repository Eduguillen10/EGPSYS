@extends('layouts.admin')
@section('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Empresas <a href="empresas/create"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.empresas.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre de la Empresa</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->idempresa }}</td>
                    <td>{{ $empresa->descripcion }}</td>
                    <td>@include('referenciales.partials.estado', ['estado' => $empresa->estado ?? 'Activo'])</td>
                    <td>
                        <a href="{{ URL('referenciales/empresas/'.$empresa->idempresa.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                        <a href="" data-target="#modal-delete-{{ $empresa->idempresa }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                    </td>
                </tr>
                @include('referenciales.empresas.modal')
                @endforeach
            </table>
        </div>
        {{ $empresas->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
