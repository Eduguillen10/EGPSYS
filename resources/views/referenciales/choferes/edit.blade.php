@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Chofer: {{ $chofer->nombre }} {{ $chofer->apellido }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/referenciales/choferes/'.$chofer->idchofer) }}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $chofer->nombre }}" placeholder="Nombre...">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="{{ $chofer->apellido }}" placeholder="Apellido...">
                </div>
                <div class="form-group">
                    <label for="ci">CI</label>
                    <input type="text" name="ci" class="form-control" value="{{ $chofer->ci }}" placeholder="CI...">
                </div>
                <div class="form-group">
                    <label for="ruc">RUC</label>
                    <input type="text" name="ruc" class="form-control" value="{{ $chofer->ruc }}" placeholder="RUC...">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ $chofer->direccion }}" placeholder="Dirección...">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $chofer->telefono }}" placeholder="Teléfono...">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control" value="{{ $chofer->email }}" placeholder="Email...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/choferes') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
