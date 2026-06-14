@extends ('layouts.admin')
@section ('contenido')

<style>
    .orden-header {
        margin-bottom: 18px;
    }

    .orden-panel {
        border: 1px solid #d9e2ec;
        border-radius: 4px;
        background: #fff;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
    }

    .orden-action {
        border: 1px dashed #9ecae1;
        border-radius: 4px;
        background: #f8fcff;
        padding: 18px;
    }

    .orden-action h4 {
        margin-top: 0;
        color: #2c3e50;
        font-weight: 700;
    }
</style>

@include('compras.partials.create-styles')

<div class="row orden-header">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Nueva Orden de Compra</h3>
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
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>
</div>

@include('compras.orden.modalelegir')

<div class="orden-panel">
    <div class="row">
        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Sucursal</label>
                <input type="text" class="form-control" value="{{ $sucursales->descripcion ?? 'Sin sucursal seleccionada' }}" readonly>
            </div>
        </div>

        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
            </div>
        </div>

        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" class="form-control" value="{{ $fecha }}" readonly>
            </div>
        </div>
    </div>
</div>

<div class="orden-panel">
    <div class="orden-action">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <h4>Seleccione el presupuesto a convertir en orden</h4>
                <p>El sistema cargara el proveedor y el detalle del presupuesto. En esta instancia se selecciona el deposito donde seran recibidos los productos.</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-right">
                <a href="" data-target="#modal-elegir" data-toggle="modal" class="btn btn-info">
                    <i class="fa fa-download" aria-hidden="true"></i> Seleccionar Presupuesto
                </a>
                <a href="{{ url('compras/orden') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
