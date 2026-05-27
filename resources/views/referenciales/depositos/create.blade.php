@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Nuevo Depósito</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('referenciales/depositos') }}" method="POST" autocomplete="off">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Nombre del depósito</label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción...">
                </div>

                <div class="form-group">
    					<label>Sucursal</label>
    					<select name="idsucursal" class="form-control selectpicker" id="idsucursal" data-Live-search="true">
    						@foreach($sucursales as $suc)
    						<option value="{{$suc->idsucursal}}">{{$suc->descripcion}}</option>
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
