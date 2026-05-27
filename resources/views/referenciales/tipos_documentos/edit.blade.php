@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Tipo de Documento: {{ $tiposDocumentos->descripcion }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/referenciales/tipos_documentos/'.$tiposDocumentos->idtipodocumento) }}" method="POST" accept-charset="UTF-8">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Descripción del tipo de documento</label>
                    <input type="text" name="descripcion" class="form-control" value="{{ $tiposDocumentos->descripcion }}" placeholder="Descripción...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/tipos_documentos') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
