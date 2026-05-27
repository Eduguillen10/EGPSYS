@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Modulos <a href="{{ url('acceso/modulos/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('acceso/modulos') }}" method="GET" autocomplete="off" role="search">
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
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Icono</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Ventanas</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($modulos as $modulo)
                    <tr>
                        <td>{{ $modulo->idmodulo }}</td>
                        <td>{{ $modulo->nombre }}</td>
                        <td><i class="{{ $modulo->icono }}"></i> {{ $modulo->icono }}</td>
                        <td>{{ $modulo->orden }}</td>
                        <td>{{ $modulo->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>{{ $modulo->ventanas_count }}</td>
                        <td>
                            <a href="{{ URL('acceso/modulos/'.$modulo->idmodulo.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $modulo->idmodulo }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('acceso.modulos.modal')
                @endforeach
            </table>
        </div>
        {{ $modulos->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
