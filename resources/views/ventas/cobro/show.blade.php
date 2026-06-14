@extends ('layouts.admin')
@section ('contenido')

<div class="row">
    <div class="col-lg-12">
        <h3>Detalles Cobro #{{ $cobros->id_cobro }}</h3>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('info')) <div class="alert alert-warning">{{ session('info') }}</div> @endif
    </div>
</div>

<div class="row" style="padding: 0 1em;">
    <div class="panel panel-primary">
        <div class="panel-body">

            <div class="row">
                <div class="col-lg-2">
                    <label>Nro. Recibo</label>
                    <input class="form-control" value="{{ $cobros->nro_recibo ?: 'Pendiente de emision' }}" readonly>
                </div>
                <div class="col-lg-2">
                    <label>Fecha</label>
                    <input class="form-control" value="{{ \Carbon\Carbon::parse($cobros->fecha_recibo ?: $cobros->fecha_cobro)->format('d/m/Y') }}" readonly>
                </div>
                <div class="col-lg-2">
                    <label>Caja</label>
                    <input class="form-control" value="{{ $cobros->caja }}" readonly>
                </div>
                <div class="col-lg-2">
                    <label>Apertura</label>
                    <input class="form-control" value="{{ $cobros->idapertura }}" readonly>
                </div>
                <div class="col-lg-2">
                    <label>Sucursal</label>
                    <input class="form-control" value="{{ $cobros->sucursal }}" readonly>
                </div>
                <div class="col-lg-2">
                    <label>Estado</label>
                    @php
                        $estadoCobro = trim((string) $cobros->cobro_estado);
                        $estadoCobro = ['R' => 'Realizado', 'P' => 'Pendiente', 'A' => 'Anulado'][strtoupper($estadoCobro)] ?? $estadoCobro;
                    @endphp
                    <input class="form-control" value="{{ $estadoCobro }}" readonly>
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
                <div class="col-lg-6">
                    <label>Cliente</label>
                    <input class="form-control" value="{{ $cobros->num_documento }} - {{ $cobros->cliente }}" readonly>
                </div>
                <div class="col-lg-3">
                    <label>Total a Cobrar</label>
                    <input class="form-control" value="{{ number_format($cobros->monto_cobro, 0, ',', '.') }}" readonly>
                </div>
                <div class="col-lg-3">
                    <label>Total Pagado</label>
                    <input class="form-control" value="{{ number_format($totalPagado, 0, ',', '.') }}" readonly>
                </div>
            </div>

        </div>
    </div>
</div>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#factura" aria-controls="factura" role="tab" data-toggle="tab">Factura</a></li>
    <li role="presentation"><a href="#forma_pago" aria-controls="forma_pago" role="tab" data-toggle="tab">Forma de Pago</a></li>
</ul>

<div class="tab-content">

    <div role="tabpanel" class="tab-pane active" id="factura">
        <br>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#ffd966">
                    <th>Items</th>
                    <th>Factura</th>
                    <th>Condición</th>
                    <th>Total</th>
                    <th>Cobrado</th>
                    <th>Imprimir</th>
                </thead>
                @php $sum = 0; @endphp
                @foreach ($cobrodetalle as $co)
                    @php $sum += $co->monto_detcobro; @endphp
                    <tr>
                        <td>{{ $co->items }}</td>
                        <td>{{ $co->nro_factura }}</td>
                        <td>{{ $co->condicion }}</td>
                        <td style="text-align:right;">{{ number_format($co->montoventa, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($co->monto_detcobro, 0, ',', '.') }}</td>
                        <td style="text-align:center;">
                            @if($cobros->cobro_estado == 'Realizado')
                                <a href="{{ route('venta.imprimirfactura', $co->idventa) }}" target="_blank" class="btn btn-info btn-xs">
                                    <i class="fa fa-ligth fa-print"></i>Imprimir
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="4"><b>Total</b></td>
                        <td style="text-align:right;"><b>{{ number_format($sum, 0, ',', '.') }}</b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="forma_pago">
        <br>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#ffd966">
                    <th>Items</th>
                    <th>Forma</th>
                    <th>Banco</th>
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Vencimiento</th>
                    <th>Monto</th>
                    <th>Recibido</th>
                    <th>Vuelto</th>
                </thead>

                @php $sum2 = 0; @endphp
                @foreach($formacobrodetalle as $det)
                    @php $sum2 += $det->monto_detformacobro; @endphp
                    <tr>
                        <td>{{ $det->items }}</td>
                        <td>{{ $det->formacobro }}</td>
                        <td>{{ $det->banco }}</td>
                        <td>{{ $det->documento }}</td>
                        <td>{{ $det->fecha }}</td>
                        <td>{{ $det->fecha_vencimiento }}</td>
                        <td style="text-align:right;">{{ number_format($det->monto_detformacobro, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ $det->monto_recibido !== null ? number_format($det->monto_recibido, 0, ',', '.') : '-' }}</td>
                        <td style="text-align:right;">{{ $det->vuelto !== null ? number_format($det->vuelto, 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach

                <tfoot>
                    <tr>
                        <td colspan="6"><b>Total</b></td>
                        <td style="text-align:right;"><b>{{ number_format($sum2, 0, ',', '.') }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

@include('ventas.partials.hash_integridad', [
    'hash' => $cobros->hash_documento ?? null,
    'hashValido' => $hashValido ?? null,
])

<a class="btn btn-default" href="{{ route('cobro.index') }}">Volver</a>

@endsection
