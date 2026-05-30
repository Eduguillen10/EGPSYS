<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Remision {{ $remision->nro_remision ?? '#' . $remision->idnota_remision_venta }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
@include('ventas.partials.comprobante_nota_credito_css')
    </style>
</head>

<body>
@php
    $destinatarioNombre = $venta ? $venta->cliente : ($remision->destinatario_nombre ?? '-');
    $destinatarioDocumento = $venta ? $venta->cliente_documento : ($remision->destinatario_documento ?? '-');
    $ventaLabel = $venta ? '#' . $venta->idventa : 'Sin venta asociada';
    $comprobanteTipo = $venta ? 'Factura' : 'Sin factura';
    $comprobanteNumero = $venta ? $venta->nro_factura : '-';
    $comprobanteTimbrado = $venta ? $venta->nro_timbrado : '-';
    $estadoNormalizado = strtoupper(trim((string) $remision->estado));
    $estado = in_array($estadoNormalizado, ['A', 'ANULADO', 'ANULADA'], true) ? 'Anulado' : ($remision->estado ?: 'Vigente');
    $totalItems = count($detalles);
    $totalCantidad = 0;

    foreach ($detalles as $detalle) {
        $totalCantidad += (float) $detalle->cantidad;
    }
@endphp

<div class="invoice-wrap">
    <div class="invoice">
        <div class="invoice-inner">

            <div class="invoice-header">
                <div class="title-row">
                    <div class="invoice-title">
                        <h2>Nota de Remisi&oacute;n</h2>
                        <div class="description">
                            Documento de respaldo para el traslado de mercader&iacute;a.
                        </div>

                        <div class="submeta">
                            <span class="chip"><span>Nro:</span> <b>{{ $remision->nro_remision ?? $remision->idnota_remision_venta }}</b></span>
                            <span class="chip"><span>Timbrado:</span> <b>{{ $remision->nro_timbrado_remision ?? '-' }}</b></span>
                            <span class="chip"><span>Estado:</span> <b>{{ $estado }}</b></span>
                        </div>
                    </div>

                    <div class="logo-box">
                        <span class="badge-status">Documento NR</span>
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
                        <h5>Destinatario</h5>
                        <span class="pill">Datos del receptor</span>
                    </div>
                    <div class="info-body">
                        <strong>{{ $destinatarioNombre }}</strong><br>
                        Documento: {{ $destinatarioDocumento }}<br>
                        Venta: {{ $ventaLabel }}<br>
                        Comprobante: {{ $comprobanteTipo }} {{ $comprobanteNumero }}
                    </div>
                </div>
            </div>

            <div class="kvs">
                <div class="kv">
                    <div class="k">Fecha emision</div>
                    <div class="v">{{ $remision->fecha_emision ? date('d/m/Y', strtotime($remision->fecha_emision)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Inicio traslado</div>
                    <div class="v">{{ $remision->fecha_inicio_traslado ? date('d/m/Y', strtotime($remision->fecha_inicio_traslado)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Fin traslado</div>
                    <div class="v">{{ $remision->fecha_fin_traslado ? date('d/m/Y', strtotime($remision->fecha_fin_traslado)) : '-' }}</div>
                </div>

                <div class="kv">
                    <div class="k">Timbrado comp.</div>
                    <div class="v">{{ $comprobanteTimbrado }}</div>
                </div>
            </div>

            <div class="amount-banner">
                <div>
                    <div class="label">Motivo de traslado</div>
                    <div class="amount">{{ $remision->motivo_traslado ?? 'Venta' }}</div>
                </div>
            </div>

            <div class="table-shell">
                <div class="table-topbar">
                    <div>
                        <span class="pill purple">Detalle de items</span>
                        <span class="pill">Mercader&iacute;a remitida</span>
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:900;">
                        Cantidad total: <span style="color:#0f172a;">{{ number_format($totalCantidad, 3, ',', '.') }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->items }}</td>
                                    <td>{{ $detalle->descripcion }}</td>
                                    <td class="right">{{ number_format($detalle->cantidad, 3, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="totals-grid">
                <div class="note-card">
                    <div class="note-head">
                        <h5>Traslado</h5>
                        <span class="pill">Origen y destino</span>
                    </div>
                    <div class="note-body">
                        <strong>Punto de partida:</strong> {{ $remision->punto_partida ?? '-' }}<br>
                        Ciudad: {{ $remision->ciudad_partida ?? '-' }} /
                        Departamento: {{ $remision->departamento_partida ?? '-' }}<br>
                        Origen interno: {{ $remision->sucursal_origen ?? '-' }} {{ $remision->deposito_origen ? '/ '.$remision->deposito_origen : '' }}
                        <br><br>
                        <strong>Punto de llegada:</strong> {{ $remision->punto_llegada ?? '-' }}<br>
                        Ciudad: {{ $remision->ciudad_llegada ?? '-' }} /
                        Departamento: {{ $remision->departamento_llegada ?? '-' }}<br>
                        Destino interno: {{ $remision->sucursal_destino ?? '-' }} {{ $remision->deposito_destino ? '/ '.$remision->deposito_destino : '' }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="sum-head">
                        <h5>Transporte</h5>
                        <span class="pill purple">Resumen</span>
                    </div>
                    <div class="sum-body">
                        <div class="sum-row">
                            <div class="label">Transportista</div>
                            <div class="value">{{ $remision->transportista_nombre ?? '-' }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Doc. transportista</div>
                            <div class="value">{{ $remision->transportista_documento ?? '-' }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Chofer</div>
                            <div class="value">{{ trim((string) $remision->chofer) ?: '-' }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">CI chofer</div>
                            <div class="value">{{ $remision->chofer_ci ?? '-' }}</div>
                        </div>
                        <div class="sum-row">
                            <div class="label">Vehiculo</div>
                            <div class="value">{{ $remision->nrochapa ?? '-' }}</div>
                        </div>
                        <div class="grand-total">
                            <div class="label">Items</div>
                            <div class="value">{{ $totalItems }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="note-card" style="margin-top:14px;">
                <div class="note-head">
                    <h5>Observaci&oacute;n</h5>
                    <span class="pill">Detalle</span>
                </div>
                <div class="note-body">
                    {{ $remision->observacion ?: 'Sin observacion registrada.' }}
                </div>
            </div>

            <div style="margin-top:55px;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;">
                    <tr>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Entrega / Transportista</td>
                        <td style="width:10%;"></td>
                        <td style="width:45%;text-align:center;border-top:1px solid #333;padding-top:8px;font-size:13px;font-weight:800;">Recibe conforme</td>
                    </tr>
                    <tr>
                        <td style="height:45px;"></td>
                        <td></td>
                        <td style="height:45px;text-align:center;font-size:12px;font-weight:700;"></td>
                    </tr>
                </table>
            </div>

            <div class="footer-actions no-print">
                <button class="btn-pro btn-print" onclick="window.print()">Imprimir Nota de Remision</button>
                <a href="{{ route('nota_remision_venta.show', $remision->idnota_remision_venta) }}" class="btn-pro btn-ghost">Volver</a>
            </div>

            @include('ventas.partials.hash_integridad', [
                'hash' => $remision->hash_documento ?? null,
                'hashValido' => $hashValido ?? null,
            ])

        </div>
    </div>
</div>
</body>
</html>
