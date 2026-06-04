@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Detalle de Nota de Debito de Compra</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Datos del comprobante</div>
            <div class="panel-body">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Nro. Nota Debito</label>
                        <p>{{ $nota_debitoc->idnota_debitoc }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Compra</label>
                        <p>{{ $nota_debitoc->idcompra }} / {{ $nota_debitoc->nro_factura_compra }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Sucursal</label>
                        <p>{{ $nota_debitoc->sucursal }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Deposito</label>
                        <p>{{ $nota_debitoc->deposito }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Usuario</label>
                        <p>{{ $nota_debitoc->usuario }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Estado</label>
                        <p>{{ $nota_debitoc->estado }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                    <div class="form-group">
                        <label>Proveedor</label>
                        <p>{{ $nota_debitoc->proveedor }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>RUC</label>
                        <p>{{ $nota_debitoc->num_documento }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Fecha Comprobante</label>
                        <p>{{ $nota_debitoc->fecha_factura ? date('d/m/Y', strtotime($nota_debitoc->fecha_factura)) : '' }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Nro. Comprobante</label>
                        <p>{{ $nota_debitoc->nro_nota_debito }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Timbrado</label>
                        <p>{{ $nota_debitoc->timbrado }}</p>
                    </div>
                </div>
                <div class="col-lg-1 col-sm-1 col-md-1 col-xs-12">
                    <div class="form-group">
                        <label>Stock</label>
                        <p>{{ $nota_debitoc->mueve_stock ? 'Entrada' : 'No mueve' }}</p>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <div class="form-group">
                        <label>Concepto</label>
                        <p>{{ $nota_debitoc->concepto }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Detalle</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color:#A9D0F5">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Compra</th>
                                <th>IVA 10</th>
                                <th>IVA 5</th>
                                <th>Gravada 10</th>
                                <th>Gravada 5</th>
                                <th>Exenta</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($total = 0)
                            @foreach($detalles as $det)
                                @php($total += (int) $det->montoitems)
                                <tr>
                                    <td>{{ $det->producto }}</td>
                                    <td>{{ number_format($det->cantidad, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->precio_compra, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->iva10, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->iva5, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->gravada10, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->gravada5, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->exenta, 0, ',', '.') }}</td>
                                    <td>{{ number_format($det->montoitems, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="8">Total</th>
                                <th>{{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('nota_debitoc.index') }}" class="btn btn-light">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
