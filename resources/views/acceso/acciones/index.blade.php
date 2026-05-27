@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Acciones <a href="{{ url('acceso/acciones/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('acceso/acciones') }}" method="GET" autocomplete="off" role="search">
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
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Clave</th>
                    <th>Orden</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($acciones as $accion)
                    <tr>
                        <td>{{ $accion->idaccion }}</td>
                        <td>{{ $accion->nombre }}</td>
                        <td>{{ $accion->clave }}</td>
                        <td>{{ $accion->orden }}</td>
                        <td>
                            <a href="{{ URL('acceso/acciones/'.$accion->idaccion.'/edit') }}"><button class="btn btn-info">Editar</button></a>
                            <a href="" data-target="#modal-delete-{{ $accion->idaccion }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                        </td>
                    </tr>
                    @include('acceso.acciones.modal')
                @endforeach
            </table>
        </div>
        {{ $acciones->appends(Request::only(['searchText']))->render() }}
    </div>
</div>
@endsection
