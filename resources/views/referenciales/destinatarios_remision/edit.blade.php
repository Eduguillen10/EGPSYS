@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Editar Destinatario: {{ $destinatario->nombre }}</h3>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/referenciales/destinatarios_remision/'.$destinatario->iddestinatario_remision) }}" method="POST" autocomplete="off">
            <input type="hidden" name="_method" value="PUT">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="nombre">Nombre / Razon social</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $destinatario->nombre) }}" placeholder="Nombre o razon social...">
            </div>
            <div class="form-group">
                <label for="documento">Documento / RUC</label>
                <input type="text" name="documento" class="form-control" value="{{ old('documento', $destinatario->documento) }}" placeholder="Documento o RUC...">
            </div>
            <div class="form-group">
                <label for="direccion">Direccion</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $destinatario->direccion) }}" placeholder="Direccion...">
            </div>
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $destinatario->telefono) }}" placeholder="Telefono...">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" class="form-control" value="{{ old('email', $destinatario->email) }}" placeholder="Email...">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a href="{{ url('referenciales/destinatarios_remision') }}" class="btn btn-danger">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
