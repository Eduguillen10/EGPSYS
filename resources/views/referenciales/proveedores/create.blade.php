@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Nuevo Proveedor</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ url('referenciales/proveedores') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{-- Campos del formulario --}}
                <div class="form-group">
                    <label for="idciudad">Ciudad</label>
                    <select name="idciudad" class="form-control">
                        <option value="" disabled selected>Seleccione una ciudad</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->idciudad }}">{{ $ciudad->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="razonsocial">Razón Social</label>
                    <input type="text" name="razonsocial" class="form-control" placeholder="Razón Social...">
                </div>

                <div class="form-group">
                    <label for="ruc">RUC</label>
                    <input type="text" name="ruc" class="form-control" placeholder="RUC...">
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Dirección...">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono...">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/proveedores') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
