@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Depósito: {{ $deposito->descripcion }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/referenciales/depositos/'.$deposito->iddeposito) }}" method="POST" accept-charset="UTF-8">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Nombre del depósito</label>
                    <input type="text" name="descripcion" class="form-control" value="{{ $deposito->descripcion }}" placeholder="Descripción...">
                </div>
                <div class="form-group">
                    <label for="sucursal" class="control-label">Sucursal</label>
                    <select name="idsucursal" id="sucursal" class="form-control"> 
                    @foreach ($sucursales as $suc)
                        <option value="{{$suc->idsucursal}}" @if ($suc->idsucursal == $deposito->idsucursal) selected="selected" @endif /> {{$suc->idsucursal}} - {{$suc->descripcion}} </option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/depositos') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
