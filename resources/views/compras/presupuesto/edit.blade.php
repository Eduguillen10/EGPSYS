@extends('layouts.admin')
@section('contenido')

@include('compras.partials.create-styles')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row presupuesto-header">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <h3>Editar Presupuesto de Compra</h3>
    </div>
</div>

<div class="row tm-detail-row">
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="idpresupuestocompra">Nro. Presupuesto</label>
            <p>{{ $presupuesto->idpresupuestocompra }}</p>
        </div>
    </div>

    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="idsucursal">Sucursal</label>
            <p>{{ $presupuesto->descripcion }}</p>
        </div>
    </div>

    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="idproveedor">Proveedor</label>
            <p>{{ $presupuesto->razonsocial }}</p>
        </div>
    </div>

    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="ruc">RUC</label>
            <p>{{ $presupuesto->ruc }}</p>
        </div>
    </div>

    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="idpedidocompra">Numero Pedido</label>
            <p>{{ $presupuesto->idpedidocompra }}</p>
        </div>
    </div>

    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label for="usuario">Usuario</label>
            <p>{{ $presupuesto->usuario }}</p>
        </div>
    </div>

    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
        <div class="form-group">
            <label for="observacion">Observacion</label>
            <p>{{ $presupuesto->observacion ?: 'Sin observacion' }}</p>
        </div>
    </div>
</div>

<form action="{{ url('compras/presupuesto/' . $presupuesto->idpresupuestocompra) }}" method="POST" autocomplete="off" id="presupuestoForm">
    @method('PUT')
    @csrf

    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Detalle del Presupuesto</h3>
            </div>
            <div class="panel-body">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <p class="text-muted" style="margin-bottom: 12px;">
                        Los precios se cargan desde el precio de compra del producto. Puede modificarlos antes de actualizar el presupuesto.
                    </p>

                    <div class="table-responsive">
                        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color:#ffd966">
                                <tr>
                                    <th style="width: 70px;">Items</th>
                                    <th>Producto</th>
                                    <th style="width: 170px;">Cantidad</th>
                                    <th style="width: 180px;">Precio Compra</th>
                                    <th style="width: 150px;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detalles as $det)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="idpresupuestocompra_detalle[]" value="{{ $det->idpresupuestocompra_detalle }}">
                                            <input type="hidden" name="idproducto[{{ $det->idpresupuestocompra_detalle }}]" value="{{ $det->idproducto }}">
                                            {{ $det->items }}
                                        </td>
                                        <td>{{ $det->producto }} {{ $det->marcas }}</td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right cantidad-presupuesto" name="cantidad[{{ $det->idpresupuestocompra_detalle }}]" value="{{ \App\Helpers\NumberFormatter::cantidadInput($det->cantidad) }}" min="0.001" step="0.001" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right precio-presupuesto" name="precio[{{ $det->idpresupuestocompra_detalle }}]" value="{{ (int) $det->precio }}" min="1" step="1" required>
                                        </td>
                                        <td class="text-right monto-presupuesto">{{ number_format($det->cantidad * $det->precio, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="guardar">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Actualizar</button>
                        <button class="btn btn-default" onclick="window.location.href='{{ url('compras/presupuesto/create') }}'" type="button">
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
    document.addEventListener('DOMContentLoaded', function() {
        function formatGs(value) {
            return new Intl.NumberFormat('es-PY', { maximumFractionDigits: 0 }).format(Math.round(value || 0));
        }

        function updateMonto(row) {
            var cantidad = parseFloat(row.querySelector('.cantidad-presupuesto').value || 0);
            var precio = parseFloat(row.querySelector('.precio-presupuesto').value || 0);
            row.querySelector('.monto-presupuesto').textContent = formatGs(cantidad * precio);
        }

        document.querySelectorAll('#detalles tbody tr').forEach(function(row) {
            row.querySelectorAll('.cantidad-presupuesto, .precio-presupuesto').forEach(function(input) {
                input.addEventListener('input', function() {
                    updateMonto(row);
                });
            });
            updateMonto(row);
        });

        document.getElementById('presupuestoForm').addEventListener('submit', function(event) {
            var preciosCompra = document.querySelectorAll('input[name^="precio["]');

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
