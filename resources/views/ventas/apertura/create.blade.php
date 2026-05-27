@extends('layouts.admin')
@section('contenido')
<div class="container" style="margin-left: 150px;">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <h3>Nueva Apertura</h3>

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <form action="{{ url('ventas/apertura') }}" method="POST" autocomplete="off" class="mx-auto">
        @csrf

        <div class="row">
            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="idapertura">ID Apertura</label>
                    <input type="text" class="form-control" value="{{ $nextidapertura }}" readonly>
                </div>
            </div>

            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" name="usuario" value="{{ Auth::user()->name }}" class="form-control" readonly>
                    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                </div>
            </div>

            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="idsucursal">Sucursal</label>
                    <input type="text" class="form-control" value="{{ $sucursales ? $sucursales->descripcion : 'No hay sucursal seleccionada' }}" readonly>
                    <input type="hidden" name="idsucursal" value="{{ $sucursales ? $sucursales->idsucursal : '' }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="fecha_apertura">Fecha Apertura</label>
                    <input type="date" name="fecha_apertura" readonly value="{{ old('fecha_apertura', date('Y-m-d')) }}" class="form-control">
                </div>
            </div>

            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="idcaja">Caja</label>
                    <select name="idcaja" class="form-control" required>
                        <option value="" selected disabled>Selecciona una caja</option>
                        @foreach($caja as $cj)
                            <option value="{{ $cj->idcaja }}">{{ $cj->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="monto_inicial">Monto Inicial</label>
                    <input type="number" name="monto_inicial" value="{{ old('monto_inicial') }}" class="form-control" placeholder="Monto Inicial..." required min="0">
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-8 col-xs-12">
                <div class="form-group text-center">
                    <button class="btn btn-primary float-right ml-2" type="submit">Guardar</button>
                    <button class="btn btn-danger float-right" type="reset">Cancelar</button>
                    <button class="btn btn-light" onclick="window.location.href='{{ url('ventas/apertura') }}'" type="button">
                        <i class="fa fa-arrow-left"></i> Volver
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
