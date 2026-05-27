@extends('layouts.admin')
@section('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Depósitos <a href="depositos/create"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.depositos.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre del Depósito</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($depositos as $deposito)
                <tr>
                    <td>{{ $deposito->iddeposito }}</td>
                    <td>{{ $deposito->descripcion }}</td>
                    <td>@include('referenciales.partials.estado', ['estado' => $deposito->estado ?? 'Activo'])</td>
                    <td>
                        <a href="{{ URL('referenciales/depositos/'.$deposito->iddeposito.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                        <a href="" data-target="#modal-delete-{{ $deposito->iddeposito }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                    </td>
                </tr>
                @include('referenciales.depositos.modal')
                @endforeach
            </table>
        </div>
        {{ $depositos->appends(Request::only(['searchText']))->render() }}
    </div>
</div>

@endsection
