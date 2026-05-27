@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Rubros <a href="rubros/create"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales/rubros.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre del Rubro</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($rubros as $rubro)
                <tr>
                    <td>{{ $rubro->idrubro }}</td>
                    <td>{{ $rubro->descripcion }}</td>
                    <td>@include('referenciales.partials.estado', ['estado' => $rubro->estado ?? 'Activo'])</td>
                    <td>
                        <a href="{{ URL('referenciales/rubros/'.$rubro->idrubro.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                        <a href="" data-target="#modal-delete-{{ $rubro->idrubro }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                    </td>
                </tr>
                @include('referenciales.rubros.modal')
                @endforeach
            </table>
        </div>
        {{ $rubros->appends(Request::only(['searchText']))->render() }}
    </div>
</div>

@endsection
