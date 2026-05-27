@extends ('layouts.admin')
@section ('contenido')

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h3>Listado de Cajas <a href="{{ url('referenciales/cajas/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('referenciales.cajas.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Nombre de la Caja</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($cajas as $caja)
                <tr>
                    <td>{{ $caja->idcaja }}</td>
                    <td>{{ $caja->descripcion }}</td>
                    <td>{{ $caja->usuario ?? 'No asignado' }}</td>
                    <td style="background-color: {{ ($caja->estado == 'Activo') ? 'green' : 'red' }}; color: white;">
                        {{ $caja->estado }}
                    </td>
                    <td>
                        <a href="{{ url('referenciales/cajas/'.$caja->idcaja.'/edit') }}">
                            <button class="btn btn-info">Editar</button>
                        </a>
                        @if ($caja->estado == 'Activo')
                            <form action="{{ url('referenciales/cajas/'.$caja->idcaja) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Desactivar</button>
                            </form>
                        @else
                            <button class="btn btn-secondary" disabled>Inactivo</button>
                        @endif
                    </td>
                </tr>
                @include('referenciales.cajas.modal')
                @endforeach
            </table>
        </div>
        {{ $cajas->appends(Request::only(['searchText']))->render() }}
    </div>
</div>

@endsection
