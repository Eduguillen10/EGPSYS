@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Nuevo Vehículo</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('referenciales/vehiculos') }}" method="POST" autocomplete="off">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="nrochapa">Número de Chapa</label>
                    <input type="text" name="nrochapa" class="form-control" placeholder="Número de Chapa...">
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" name="color" class="form-control" placeholder="Color...">
                </div>
                <div class="form-group">
                    <label for="chasis">Chasis</label>
                    <input type="text" name="chasis" class="form-control" placeholder="Chasis...">
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" class="form-control" placeholder="Modelo...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/vehiculos') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
