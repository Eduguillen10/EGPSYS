<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante Nota de Debito #{{ $nota_debitov->idnota_debitov }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
@include('ventas.partials.comprobante_nota_credito_css')
    </style>
</head>

<body>
@php
    $estadoNormalizado = strtoupper(trim((string) $nota_debitov->estado));
    $estado = in_array($estadoNormalizado, ['A', 'ANULADO', 'ANULADA'], true) ? 'Anulado' : 'Realizado';
    $total = 0;
    $totalIva10 = 0;
    $totalIva5 = 0;
    $totalExenta = 0;
    $totalGravada10 = 0;
    $totalGravada5 = 0;

    foreach ($detalles as $det) {
        $total += (int) $det->totalitems;
        $totalIva10 += (int) $det->iva10;
        $totalIva5 += (int) $det->iva5;
        $totalExenta += (int) $det->exenta;
        $totalGravada10 += (int) $det->gravada10;
        $totalGravada5 += (int) $det->gravada5;
    }
@endphp

<div class="invoice-wrap">
    <div class="invoice">
        <div class="invoice-inner">

            <div class="invoice-header">
                <div class="title-row">
                    <div class="invoice-title">
                        <h2>Comprobante de Nota de D&eacute;bito</h2>
                        <div class="description">
                            Documento de respaldo de la nota de d&eacute;bito aplicada a una venta.
                        </div>

                        <div class="submeta">
                            <span class="chip"><span>Nro:</span> <b>{{ $nota_debitov->nro_nota_debito ?? ('#' . $nota_debitov->idnota_debitov) }}</b></span>
                            <span class="chip"><span>Nro. doc:</span> <b>{{ $nota_debitov->nro_factura }}</b></span>
                            <span class="chip"><span>Timbrado:</span> <b>{{ $nota_debitov->timbrado }}</b></span>
                            <span class="chip"><span>Estado:</span> <b>{{ $estado }}</b></span>
                        </div>
                    </div>

                    <div class="logo-box">
                        <span class="badge-status">Documento ND</span>
                        <div class="invoice-logo">
                            <img src="/img/TM Soluciones.jpg" alt="TM Soluciones">
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-head">
                        <h5>Emisor</h5>
                        <span class="pill purple">TecnoMaster</span>
                    </div>
                    <div class="info-body">
                        <strong>TecnoMaster Soluciones</strong><br>
                        29 de Septiembre c/guyara campana<br>
                        Luque<br>
                        Tel&eacute;fono: 0981181360
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-head">
                        <h5>Cliente</h5>
                        <span class="pill">Datos del receptor</span>
                    </div>
                    <div class="info-body">
                        <strong>{{ $nota_debitov->cliente }}</strong><br>
                        Documento: {{ $nota_debitov->num_documento }}<br>
                        Sucursal: {{ $nota_debitov->sucursal }}<br>
                        Dep&oacute;sito: {{ $nota_debitov->deposito }}
                    </div>
                </div>
            </div>

            <div class="kvs">
                <div class="kv">
                    <div class="k">Fecha emision</div>
                    <div class="v">{{ $nota_debitov->fecha_registro ? date('d/m/Y', strtotime($nota_debitov->fecha_registro)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Fecha factura</div>
                    <div class="v">{{ $nota_debitov->fecha_factura ? date('d/m/Y', strtotime($nota_debitov->fecha_factura)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Venta afectada</div>
                    <div class="v">{{ $nota_debitov->idventa }}</div>
                </div>

                <div class="kv">
                    <div class="k">Condicion</div>
                    <div class="v">{{ $nota_debitov->condicion ?? '-' }}</div>
                </div>
            </div>

            <div class="amount-banner">
                <div>
                    <div class="label">Monto agregado por esta nota</div>
                    <div class="amount">Gs. {{ number_format($total, 0, ',', '.') }}</div>
                </div>

                <div class="status-pill">
                    {{ $estado === 'Anulado' ? 'Nota anulada' : 'Aplicada a cuenta de cliente' }}
                </div>
            </div>

            <div class="table-shell">
                <div class="table-topbar">
                    <div>
                        <span class="pill purple">Detalle de items</span>
                        <span class="pill">IVA / Gravadas / Exentas</span>
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:900;">
                        Moneda: <span style="color:#0f172a;">Gs.</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>IVA 10%</th>
                                <th>IVA 5%</th>
                                <th>Gravada 10%</th>
                                <th>Gravada 5%</th>
                                <th>Exenta</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $det)
                                <tr>
                                    <td>{{ $det->producto }}</td>
                                    <td class="right">{{ \App\Helpers\NumberFormatter::cantidad($det->cantidad) }}</td>
                                    <td class="right">{{ number_format($det->precio_venta, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->iva10, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->iva5, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->gravada10, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->gravada5, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->exenta, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->totalitems, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="totals-grid">
                <div class="note-card">
                    <div class="note-head">
                        <h5>Concepto</h5>
                        <span class="pill">Detalle</span>
                    </div>
                    <div class="note-body">
                        {{ $nota_debitov->concepto ?: 'Nota de debito aplicada a la factura seleccionada.' }}
                        <br><br>
                        Este comprobante respalda el aumento del importe de la venta afectada.
                    </div>
                </div>

                <div class="summary-card">
                    <div class="sum-head">
                        <h5>Totales</h5>
                        <span class="pill purple">Resumen</span>
                    </div>
                    <div class="sum-body">
                        <div class="sum-row">
                            <div class="label">Subtotal</div>
                            <div class="value">{{ number_format($total, 0, ',', '.') }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">IVA 10%</div>
                            <div class="value">{{ number_format($totalIva10, 0, ',', '.') }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">IVA 5%</div>
                            <div class="value">{{ number_format($totalIva5, 0, ',', '.') }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Gravada 10%</div>
                            <div class="value">{{ number_format($totalGravada10, 0, ',', '.') }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Gravada 5%</div>
                            <div class="value">{{ number_format($totalGravada5, 0, ',', '.') }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Exenta</div>
                            <div class="value">{{ number_format($totalExenta, 0, ',', '.') }}</div>
                        </div>
                        <div class="grand-total">
                            <div class="label">Total ND</div>
                            <div class="value">{{ number_format($total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-actions no-print">
                <button class="btn-pro btn-print" onclick="window.print()">Imprimir Nota de Debito</button>
                <a href="{{ route('nota_debitov.show', $nota_debitov->idnota_debitov) }}" class="btn-pro btn-ghost">Volver</a>
            </div>

            @include('ventas.partials.hash_integridad', [
                'hash' => $nota_debitov->hash_documento ?? null,
                'hashValido' => $hashValido ?? null,
            ])

        </div>
    </div>
</div>
</body>
</html>
