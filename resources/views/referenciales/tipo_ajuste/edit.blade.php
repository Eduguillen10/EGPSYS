@extends('layouts.admin')
@section('contenido')
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-12">
        <h3>Editar Tipo Ajuste: {{ $tipo->descripcion }}</h3>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/referenciales/tipo_ajuste/'.$tipo->idtipo_ajuste) }}" method="POST" accept-charset="UTF-8">
            <input type="hidden" name="_method" value="PUT">
            {{ csrf_field() }}
            <div class="form-group">
                <label>Descripcion</label>
                <select name="descripcion" class="form-control">
                    <option value="Entrada" {{ $tipo->descripcion === 'Entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="Salida" {{ $tipo->descripcion === 'Salida' ? 'selected' : '' }}>Salida</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a href="{{ url('referenciales/tipo_ajuste') }}" class="btn btn-danger">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
