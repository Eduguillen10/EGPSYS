@extends('layouts.admin')
@section('contenido')

@include('compras.partials.create-styles')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row orden-header">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <h3>Editar Orden de Compra</h3>
    </div>
</div>

<div class="row tm-detail-row">
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Orden</label>
            <p>{{ $orden->idordencompra }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Sucursal</label>
            <p>{{ $orden->sucursal }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Proveedor</label>
            <p>{{ $orden->proveedor }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>RUC</label>
            <p>{{ $orden->ruc }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Numero Presupuesto</label>
            <p>{{ $orden->idpresupuestocompra }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Usuario</label>
            <p>{{ $orden->usuario }}</p>
        </div>
    </div>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
        <div class="form-group">
            <label>Observacion</label>
            <p>{{ $orden->observacion ?: 'Sin observacion' }}</p>
        </div>
    </div>
</div>

<form action="{{ url('compras/orden/'.$orden->idordencompra) }}" method="POST" autocomplete="off" id="ordenForm">
    @method('PUT')
    @csrf

    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Detalle de la Orden</h3>
            </div>
            <div class="panel-body">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <p class="text-muted" style="margin-bottom: 12px;">
                        Los precios se cargan desde el presupuesto seleccionado. Puede modificarlos antes de actualizar la orden.
                    </p>

                    <div class="table-responsive">
                        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color:#ffd966">
                                <tr>
                                    <th style="width: 70px;">Items</th>
                                    <th>Producto</th>
                                    <th style="width: 170px;">Cantidad</th>
                                    <th style="width: 180px;">Precio Compra</th>
                                    <th style="width: 150px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detalles as $det)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="idorden_detalle[]" value="{{ $det->idorden_detalle }}">
                                            <input type="hidden" name="idproducto[{{ $det->idorden_detalle }}]" value="{{ $det->idproducto }}">
                                            {{ $det->idproducto }}
                                        </td>
                                        <td>{{ $det->producto }}</td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right cantidad-orden" name="cantidad[{{ $det->idorden_detalle }}]" value="{{ \App\Helpers\NumberFormatter::cantidadInput($det->cantidad) }}" min="0.001" step="0.001" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right precio-orden" name="precio_compra[{{ $det->idorden_detalle }}]" value="{{ (int) $det->precio_compra }}" min="1" step="1" required>
                                        </td>
                                        <td class="text-right monto-orden">{{ number_format($det->cantidad * $det->precio_compra, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="guardar">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Actualizar</button>
                        <button class="btn btn-default" onclick="window.location.href='{{ url('compras/orden/create') }}'" type="button">
                            <i class="fa fa-arrow-left"></i> Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatGs(value) {
            return new Intl.NumberFormat('es-PY', { maximumFractionDigits: 0 }).format(Math.round(value || 0));
        }

        function updateMonto(row) {
            var cantidad = parseFloat(row.querySelector('.cantidad-orden').value || 0);
            var precio = parseFloat(row.querySelector('.precio-orden').value || 0);
            row.querySelector('.monto-orden').textContent = formatGs(cantidad * precio);
        }

        document.querySelectorAll('#detalles tbody tr').forEach(function(row) {
            row.querySelectorAll('.cantidad-orden, .precio-orden').forEach(function(input) {
                input.addEventListener('input', function() {
                    updateMonto(row);
                });
            });
            updateMonto(row);
        });

        document.getElementById('ordenForm').addEventListener('submit', function (event) {
            var preciosCompra = document.querySelectorAll('input[name^="precio_compra["]');

            for (var i = 0; i < preciosCompra.length; i++) {
                if (!preciosCompra[i].value || parseFloat(preciosCompra[i].value) <= 0) {
                    alert('Todos los precios de compra deben tener un valor mayor a cero.');
                    event.preventDefault();
                    return;
                }
            }
        });
    });
</script>
@endsection

@endsection
