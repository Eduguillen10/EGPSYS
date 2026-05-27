@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Formas de Cobro <a href="{{ URL('referenciales/formacobro/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.formacobro.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>Id</th>
                        <th>Descripción de la Forma de Cobro</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($formacobros as $formacobro)
                        <tr>
                            <td>{{ $formacobro->id_formacobro }}</td>
                        <td>{{ $formacobro->descripcion }}</td>
                        <td>@include('referenciales.partials.estado', ['estado' => $formacobro->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ URL('referenciales/formacobro/'.$formacobro->id_formacobro.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $formacobro->id_formacobro }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.formacobro.modal')
                    @endforeach
                </table>
            </div>
            {{ $formacobros->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
