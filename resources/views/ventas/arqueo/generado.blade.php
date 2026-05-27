@extends('layouts.listar')

@section('contenido')

<div class="row justify-content-center">
    <div class="col-12 text-center mb-4">
        <h2>Arqueo de Caja - {{ $tipo }}</h2>
    </div>

    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <p style="font-size: 16px; margin-bottom: 0;">
                    <b>Fecha Apertura:</b> {{ date("d/m/Y", strtotime($datosArqueo->fecha_apertura)) }} &nbsp;&nbsp;&nbsp;
                    <b>Caja:</b> {{ $datosArqueo->caja }} &nbsp;&nbsp;&nbsp;
                    <b>Sucursal:</b> {{ $datosArqueo->sucursal }} &nbsp;&nbsp;&nbsp;
                    <b>N° Apertura:</b> {{ $datosArqueo->idapertura }} &nbsp;&nbsp;&nbsp;
                    <b>Estado:</b> {{ $datosArqueo->estado }}
                </p>
            </div>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col-lg-12">
        <h4>Detalle por Forma de Cobro (sin Anulados)</h4>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#ffd966">
                    <th>#</th>
                    <th>Forma de Cobro</th>
                    <th style="text-align:right;">Total</th>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @forelse($porForma as $row)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $row->formacobro }}</td>
                            <td style="text-align:right;">{{ number_format($row->total_cobrado, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay cobros para esta apertura.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><b>Subtotal Cobrado</b></td>
                        <td style="text-align:right;"><b>{{ number_format($subtotalCobrado, 0, ',', '.') }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>Devoluciones por Anulacion</h4>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#f2dede">
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cobro</th>
                    <th>Venta</th>
                    <th>Factura</th>
                    <th>Forma de Cobro</th>
                    <th>Referencia</th>
                    <th>Usuario</th>
                    <th style="text-align:right;">Monto</th>
                </thead>
                <tbody>
                    @php $j=1; @endphp
                    @forelse($devoluciones as $dev)
                        <tr>
                            <td>{{ $j++ }}</td>
                            <td>{{ date('d/m/Y', strtotime($dev->fecha_cobro)) }}</td>
                            <td>{{ $dev->id_cobro }}</td>
                            <td>{{ $dev->idventa ?? 'Sin venta' }}</td>
                            <td>{{ $dev->nro_factura ?? 'Sin factura' }}</td>
                            <td>{{ $dev->formacobro ?? 'Sin forma' }}</td>
                            <td>{{ $dev->documento ?? 'Sin referencia' }}</td>
                            <td>{{ $dev->usuario }}</td>
                            <td style="text-align:right;">{{ number_format($dev->monto_devolucion, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay devoluciones por anulacion en esta apertura.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8"><b>Total Devoluciones</b></td>
                        <td style="text-align:right;"><b>{{ number_format($totalDevoluciones, 0, ',', '.') }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>Totales</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#A9D0F5">
                    <th style="text-align:right;">Monto Apertura</th>
                    <th style="text-align:right;">Subtotal Cobrado</th>
                    <th style="text-align:right;">Monto Cierre (guardado)</th>
                    <th style="text-align:right;">Total (Apertura + Cobrado)</th>
                    <th style="text-align:right;">Recaudación a Depositar</th>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:right;">{{ number_format($montoApertura, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($subtotalCobrado, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($montoCierre, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($subtotalCobrado, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row no-print" style="margin-bottom:12px;">
    <div class="col-lg-12 text-right">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> Imprimir
        </button>
    </div>
</div>

@endsection
