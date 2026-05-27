@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Nueva Ventana</h3>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('acceso/ventanas') }}" method="POST" autocomplete="off">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="idmodulo">Modulo</label>
                <select name="idmodulo" class="form-control" required>
                    @foreach ($modulos as $modulo)
                        <option value="{{ $modulo->idmodulo }}" {{ old('idmodulo') == $modulo->idmodulo ? 'selected' : '' }}>{{ $modulo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="form-group">
                <label for="ruta">Ruta</label>
                <input type="text" name="ruta" class="form-control" value="{{ old('ruta') }}" placeholder="/acceso/ventanas" required>
            </div>
            <div class="form-group">
                <label for="permiso_clave">Clave de permiso</label>
                <input type="text" name="permiso_clave" class="form-control" value="{{ old('permiso_clave') }}" placeholder="ventanas" required>
            </div>
            <div class="form-group">
                <label for="orden">Orden</label>
                <input type="number" name="orden" class="form-control" value="{{ old('orden', 0) }}" min="0" required>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="estado" value="1" {{ old('estado', '1') ? 'checked' : '' }}> Activo
                </label>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a href="{{ url('acceso/ventanas') }}" class="btn btn-danger">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
