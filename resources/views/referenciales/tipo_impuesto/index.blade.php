@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Tipos de Impuesto <a href="tipo_impuesto/create"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.tipo_impuesto.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Descripción del Tipo de Impuesto</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($tipoImpuestos as $tipoImpuesto)
                <tr>
                    <td>{{ $tipoImpuesto->idtipoimpuesto }}</td>
                    <td>{{ $tipoImpuesto->descripcion }}</td>
                    <td>@include('referenciales.partials.estado', ['estado' => $tipoImpuesto->estado ?? 'Activo'])</td>
                    <td>
                        <a href="{{ URL('referenciales/tipo_impuesto/'.$tipoImpuesto->idtipoimpuesto.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                        <a href="" data-target="#modal-delete-{{ $tipoImpuesto->idtipoimpuesto }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                    </td>
                </tr>
                @include('referenciales.tipo_impuesto.modal')
                @endforeach
            </table>
        </div>
        {{ $tipoImpuestos->appends(Request::only(['searchText']))->render() }}
    </div>
</div>

@endsection
