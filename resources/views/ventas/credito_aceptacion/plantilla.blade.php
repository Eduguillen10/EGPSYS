<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compromiso de Pago Venta #{{ $venta->idventa }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
@include('ventas.partials.comprobante_nota_credito_css')
    </style>
</head>

<body>
<div class="invoice-wrap">
    <div class="invoice">
        <div class="invoice-inner">

            <div class="invoice-header">
                <div class="title-row">
                    <div class="invoice-title">
                        <h2>Compromiso de Pago</h2>
                        <div class="description">
                            Documento de respaldo para firma f&iacute;sica del cliente en venta a cr&eacute;dito.
                        </div>

                        <div class="submeta">
                            <span class="chip"><span>Factura:</span> <b>{{ $venta->nro_factura }}</b></span>
                            <span class="chip"><span>Venta:</span> <b>#{{ $venta->idventa }}</b></span>
                            <span class="chip"><span>Condici&oacute;n:</span> <b>{{ $venta->condicion }}</b></span>
                        </div>
                    </div>

                    <div class="logo-box">
                        <span class="badge-status">Documento CP</span>
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
                        <strong>{{ $venta->cliente }}</strong><br>
                        Documento: {{ $venta->cliente_documento }}<br>
                        Direcci&oacute;n: {{ $venta->cliente_direccion ?? '-' }}<br>
                        Tel&eacute;fono: {{ $venta->cliente_telefono ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="kvs">
                <div class="kv">
                    <div class="k">Factura</div>
                    <div class="v">{{ $venta->nro_factura }}</div>
                </div>

                <div class="kv">
                    <div class="k">Venta</div>
                    <div class="v">#{{ $venta->idventa }}</div>
                </div>

                <div class="kv">
                    <div class="k">Condicion</div>
                    <div class="v">{{ $venta->condicion }}</div>
                </div>

                <div class="kv">
                    <div class="k">Vencimiento</div>
                    <div class="v">{{ $cuenta?->fecha_vencimiento ? date('d/m/Y', strtotime($cuenta->fecha_vencimiento)) : '-' }}</div>
                </div>
            </div>

            <div class="amount-banner">
                <div>
                    <div class="label">Monto comprometido</div>
                    <div class="amount">Gs. {{ number_format($venta->totalventa, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="totals-grid">
                <div class="note-card">
                    <div class="note-head">
                        <h5>Texto de aceptaci&oacute;n</h5>
                        <span class="pill">Compromiso</span>
                    </div>
                    <div class="note-body" style="font-size:15px;line-height:1.65;">
                        {{ $textoAceptado }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="sum-head">
                        <h5>Datos para completar</h5>
                        <span class="pill purple">Firma</span>
                    </div>
                    <div class="sum-body">
                        <div class="sum-row">
                            <div class="label">Documento</div>
                            <div class="value">________________</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Telefono</div>
                            <div class="value">________________</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Fecha</div>
                            <div class="value">____ / ____ / ______</div>
                        </div>
                        <div class="grand-total">
                            <div class="label">Total</div>
                            <div class="value">{{ number_format($venta->totalventa, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:70px;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;">
                    <tr>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Firma del Cliente / Receptor</td>
                        <td style="width:10%;"></td>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Aclaraci&oacute;n</td>
                    </tr>
                </table>
            </div>

            <div class="footer-actions no-print">
                <button class="btn-pro btn-print" onclick="window.print()">Imprimir Compromiso</button>
                <a href="{{ route('venta.show', $venta->idventa) }}" class="btn-pro btn-ghost">Volver</a>
            </div>

        </div>
    </div>
</div>
</body>
</html>
