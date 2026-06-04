@extends ('layouts.admin')
@section ('contenido')

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Nueva Caja</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('referenciales/cajas') }}" method="POST" autocomplete="off">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="descripcion">Nombre de la caja</label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción..." required>
                </div>

                <div class="form-group">
                    <label>Usuario Creador</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" class="form-control" value="Activo" readonly>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/cajas') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection
