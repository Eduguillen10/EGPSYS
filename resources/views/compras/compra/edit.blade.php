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

<div class="row compra-create-header">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <h3>Editar Compra</h3>
    </div>
</div>

<div class="row tm-detail-row">
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Compra</label>
            <p>{{ $compra->idcompra }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Sucursal</label>
            <p>{{ $compra->sucursal }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Deposito</label>
            <p>{{ $compra->deposito }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Usuario</label>
            <p>{{ $compra->usuario }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Proveedor</label>
            <p>{{ $compra->proveedor }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>RUC</label>
            <p>{{ $compra->ruc }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Numero Orden</label>
            <p>{{ $compra->idordencompra }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Fecha Factura</label>
            <p>{{ date('d/m/Y', strtotime($compra->fecha_factura)) }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Factura</label>
            <p>{{ $compra->nro_factura }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Timbrado</label>
            <p>{{ $compra->timbrado }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Condicion</label>
            <p>{{ $compra->condicion }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Fecha Vencimiento</label>
            <p>{{ date('d/m/Y', strtotime($compra->fecha_vencimiento)) }}</p>
        </div>
    </div>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
        <div class="form-group">
            <label>Concepto</label>
            <p>{{ $compra->concepto ?: 'Sin concepto' }}</p>
        </div>
    </div>
</div>

<form action="{{ url('compras/compra/'.$compra->idcompra) }}" method="POST" autocomplete="off" id="compraForm">
    @method('PUT')
    @csrf

    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Detalle de la Compra</h3>
            </div>
            <div class="panel-body">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
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
                                @if ($detalles->isNotEmpty())
                                    @foreach ($detalles as $det)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="idcompra_detalle[]" value="{{ $det->idcompra_detalle }}">
                                                <input type="hidden" name="idproducto[{{ $det->idcompra_detalle }}]" value="{{ $det->idproducto }}">
                                                {{ $det->idproducto }}
                                            </td>
                                            <td>{{ $det->producto }}</td>
                                            <td>
                                                <input type="number" class="form-control input-sm text-right cantidad-compra" name="cantidad[{{ $det->idcompra_detalle }}]" value="{{ \App\Helpers\NumberFormatter::cantidadInput($det->cantidad) }}" min="0.001" step="0.001" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control input-sm text-right precio-compra" name="precio_compra[{{ $det->idcompra_detalle }}]" value="{{ (int) $det->precio_compra }}" min="1" step="1" required>
                                            </td>
                                            <td class="text-right monto-compra">{{ number_format($det->montoitems > 0 ? $det->montoitems : (($det->cantidad ?? 0) * ($det->precio_compra ?? 0)), 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">No se encontraron detalles para esta compra.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="guardar">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Actualizar</button>
                        <button class="btn btn-default" onclick="window.location.href='{{ url('compras/compra/create') }}'" type="button">
                            <i class="fa fa-arrow-left"></i> Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var compraForm = document.getElementById('compraForm');

        if (!compraForm) {
            return;
        }

        function formatGs(value) {
            return new Intl.NumberFormat('es-PY', { maximumFractionDigits: 0 }).format(Math.round(value || 0));
        }

        function updateMonto(row) {
            var cantidad = parseFloat(row.querySelector('.cantidad-compra').value || 0);
            var precio = parseFloat(row.querySelector('.precio-compra').value || 0);
            row.querySelector('.monto-compra').textContent = formatGs(cantidad * precio);
        }

        document.querySelectorAll('#detalles tbody tr').forEach(function(row) {
            var cantidad = row.querySelector('.cantidad-compra');
            var precio = row.querySelector('.precio-compra');

            if (!cantidad || !precio) {
                return;
            }

            [cantidad, precio].forEach(function(input) {
                input.addEventListener('input', function() {
                    updateMonto(row);
                });
            });
            updateMonto(row);
        });

        compraForm.addEventListener('submit', function (event) {
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
@endpush
@endsection
