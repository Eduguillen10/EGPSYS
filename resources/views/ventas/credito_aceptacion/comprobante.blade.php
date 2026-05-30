<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Aceptacion #{{ $aceptacion->idaceptacion_credito }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
@include('ventas.partials.comprobante_nota_credito_css')
    </style>
</head>

<body>
@php
    $estadoNormalizado = strtoupper(trim((string) $aceptacion->estado));
    $estado = in_array($estadoNormalizado, ['A', 'ANULADO', 'ANULADA'], true) ? 'Anulado' : ($aceptacion->estado ?: 'Aceptado');
@endphp

<div class="invoice-wrap">
    <div class="invoice">
        <div class="invoice-inner">

            <div class="invoice-header">
                <div class="title-row">
                    <div class="invoice-title">
                        <h2>Comprobante de Aceptaci&oacute;n de Cr&eacute;dito</h2>
                        <div class="description">
                            Respaldo de compromiso de pago de venta a cr&eacute;dito con firma f&iacute;sica.
                        </div>

                        <div class="submeta">
                            <span class="chip"><span>Factura:</span> <b>{{ $venta->nro_factura }}</b></span>
                            <span class="chip"><span>Venta:</span> <b>#{{ $venta->idventa }}</b></span>
                            <span class="chip"><span>Estado:</span> <b>{{ $estado }}</b></span>
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
                        Condici&oacute;n: {{ $aceptacion->condicion }}<br>
                        Vencimiento: {{ $aceptacion->fecha_vencimiento ? date('d/m/Y', strtotime($aceptacion->fecha_vencimiento)) : '-' }}
                    </div>
                </div>
            </div>

            <div class="kvs">
                <div class="kv">
                    <div class="k">Factura</div>
                    <div class="v">{{ $venta->nro_factura }}</div>
                </div>

                <div class="kv">
                    <div class="k">Metodo</div>
                    <div class="v">{{ $aceptacion->metodo_aceptacion }}</div>
                </div>

                <div class="kv">
                    <div class="k">Registrado</div>
                    <div class="v">{{ $aceptacion->created_at ? date('d/m/Y H:i', strtotime($aceptacion->created_at)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Usuario</div>
                    <div class="v">{{ $aceptacion->usuario }}</div>
                </div>
            </div>

            <div class="amount-banner">
                <div>
                    <div class="label">Monto aceptado</div>
                    <div class="amount">Gs. {{ number_format($aceptacion->monto, 0, ',', '.') }}</div>
                </div>

                <div class="status-pill">
                    {{ $estado === 'Anulado' ? 'Aceptacion anulada' : 'Aceptacion registrada' }}
                </div>
            </div>

            <div class="totals-grid">
                <div class="note-card">
                    <div class="note-head">
                        <h5>Texto aceptado</h5>
                        <span class="pill">Compromiso</span>
                    </div>
                    <div class="note-body" style="font-size:15px;line-height:1.65;">
                        {{ $aceptacion->texto_aceptado }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="sum-head">
                        <h5>Firma f&iacute;sica</h5>
                        <span class="pill purple">Resumen</span>
                    </div>
                    <div class="sum-body">
                        <div class="sum-row">
                            <div class="label">Firmado por</div>
                            <div class="value">{{ $aceptacion->recibido_por }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Documento</div>
                            <div class="value">{{ $aceptacion->documento_receptor }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Telefono</div>
                            <div class="value">{{ $aceptacion->telefono_receptor ?? '-' }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Relacion</div>
                            <div class="value">{{ $aceptacion->relacion_receptor ?? '-' }}</div>
                        </div>
                        <div class="grand-total">
                            <div class="label">Monto</div>
                            <div class="value">{{ number_format($aceptacion->monto, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="totals-grid">
                <div class="note-card">
                    <div class="note-head">
                        <h5>Observaci&oacute;n</h5>
                        <span class="pill">Detalle</span>
                    </div>
                    <div class="note-body">
                        {{ $aceptacion->observacion ?: 'Sin observacion registrada.' }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="sum-head">
                        <h5>Respaldo</h5>
                        <span class="pill purple">Archivo</span>
                    </div>
                    <div class="sum-body">
                        @if($aceptacion->archivo_respaldo)
                            Archivo adjunto registrado en el sistema.<br><br>
                            <a class="btn btn-sm btn-primary no-print" href="{{ asset($aceptacion->archivo_respaldo) }}" target="_blank">Ver respaldo</a>
                        @else
                            Sin archivo adjunto.
                        @endif
                    </div>
                </div>
            </div>

            <div style="margin-top:60px;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;">
                    <tr>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Firma archivada</td>
                        <td style="width:10%;"></td>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Responsable</td>
                    </tr>
                    <tr>
                        <td style="height:45px;text-align:center;font-size:12px;font-weight:700;">{{ $aceptacion->recibido_por }} {{ $aceptacion->documento_receptor ? '- '.$aceptacion->documento_receptor : '' }}</td>
                        <td></td>
                        <td style="height:45px;text-align:center;font-size:12px;font-weight:700;">{{ $aceptacion->usuario }}</td>
                    </tr>
                </table>
            </div>

            <div class="footer-actions no-print">
                <button class="btn-pro btn-print" onclick="window.print()">Imprimir Compromiso</button>
                <a href="{{ route('venta_credito_aceptacion.show', [$venta->idventa, $aceptacion->idaceptacion_credito]) }}" class="btn-pro btn-ghost">Volver</a>
            </div>

            @include('ventas.partials.hash_integridad', [
                'hash' => $aceptacion->hash_documento ?? null,
                'hashValido' => $hashValido ?? null,
            ])

        </div>
    </div>
</div>
</body>
</html>
