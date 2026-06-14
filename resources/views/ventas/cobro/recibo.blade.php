<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Cobro {{ $cobros->nro_recibo ?: '#' . $cobros->id_cobro }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --bg:#0b1020;
            --paper:#ffffff;
            --ink:#101828;
            --muted:#667085;
            --line:#eaecf0;
            --soft:#f7f8fb;

            --brand1:#0ea5e9;
            --brand2:#22c55e;
            --brand3:#14b8a6;

            --warning:#f59e0b;
            --danger:#ef4444;
            --success:#22c55e;

            --shadow: 0 18px 50px rgba(16,24,40,.16);
            --shadow2: 0 10px 30px rgba(16,24,40,.10);
            --radius: 18px;
        }

        *{ box-sizing:border-box; }

        body{
            padding:28px 0;
            background:
                radial-gradient(900px 480px at 12% 5%, rgba(14,165,233,.35), transparent 55%),
                radial-gradient(900px 520px at 88% 0%, rgba(34,197,94,.25), transparent 55%),
                radial-gradient(850px 580px at 85% 95%, rgba(20,184,166,.18), transparent 60%),
                linear-gradient(180deg, #070b16 0%, #0b1020 60%, #070b16 100%);
            color:var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,Helvetica,sans-serif;
        }

        .receipt-wrap{
            max-width:1080px;
            margin:0 auto;
            padding:0 14px;
        }

        .receipt{
            position:relative;
            background:var(--paper);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            overflow:hidden;
            border:1px solid rgba(234,236,240,.9);
        }

        .receipt::before{
            content:"";
            position:absolute;
            inset:0 0 auto 0;
            height:145px;
            background:linear-gradient(135deg, var(--brand1), var(--brand2), var(--brand3));
            opacity:.96;
        }

        .receipt::after{
            content:"";
            position:absolute;
            inset:0;
            pointer-events:none;
            background-image:
                radial-gradient(circle at 12px 12px, rgba(255,255,255,.22) 2px, transparent 3px),
                radial-gradient(circle at 40px 40px, rgba(255,255,255,.13) 2px, transparent 3px);
            background-size:64px 64px;
            opacity:.22;
            mix-blend-mode:soft-light;
        }

        .receipt-inner{
            position:relative;
            z-index:1;
            padding:22px 22px 18px;
        }

        .receipt-header{
            border-radius:16px;
            background:rgba(255,255,255,.94);
            backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.55);
            box-shadow:var(--shadow2);
            padding:18px;
            margin-top:54px;
        }

        .title-row{
            display:flex;
            gap:16px;
            align-items:flex-start;
            justify-content:space-between;
            flex-wrap:wrap;
        }

        .receipt-title h2{
            margin:0;
            font-size:23px;
            font-weight:900;
            letter-spacing:.2px;
            color:var(--ink);
        }

        .receipt-title .description{
            margin-top:6px;
            color:var(--muted);
            font-size:13px;
            font-weight:700;
        }

        .submeta{
            margin-top:10px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            color:var(--muted);
            font-size:12px;
            font-weight:700;
        }

        .chip{
            display:inline-flex;
            align-items:center;
            gap:7px;
            padding:6px 10px;
            border-radius:999px;
            background:var(--soft);
            border:1px solid var(--line);
            white-space:nowrap;
        }

        .chip b{
            color:var(--ink);
            font-weight:900;
        }

        .logo-box{
            display:flex;
            align-items:flex-start;
            gap:10px;
            justify-content:flex-end;
            min-width:220px;
        }

        .receipt-logo{
            background:#fff;
            border:1px solid var(--line);
            border-radius:14px;
            padding:10px;
            box-shadow:0 8px 18px rgba(16,24,40,.06);
        }

        .receipt-logo img{
            max-width:165px;
            height:auto;
            display:block;
            object-fit:contain;
        }

        .badge-status{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border-radius:12px;
            padding:10px 12px;
            background:linear-gradient(135deg, rgba(14,165,233,.12), rgba(34,197,94,.12));
            border:1px dashed rgba(14,165,233,.40);
            color:var(--ink);
            font-weight:900;
            font-size:12px;
            white-space:nowrap;
        }

        .info-grid{
            margin-top:14px;
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:14px;
        }

        .info-card{
            border-radius:16px;
            border:1px solid var(--line);
            background:#fff;
            box-shadow:0 10px 25px rgba(16,24,40,.06);
            overflow:hidden;
        }

        .info-card .info-head{
            padding:12px 14px;
            background:linear-gradient(135deg, rgba(14,165,233,.09), rgba(34,197,94,.06));
            border-bottom:1px solid var(--line);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .info-card .info-head h5{
            margin:0;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.8px;
            color:var(--muted);
            font-weight:900;
        }

        .info-body{
            padding:12px 14px 14px;
            color:var(--ink);
            line-height:1.45;
            font-size:14px;
        }

        .kvs{
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:10px;
            margin-top:14px;
        }

        .kv{
            border:1px solid var(--line);
            background:#fff;
            border-radius:14px;
            padding:10px 12px;
            box-shadow:0 8px 18px rgba(16,24,40,.05);
        }

        .kv .k{
            color:var(--muted);
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:.7px;
            font-weight:900;
            margin-bottom:4px;
        }

        .kv .v{
            color:var(--ink);
            font-size:14px;
            font-weight:900;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .amount-banner{
            margin-top:14px;
            border-radius:18px;
            background:
                radial-gradient(600px 220px at 10% 0%, rgba(14,165,233,.20), transparent 60%),
                radial-gradient(520px 240px at 100% 40%, rgba(34,197,94,.18), transparent 60%),
                linear-gradient(135deg, rgba(14,165,233,.08), rgba(34,197,94,.08));
            border:1px solid rgba(14,165,233,.25);
            box-shadow:0 16px 36px rgba(16,24,40,.10);
            padding:18px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            flex-wrap:wrap;
        }

        .amount-banner .label{
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.9px;
            color:var(--muted);
            font-weight:900;
        }

        .amount-banner .amount{
            font-size:30px;
            font-weight:1000;
            color:#0f172a;
            font-variant-numeric:tabular-nums;
            white-space:nowrap;
        }

        .amount-banner .status-pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 12px;
            border-radius:999px;
            background:#fff;
            border:1px solid var(--line);
            font-weight:900;
            font-size:12px;
            color:#0f172a;
            box-shadow:0 8px 16px rgba(16,24,40,.06);
        }

        .table-shell{
            margin-top:14px;
            border-radius:16px;
            border:1px solid var(--line);
            overflow:hidden;
            box-shadow:0 14px 30px rgba(16,24,40,.07);
            background:#fff;
        }

        .table-topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:12px 14px;
            background:linear-gradient(135deg, rgba(14,165,233,.09), rgba(34,197,94,.06));
            border-bottom:1px solid var(--line);
            gap:10px;
            flex-wrap:wrap;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 10px;
            border-radius:999px;
            border:1px solid rgba(14,165,233,.25);
            background:rgba(14,165,233,.08);
            font-size:12px;
            font-weight:900;
            color:#075985;
        }

        .pill.green{
            border-color:rgba(34,197,94,.25);
            background:rgba(34,197,94,.10);
            color:#166534;
        }

        .table{
            margin-bottom:0;
        }

        .table thead th{
            background:#0b1220 !important;
            color:#e6edf7 !important;
            border:0 !important;
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:.6px;
            padding:12px 10px;
            white-space:nowrap;
        }

        .table tbody td{
            border-top:1px solid var(--line) !important;
            padding:10px 10px;
            font-size:13px;
            color:var(--ink);
            vertical-align:middle;
        }

        .table-striped tbody tr:nth-of-type(odd){
            background-color:rgba(247,248,251,.7);
        }

        .right{
            text-align:right;
            font-variant-numeric:tabular-nums;
            white-space:nowrap;
        }

        .center{
            text-align:center;
        }

        .summary-grid{
            margin-top:14px;
            display:grid;
            grid-template-columns:1fr .85fr;
            gap:14px;
        }

        .note-card,
        .summary-card{
            border:1px solid var(--line);
            border-radius:16px;
            background:#fff;
            box-shadow:0 10px 25px rgba(16,24,40,.06);
            overflow:hidden;
        }

        .note-card .head,
        .summary-card .head{
            padding:12px 14px;
            background:linear-gradient(135deg, rgba(14,165,233,.08), rgba(34,197,94,.06));
            border-bottom:1px solid var(--line);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .note-card .head h5,
        .summary-card .head h5{
            margin:0;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.8px;
            color:var(--muted);
            font-weight:900;
        }

        .note-card .body,
        .summary-card .body{
            padding:12px 14px 14px;
            font-size:13px;
            line-height:1.55;
        }

        .sum-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            padding:8px 0;
            border-bottom:1px dashed rgba(234,236,240,.95);
            font-size:13px;
        }

        .sum-row:last-child{
            border-bottom:none;
        }

        .sum-row .label{
            color:var(--muted);
            font-weight:800;
        }

        .sum-row .value{
            color:var(--ink);
            font-weight:900;
            font-variant-numeric:tabular-nums;
            white-space:nowrap;
        }

        .signatures{
            margin-top:30px;
            display:flex;
            justify-content:space-between;
            gap:18px;
        }

        .signature{
            width:42%;
            text-align:center;
            border-top:1px solid #333;
            padding-top:7px;
            font-size:12px;
            font-weight:800;
            color:#344054;
        }

        .footer-actions{
            margin-top:16px;
            display:flex;
            justify-content:center;
            gap:10px;
            flex-wrap:wrap;
            padding-bottom:6px;
        }

        .btn-pro{
            border:none;
            border-radius:14px;
            padding:10px 16px;
            font-weight:900;
            letter-spacing:.2px;
            transition:transform .06s ease, filter .15s ease;
            box-shadow:0 12px 26px rgba(16,24,40,.12);
        }

        .btn-pro:active{
            transform:translateY(1px);
        }

        .btn-print{
            color:#fff;
            background:linear-gradient(135deg, var(--brand1), var(--brand2));
        }

        .btn-print:hover{
            filter:brightness(.96);
            color:#fff;
        }

        .btn-ghost{
            color:var(--ink);
            background:#fff;
            border:1px solid var(--line);
            box-shadow:0 12px 26px rgba(16,24,40,.08);
        }

        @media(max-width:991px){
            .info-grid,
            .summary-grid{
                grid-template-columns:1fr;
            }

            .kvs{
                grid-template-columns:1fr 1fr;
            }

            .logo-box{
                min-width:100%;
                justify-content:flex-start;
            }

            .receipt-header{
                margin-top:44px;
            }
        }

        @page{
            size:A4 portrait;
            margin:8mm;
        }

        @media print{
            body{
                background:#fff !important;
                padding:0 !important;
                margin:0 !important;
            }

            .receipt::before,
            .receipt::after{
                display:none !important;
            }

            .receipt{
                border:0 !important;
                box-shadow:none !important;
                border-radius:0 !important;
                overflow:visible !important;
                transform:scale(.97);
                transform-origin:top left;
                width:calc(100% / .97);
            }

            .receipt-inner{
                padding:8px 8px 6px !important;
            }

            .receipt-header{
                margin-top:0 !important;
                padding:10px !important;
                border-radius:10px !important;
                box-shadow:none !important;
                border:1px solid #ddd !important;
                background:#fff !important;
            }

            .title-row{
                flex-wrap:nowrap !important;
            }

            .receipt-title h2{
                font-size:16px !important;
            }

            .receipt-title .description{
                font-size:10px !important;
            }

            .submeta{
                font-size:10px !important;
                gap:6px !important;
            }

            .chip{
                padding:4px 8px !important;
                background:#f6f7f9 !important;
                border:1px solid #ddd !important;
                white-space:normal !important;
            }

            .badge-status{
                display:none !important;
            }

            .logo-box{
                flex:0 0 145px !important;
                min-width:145px !important;
                justify-content:flex-end !important;
            }

            .receipt-logo{
                padding:0 !important;
                border:0 !important;
                box-shadow:none !important;
                background:transparent !important;
            }

            .receipt-logo img{
                max-width:110px !important;
            }

            .info-grid{
                grid-template-columns:1fr 1fr !important;
                gap:8px !important;
                margin-top:10px !important;
            }

            .kvs{
                grid-template-columns:repeat(4, 1fr) !important;
                gap:8px !important;
                margin-top:10px !important;
            }

            .info-card,
            .kv,
            .table-shell,
            .note-card,
            .summary-card{
                box-shadow:none !important;
                border:1px solid #ddd !important;
                border-radius:10px !important;
            }

            .info-card .info-head,
            .table-topbar,
            .note-card .head,
            .summary-card .head{
                padding:8px 10px !important;
                background:#f6f7f9 !important;
                border-bottom:1px solid #ddd !important;
            }

            .info-body,
            .note-card .body,
            .summary-card .body{
                padding:8px 10px !important;
                font-size:10px !important;
                line-height:1.3 !important;
            }

            .kv{
                padding:7px 9px !important;
                background:#f6f7f9 !important;
            }

            .kv .k{
                font-size:9px !important;
            }

            .kv .v{
                font-size:10px !important;
            }

            .amount-banner{
                margin-top:10px !important;
                padding:10px !important;
                border-radius:10px !important;
                box-shadow:none !important;
                background:#f6f7f9 !important;
                border:1px solid #ddd !important;
            }

            .amount-banner .amount{
                font-size:18px !important;
            }

            .amount-banner .label{
                font-size:10px !important;
            }

            .table-shell{
                margin-top:10px !important;
            }

            .table thead th{
                background:#111827 !important;
                color:#fff !important;
                font-size:9px !important;
                padding:6px 6px !important;
            }

            .table tbody td{
                font-size:10px !important;
                padding:5px 6px !important;
            }

            .summary-grid{
                grid-template-columns:1fr 1fr !important;
                gap:8px !important;
                margin-top:10px !important;
            }

            .sum-row{
                padding:5px 0 !important;
                font-size:10px !important;
            }

            .signatures{
                margin-top:22px !important;
            }

            .signature{
                font-size:10px !important;
            }

            .footer-actions,
            .no-print{
                display:none !important;
            }

            tr, td, th{
                page-break-inside:avoid !important;
            }
        }
    </style>
</head>

<body>
@php
    $totalFactura = 0;
    $saldoActual = 0;
    $condicionGeneral = '-';

    foreach($cobrodetalle as $det){
        $totalFactura += (int) $det->montoventa;
        $saldoActual += (int) ($det->saldo ?? 0);
        $condicionGeneral = $det->condicion ?? $condicionGeneral;
    }

    $estadoPago = $saldoActual <= 0 ? 'Pago total' : 'Pago parcial';
@endphp

<div class="receipt-wrap">
    <div class="receipt">
        <div class="receipt-inner">

            <div class="receipt-header">
                <div class="title-row">
                    <div class="receipt-title">
                        <h2>Recibo de Cobro</h2>
                        <div class="description">
                            Constancia de pago aplicado a factura/s de venta.
                        </div>

                        <div class="submeta">
                            <span class="chip"><span>Recibo:</span> <b>{{ $cobros->nro_recibo ?: '#' . $cobros->id_cobro }}</b></span>
                            <span class="chip"><span>Fecha:</span> <b>{{ \Carbon\Carbon::parse($cobros->fecha_recibo ?: $cobros->fecha_cobro)->format('d/m/Y') }}</b></span>
                            @php
                                $estadoCobro = trim((string) $cobros->cobro_estado);
                                $estadoCobro = ['R' => 'Realizado', 'P' => 'Pendiente', 'A' => 'Anulado'][strtoupper($estadoCobro)] ?? $estadoCobro;
                            @endphp
                            <span class="chip"><span>Estado:</span> <b>{{ $estadoCobro }}</b></span>
                            <span class="chip"><span>Condición:</span> <b>{{ $condicionGeneral }}</b></span>
                        </div>
                    </div>

                    <div class="logo-box">
                        <span class="badge-status">✅ Constancia de Pago</span>
                        <div class="receipt-logo">
                            <img src="/img/TM Soluciones.jpg" alt="TM Soluciones">
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-head">
                        <h5>Emisor</h5>
                        <span class="pill green">TecnoMaster</span>
                    </div>
                    <div class="info-body">
                        <strong>TecnoMaster Soluciones</strong><br>
                        29 de Septiembre c/guyara campana<br>
                        Luque<br>
                        Teléfono: 0981181360
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-head">
                        <h5>Cliente</h5>
                        <span class="pill">Receptor del recibo</span>
                    </div>
                    <div class="info-body">
                        <strong>{{ $cobros->cliente }}</strong><br>
                        Documento: {{ $cobros->num_documento }}<br>
                        Sucursal: {{ $cobros->sucursal }}<br>
                        Caja: {{ $cobros->caja }} | Apertura: {{ $cobros->idapertura }}
                    </div>
                </div>
            </div>

            <div class="kvs">
                <div class="kv">
                    <div class="k">Usuario</div>
                    <div class="v">{{ $cobros->usuario }}</div>
                </div>

                <div class="kv">
                    <div class="k">Total Factura/s</div>
                    <div class="v">{{ number_format($totalFactura, 0, ',', '.') }}</div>
                </div>

                <div class="kv">
                    <div class="k">Saldo Actual</div>
                    <div class="v">{{ number_format($saldoActual, 0, ',', '.') }}</div>
                </div>

                <div class="kv">
                    <div class="k">Tipo de Pago</div>
                    <div class="v">{{ $estadoPago }}</div>
                </div>
            </div>

            <div class="amount-banner">
                <div>
                    <div class="label">Monto recibido en este cobro</div>
                    <div class="amount">Gs. {{ number_format($totalPagado, 0, ',', '.') }}</div>
                </div>

                <div class="status-pill">
                    {{ $saldoActual <= 0 ? 'Cuenta saldada' : 'Saldo pendiente: Gs. ' . number_format($saldoActual, 0, ',', '.') }}
                </div>
            </div>

            <div class="table-shell">
                <div class="table-topbar">
                    <div>
                        <span class="pill">Factura/s asociada/s</span>
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:900;">
                        Moneda: <span style="color:#0f172a;">Gs.</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Factura</th>
                                <th>Condición</th>
                                <th>Total Factura</th>
                                <th>Monto Cobrado</th>
                                <th>Saldo Actual</th>
                                <th>Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cobrodetalle as $det)
                                <tr>
                                    <td>{{ $det->items }}</td>
                                    <td>{{ $det->nro_factura }}</td>
                                    <td>{{ $det->condicion }}</td>
                                    <td class="right">{{ number_format($det->montoventa, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->monto_detcobro, 0, ',', '.') }}</td>
                                    <td class="right">{{ number_format($det->saldo ?? 0, 0, ',', '.') }}</td>
                                    <td class="center">
                                        {{ $det->fecha_vencimiento ? \Carbon\Carbon::parse($det->fecha_vencimiento)->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-shell">
                <div class="table-topbar">
                    <div>
                        <span class="pill green">Forma/s de Cobro</span>
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:900;">
                        Detalle del pago recibido
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Forma</th>
                                <th>Entidad</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Vencimiento</th>
                                <th>Monto</th>
                                <th>Recibido</th>
                                <th>Vuelto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formacobrodetalle as $fc)
                                <tr>
                                    <td>{{ $fc->items }}</td>
                                    <td>{{ $fc->formacobro }}</td>
                                    <td>{{ $fc->banco ?? '-' }}</td>
                                    <td>{{ $fc->documento ?? '-' }}</td>
                                    <td class="center">{{ $fc->fecha ? \Carbon\Carbon::parse($fc->fecha)->format('d/m/Y') : '-' }}</td>
                                    <td class="center">{{ $fc->fecha_vencimiento ? \Carbon\Carbon::parse($fc->fecha_vencimiento)->format('d/m/Y') : '-' }}</td>
                                    <td class="right">{{ number_format($fc->monto_detformacobro, 0, ',', '.') }}</td>
                                    <td class="right">{{ $fc->monto_recibido !== null ? number_format($fc->monto_recibido, 0, ',', '.') : '-' }}</td>
                                    <td class="right">{{ $fc->vuelto !== null ? number_format($fc->vuelto, 0, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="summary-grid">
                <div class="note-card">
                    <div class="head">
                        <h5>Concepto</h5>
                        <span class="pill">Detalle</span>
                    </div>
                    <div class="body">
                        Recibimos del cliente la suma de
                        <strong>Gs. {{ number_format($totalPagado, 0, ',', '.') }}</strong>,
                        en concepto de pago aplicado a la/s factura/s detallada/s en este recibo.
                        <br><br>
                        Este documento sirve como constancia del cobro registrado en el sistema.
                    </div>
                </div>

                <div class="summary-card">
                    <div class="head">
                        <h5>Resumen</h5>
                        <span class="pill green">Cobro</span>
                    </div>
                    <div class="body">
                        <div class="sum-row">
                            <div class="label">Total Factura/s</div>
                            <div class="value">{{ number_format($totalFactura, 0, ',', '.') }}</div>
                        </div>

                        <div class="sum-row">
                            <div class="label">Monto Cobrado</div>
                            <div class="value">{{ number_format($totalPagado, 0, ',', '.') }}</div>
                        </div>

                        <div class="sum-row">
                            <div class="label">Saldo Actual</div>
                            <div class="value">{{ number_format($saldoActual, 0, ',', '.') }}</div>
                        </div>

                        <div class="sum-row">
                            <div class="label">Situación</div>
                            <div class="value">{{ $saldoActual <= 0 ? 'Saldado' : 'Pendiente' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="signatures">
                <div class="signature">Firma Cajero</div>
                <div class="signature">Firma Cliente</div>
            </div>

            <div class="footer-actions no-print">
                <button class="btn-pro btn-print" onclick="window.print()">Imprimir Recibo</button>
                <a href="{{ route('cobro.show', $cobros->id_cobro) }}" class="btn-pro btn-ghost">Volver</a>
            </div>

            @include('ventas.partials.hash_integridad', [
                'hash' => $cobros->hash_documento ?? null,
                'hashValido' => $hashValido ?? null,
            ])

        </div>
    </div>
</div>

<script>
    window.print();
</script>

</body>
</html>
