@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Editar Modulo: {{ $modulo->nombre }}</h3>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('acceso/modulos/'.$modulo->idmodulo) }}" method="POST" accept-charset="UTF-8">
            <input type="hidden" name="_method" value="PUT">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $modulo->nombre) }}" required>
            </div>
            <div class="form-group">
                <label for="icono">Icono</label>
                <input type="text" name="icono" class="form-control" value="{{ old('icono', $modulo->icono) }}">
            </div>
            <div class="form-group">
                <label for="orden">Orden</label>
                <input type="number" name="orden" class="form-control" value="{{ old('orden', $modulo->orden) }}" min="0" required>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="estado" value="1" {{ old('estado', $modulo->estado) ? 'checked' : '' }}> Activo
                </label>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a href="{{ url('acceso/modulos') }}" class="btn btn-danger">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
