@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Vehículo: {{ $vehiculo->nrochapa }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/referenciales/vehiculos/'.$vehiculo->idvehiculo) }}" method="POST" accept-charset="UTF-8">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="nrochapa">Número de Chapa</label>
                    <input type="text" name="nrochapa" class="form-control" value="{{ $vehiculo->nrochapa }}" placeholder="Número de Chapa...">
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" name="color" class="form-control" value="{{ $vehiculo->color }}" placeholder="Color...">
                </div>
                <div class="form-group">
                    <label for="chasis">Chasis</label>
                    <input type="text" name="chasis" class="form-control" value="{{ $vehiculo->chasis }}" placeholder="Chasis...">
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ $vehiculo->modelo }}" placeholder="Modelo...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/vehiculos') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
