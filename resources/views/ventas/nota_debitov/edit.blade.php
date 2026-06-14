@extends('layouts.admin')
@section('contenido')

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

<div class="row tm-detail-row">
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Nota de Debito</label>
            <p>{{ $nota_debitov->nro_nota_debito ?? ('#' . $nota_debitov->idnota_debitov) }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Sucursal</label>
            <p>{{ $nota_debitov->sucursal }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Deposito</label>
            <p>{{ $nota_debitov->deposito }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Usuario</label>
            <p>{{ $nota_debitov->usuario }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Razon Social</label>
            <p>{{ $nota_debitov->cliente }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>RUC-C.I.</label>
            <p>{{ $nota_debitov->num_documento }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Venta</label>
            <p>{{ $nota_debitov->idventa }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Fecha Factura</label>
            <p>{{ date('d/m/Y', strtotime($nota_debitov->fecha_factura)) }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Factura</label>
            <p>{{ $nota_debitov->nro_factura }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Timbrado</label>
            <p>{{ $nota_debitov->timbrado }}</p>
        </div>
    </div>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
        <div class="form-group">
            <label>Concepto</label>
            <p>{{ $nota_debitov->concepto ?: 'Sin concepto' }}</p>
        </div>
    </div>
</div>

<form action="{{ url('ventas/nota_debitov/'.$nota_debitov->idnota_debitov) }}" method="POST" autocomplete="off" id="ventaForm">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Detalle de la Nota de Debito</h3>
            </div>
            <div class="panel-body">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <div class="table-responsive">
                        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color:#A9D0F5">
                                <tr>
                                    <th style="width: 70px;">Items</th>
                                    <th>Producto</th>
                                    <th style="width: 170px;">Cantidad</th>
                                    <th style="width: 180px;">Precio Venta</th>
                                    <th style="width: 120px;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $det)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="idnota_debitov_detalle[]" value="{{ $det->idnota_debitov_detalle }}">
                                            <input type="hidden" name="idproducto[{{ $det->idnota_debitov_detalle }}]" value="{{ $det->idproducto }}">
                                            {{ $det->idproducto }}
                                        </td>
                                        <td>{{ $det->producto }}</td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right" name="cantidad[{{ $det->idnota_debitov_detalle }}]" value="{{ \App\Helpers\NumberFormatter::cantidadInput($det->cantidad) }}" min="0.001" step="0.001" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-sm text-right" name="precio_venta[{{ $det->idnota_debitov_detalle }}]" value="{{ (int) $det->precio_venta }}" min="1" step="1" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-delete-{{ $det->idnota_debitov_detalle }}">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="guardar">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Actualizar</button>
                        <button class="btn btn-default" onclick="window.location.href='{{ url('ventas/nota_debitov/create') }}'" type="button">
                            <i class="fa fa-arrow-left"></i> Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@foreach($detalles as $det)
    @include('ventas.nota_debitov.modaldetalles', ['det' => $det])
@endforeach

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('ventaForm').addEventListener('submit', function (event) {
            var preciosVenta = document.querySelectorAll('input[name^="precio_venta["]');

            for (var i = 0; i < preciosVenta.length; i++) {
                if (!preciosVenta[i].value || parseFloat(preciosVenta[i].value) <= 0) {
                    alert('Todos los precios de venta deben tener un valor mayor a cero.');
                    event.preventDefault();
                    return;
                }
            }
        });
    });
</script>
@endsection

@endsection
