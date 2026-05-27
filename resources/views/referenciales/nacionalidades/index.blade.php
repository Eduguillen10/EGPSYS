@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Nacionalidades <a href="nacionalidades/create"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales/nacionalidades.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre de la nacionalidad</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($nacionalidades as $nacionalidad)
                <tr>
                    <td>{{ $nacionalidad->idnacionalidad }}</td>
                    <td>{{ $nacionalidad->descripcion }}</td>
                    <td>@include('referenciales.partials.estado', ['estado' => $nacionalidad->estado ?? 'Activo'])</td>
                    <td>
                        <a href="{{ URL('referenciales/nacionalidades/'.$nacionalidad->idnacionalidad.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                        <a href="" data-target="#modal-delete-{{ $nacionalidad->idnacionalidad }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                    </td>
                </tr>
                @include('referenciales.nacionalidades.modal')
                @endforeach
            </table>
        </div>
        {{ $nacionalidades->appends(Request::only(['searchText']))->render() }}
    </div>
</div>

@endsection
