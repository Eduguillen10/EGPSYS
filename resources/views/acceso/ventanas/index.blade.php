@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Ventanas <a href="{{ url('acceso/ventanas/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>

        <form action="{{ url('acceso/ventanas') }}" method="GET" autocomplete="off" role="search">
            <div class="input-group">
                <input type="text" class="form-control" name="searchText" placeholder="Buscar..." value="{{ $query }}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </span>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Modulo</th>
                    <th>Nombre</th>
                    <th>Ruta</th>
                    <th>Clave</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($ventanas as $ventana)
                    <tr>
                        <td>{{ $ventana->idventana }}</td>
                        <td>{{ optional($ventana->modulo)->nombre }}</td>
                        <td>{{ $ventana->nombre }}</td>
                        <td>{{ $ventana->ruta }}</td>
                        <td>{{ $ventana->permiso_clave }}</td>
                        <td>{{ $ventana->orden }}</td>
                        <td>{{ $ventana->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ URL('acceso/ventanas/'.$ventana->idventana.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $ventana->idventana }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('acceso.ventanas.modal')
                @endforeach
            </table>
        </div>
        {{ $ventanas->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
