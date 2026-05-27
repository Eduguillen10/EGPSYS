@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Sucursal: {{ $sucursal->descripcion }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/referenciales/sucursales/'.$sucursal->idsucursal) }}" method="POST" accept-charset="UTF-8">
                @method('put')
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="idempresa">Empresa</label>
                    <select name="idempresa" class="form-control">
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->idempresa }}" {{ $empresa->idempresa == $sucursal->idempresa ? 'selected' : '' }}>
                                {{ $empresa->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="descripcion">Nombre de la sucursal</label>
                    <input type="text" name="descripcion" class="form-control" value="{{ $sucursal->descripcion }}" placeholder="Descripción...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/sucursales') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
