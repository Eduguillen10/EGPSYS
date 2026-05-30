<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Factura de Venta</title>

  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    :root{
      --bg:#0b1020;
      --paper:#ffffff;
      --ink:#101828;
      --muted:#667085;
      --line:#eaecf0;
      --soft:#f7f8fb;

      --brand1:#6d28d9;
      --brand2:#06b6d4;
      --brand3:#22c55e;

      --shadow: 0 18px 50px rgba(16,24,40,.16);
      --shadow2: 0 10px 30px rgba(16,24,40,.10);
      --radius: 18px;
    }

    *{ box-sizing: border-box; }

    body{
      padding: 28px 0;
      background:
        radial-gradient(1000px 500px at 15% 5%, rgba(109,40,217,.35), transparent 55%),
        radial-gradient(900px 520px at 85% 0%, rgba(6,182,212,.28), transparent 55%),
        radial-gradient(900px 600px at 85% 95%, rgba(34,197,94,.18), transparent 60%),
        linear-gradient(180deg, #070b16 0%, #0b1020 60%, #070b16 100%);
      color: var(--ink);
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,Helvetica,sans-serif;
    }

    .invoice-wrap{
      position: relative;
      max-width: 1160px;
      margin: 0 auto;
      padding: 0 14px;
    }

    .invoice{
      position: relative;
      background: var(--paper);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      border: 1px solid rgba(234,236,240,.9);
    }

    .invoice::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height: 140px;
      background: linear-gradient(135deg, var(--brand1) 0%, var(--brand2) 40%, var(--brand3) 100%);
      opacity: .95;
    }

    .invoice::after{
      content:"";
      position:absolute;
      inset:0;
      pointer-events:none;
      background-image:
        radial-gradient(circle at 12px 12px, rgba(255,255,255,.22) 2px, transparent 3px),
        radial-gradient(circle at 38px 38px, rgba(255,255,255,.14) 2px, transparent 3px);
      background-size: 64px 64px;
      opacity: .22;
      mix-blend-mode: soft-light;
    }

    .invoice-inner{
      position: relative;
      z-index: 1;
      padding: 22px 22px 18px;
    }

    .invoice-header{
      border-radius: 16px;
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.55);
      box-shadow: var(--shadow2);
      padding: 18px 18px 14px;
      margin-top: 54px;
    }

    .title-row{
      display:flex;
      gap: 16px;
      align-items:flex-start;
      justify-content: space-between;
      flex-wrap: wrap; /* pantalla OK */
    }

    .invoice-title h2{
      margin: 0;
      font-size: 22px;
      letter-spacing: .2px;
      color: var(--ink);
      font-weight: 800;
    }

    .submeta{
      margin-top: 8px;
      display:flex;
      gap: 10px;
      flex-wrap: wrap;
      color: var(--muted);
      font-size: 12px;
      font-weight: 600;
    }

    .chip{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: var(--soft);
      border: 1px solid var(--line);
      white-space: nowrap;
    }
    .chip b{ color: var(--ink); font-weight: 800; }

    .logo-box{
      display:flex;
      align-items:flex-start;
      gap: 10px;
      justify-content:flex-end;
      min-width: 220px;
    }

    .invoice-logo{
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 10px;
      box-shadow: 0 8px 18px rgba(16,24,40,.06);
    }
    .invoice-logo img{
      max-width: 170px;
      height: auto;
      display:block;
      object-fit: contain;
    }

    .badge-status{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      border-radius: 12px;
      padding: 10px 12px;
      background: linear-gradient(135deg, rgba(109,40,217,.10), rgba(6,182,212,.10));
      border: 1px dashed rgba(109,40,217,.35);
      color: var(--ink);
      font-weight: 800;
      font-size: 12px;
      white-space: nowrap;
    }

    .info-grid{
      margin-top: 14px;
      display:grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 14px;
    }

    .info-card{
      border-radius: 16px;
      border: 1px solid var(--line);
      background: #fff;
      box-shadow: 0 10px 25px rgba(16,24,40,.06);
      overflow:hidden;
    }

    .info-card .info-head{
      padding: 12px 14px;
      background: linear-gradient(135deg, rgba(109,40,217,.08), rgba(6,182,212,.06));
      border-bottom: 1px solid var(--line);
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
    }

    .info-card .info-head h5{
      margin: 0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: var(--muted);
      font-weight: 900;
    }

    .info-card .info-body{
      padding: 12px 14px 14px;
      color: var(--ink);
      line-height: 1.45;
      font-size: 14px;
    }

    .kvs{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      padding: 12px 14px 14px;
    }
    .kv{
      border: 1px solid var(--line);
      background: var(--soft);
      border-radius: 14px;
      padding: 10px 12px;
    }
    .kv .k{
      color: var(--muted);
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .7px;
      font-weight: 900;
      margin-bottom: 4px;
    }
    .kv .v{
      color: var(--ink);
      font-size: 14px;
      font-weight: 800;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .table-shell{
      margin-top: 14px;
      border-radius: 16px;
      border: 1px solid var(--line);
      overflow:hidden;
      box-shadow: 0 14px 30px rgba(16,24,40,.07);
      background: #fff;
    }

    .table-topbar{
      display:flex;
      align-items:center;
      justify-content: space-between;
      padding: 12px 14px;
      background: linear-gradient(135deg, rgba(109,40,217,.09), rgba(6,182,212,.06));
      border-bottom: 1px solid var(--line);
      gap: 10px;
      flex-wrap: wrap;
    }
    .table-topbar .left{
      display:flex;
      align-items:center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .pill{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 8px 10px;
      border-radius: 999px;
      border: 1px solid rgba(6,182,212,.25);
      background: rgba(6,182,212,.08);
      font-size: 12px;
      font-weight: 900;
      color: #055b69;
    }
    .pill.purple{
      border-color: rgba(109,40,217,.25);
      background: rgba(109,40,217,.08);
      color: #3b1c92;
    }

    .table{ margin-bottom: 0; }

    .table thead th{
      background: #0b1220 !important;
      color: #e6edf7 !important;
      border: 0 !important;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .6px;
      padding: 12px 10px;
      white-space: nowrap;
      position: sticky;
      top: 0;
      z-index: 2;
    }

    .table tbody td{
      border-top: 1px solid var(--line) !important;
      padding: 10px 10px;
      font-size: 13px;
      color: var(--ink);
      vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd){
      background-color: rgba(247,248,251,.7);
    }

    .table td:nth-child(1){ width: 70px; font-weight: 800; color: #111827; }
    .table td:nth-child(3),
    .table td:nth-child(4),
    .table td:nth-child(5),
    .table td:nth-child(6),
    .table td:nth-child(7),
    .table td:nth-child(8),
    .table td:nth-child(9),
    .table td:nth-child(10){
      text-align: right;
      white-space: nowrap;
      font-variant-numeric: tabular-nums;
    }

    .totals-grid{
      display:grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 14px;
      margin-top: 14px;
    }

    .note-card{
      border: 1px solid var(--line);
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 10px 25px rgba(16,24,40,.06);
      overflow:hidden;
    }
    .note-card .note-head{
      padding: 12px 14px;
      background: linear-gradient(135deg, rgba(34,197,94,.10), rgba(6,182,212,.06));
      border-bottom: 1px solid var(--line);
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
    }
    .note-card .note-head h5{
      margin:0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: var(--muted);
      font-weight: 900;
    }
    .note-card .note-body{
      padding: 12px 14px 14px;
      color: var(--ink);
      font-size: 13px;
      line-height: 1.55;
    }

    .summary-card{
      border-radius: 16px;
      border: 1px solid var(--line);
      background:
        radial-gradient(600px 220px at 20% 0%, rgba(109,40,217,.10), transparent 60%),
        radial-gradient(520px 240px at 100% 40%, rgba(6,182,212,.10), transparent 60%),
        #ffffff;
      box-shadow: 0 16px 36px rgba(16,24,40,.10);
      overflow:hidden;
    }
    .summary-card .sum-head{
      padding: 12px 14px;
      border-bottom: 1px solid var(--line);
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
    }
    .summary-card .sum-head h5{
      margin:0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: var(--muted);
      font-weight: 900;
    }

    .sum-body{ padding: 12px 14px 14px; }

    .sum-row{
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px dashed rgba(234,236,240,.95);
      font-size: 13px;
    }
    .sum-row:last-child{ border-bottom:none; padding-bottom: 4px; }

    .sum-row .label{ color: var(--muted); font-weight: 800; }
    .sum-row .value{ color: var(--ink); font-weight: 900; font-variant-numeric: tabular-nums; white-space: nowrap; }

    .grand-total{
      margin-top: 10px;
      padding: 12px 12px;
      border-radius: 14px;
      background: linear-gradient(135deg, rgba(109,40,217,.12), rgba(6,182,212,.12), rgba(34,197,94,.12));
      border: 1px solid rgba(109,40,217,.20);
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
    }
    .grand-total .label{
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
      font-weight: 900;
      color: #2b2f3a;
    }
    .grand-total .value{
      font-size: 18px;
      font-weight: 1000;
      color: #0f172a;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }

    .footer-actions{
      margin-top: 16px;
      display:flex;
      justify-content:center;
      gap: 10px;
      flex-wrap: wrap;
      padding-bottom: 6px;
    }

    .btn-pro{
      border: none;
      border-radius: 14px;
      padding: 10px 16px;
      font-weight: 900;
      letter-spacing: .2px;
      transition: transform .06s ease, filter .15s ease;
      box-shadow: 0 12px 26px rgba(16,24,40,.12);
    }
    .btn-pro:active{ transform: translateY(1px); }

    .btn-print{
      color:#fff;
      background: linear-gradient(135deg, var(--brand1), var(--brand2));
    }
    .btn-print:hover{ filter: brightness(.96); color:#fff; }

    .btn-ghost{
      color: var(--ink);
      background: #fff;
      border: 1px solid var(--line);
      box-shadow: 0 12px 26px rgba(16,24,40,.08);
    }
    .btn-ghost:hover{ filter: brightness(.98); }

    @media (max-width: 991px){
      .info-grid{ grid-template-columns: 1fr; }
      .totals-grid{ grid-template-columns: 1fr; }
      .logo-box{ min-width: 100%; justify-content:flex-start; }
      .invoice-header{ margin-top: 44px; }
    }

    /* =========================
       PRINT (A4) - 1 HOJA + LOGO AL COSTADO
       ========================= */
    @page{
      size: A4 portrait;
      margin: 8mm;
    }

    @media print{
      body{
        background:#fff !important;
        padding: 0 !important;
        margin: 0 !important;
      }

      /* quitar overlays */
      .invoice::before, .invoice::after{ display:none !important; }

      .invoice{
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        overflow: visible !important;

        /* para que entre en 1 hoja */
        transform: scale(0.97);
        transform-origin: top left;
        width: calc(100% / 0.97);
      }

      .invoice-inner{
        padding: 8px 8px 6px !important;
      }

      .invoice-header{
        margin-top: 0 !important;
        padding: 10px 10px 8px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        background:#fff !important;
      }

      /* FORZAR 2 COLUMNAS (texto izq / logo der) */
      .invoice-header .title-row{
        display:flex !important;
        flex-wrap: nowrap !important; /* CLAVE: evita que el logo baje */
        align-items:flex-start !important;
        justify-content: space-between !important;
        gap: 10px !important;
      }

      .invoice-header .invoice-title{
        flex: 1 1 auto !important;
        min-width: 0 !important;
      }

      .invoice-title h2{
        font-size: 15px !important;
        margin: 0 !important;
      }

      .invoice-header .submeta{
        margin-top: 6px !important;
        gap: 6px !important;
        font-size: 10px !important;
        flex-wrap: wrap !important;
      }
      .invoice-header .chip{
        padding: 4px 8px !important;
        border: 1px solid #ddd !important;
        background: #f6f7f9 !important;
        white-space: normal !important; /* permite envolver */
      }

      /* si el badge empuja, ocultarlo */
      .invoice-header .badge-status{
        display:none !important;
      }

      /* columna derecha fija para el logo */
      .invoice-header .logo-box{
        flex: 0 0 150px !important;
        min-width: 150px !important;
        justify-content: flex-end !important;
        align-items: flex-start !important;
        gap: 0 !important;
      }

      /* logo limpio (sin caja) */
      .invoice-header .invoice-logo{
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
      }
      .invoice-header .invoice-logo img{
        max-width: 115px !important;
        height: auto !important;
        display:block !important;
        object-fit: contain !important;
      }

      /* Mantener 2 columnas en print */
      .info-grid{
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        margin-top: 10px !important;
      }

      .info-card, .note-card, .summary-card, .table-shell{
        box-shadow: none !important;
      }

      .info-card{
        border-radius: 10px !important;
        border: 1px solid #ddd !important;
      }
      .info-card .info-head{
        padding: 8px 10px !important;
        background:#f6f7f9 !important;
        border-bottom: 1px solid #ddd !important;
      }
      .info-card .info-body{
        padding: 8px 10px !important;
        font-size: 11px !important;
        line-height: 1.25 !important;
      }

      .kvs{
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        padding: 8px 10px 10px !important;
      }
      .kv{
        padding: 7px 9px !important;
        border-radius: 10px !important;
        border: 1px solid #ddd !important;
        background:#f6f7f9 !important;
      }
      .kv .k{ font-size: 9px !important; }
      .kv .v{ font-size: 11px !important; }

      .table-shell{
        margin-top: 10px !important;
        border-radius: 10px !important;
        border: 1px solid #ddd !important;
      }
      .table-topbar{
        padding: 8px 10px !important;
        border-bottom: 1px solid #ddd !important;
        background:#f6f7f9 !important;
      }
      .pill{ font-size: 10px !important; padding: 6px 8px !important; }

      .table thead th{
        position: static !important;
        background: #111827 !important;
        color: #fff !important;
        font-size: 9px !important;
        padding: 6px 6px !important;
        letter-spacing: .4px !important;
      }
      .table tbody td{
        font-size: 10px !important;
        padding: 5px 6px !important;
        border-top: 1px solid #e5e7eb !important;
      }
      .table td:nth-child(2){
        white-space: normal !important;
      }

      tr, td, th{ page-break-inside: avoid !important; }
      .info-card, .table-shell, .note-card, .summary-card{
        break-inside: avoid !important;
        page-break-inside: avoid !important;
      }

      .totals-grid{
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        margin-top: 10px !important;
      }
      .note-card, .summary-card{
        border: 1px solid #ddd !important;
        border-radius: 10px !important;
      }
      .note-card .note-head,
      .summary-card .sum-head{
        padding: 8px 10px !important;
        border-bottom: 1px solid #ddd !important;
        background:#f6f7f9 !important;
      }
      .note-card .note-body{
        padding: 8px 10px 10px !important;
        font-size: 10px !important;
        line-height: 1.3 !important;
      }
      .sum-body{ padding: 8px 10px 10px !important; }
      .sum-row{
        padding: 5px 0 !important;
        font-size: 10px !important;
      }
      .grand-total{
        margin-top: 8px !important;
        padding: 8px 10px !important;
        border-radius: 10px !important;
        border: 1px solid #ddd !important;
        background:#f6f7f9 !important;
      }
      .grand-total .label{ font-size: 10px !important; }
      .grand-total .value{ font-size: 14px !important; }

      /* no imprimir botones */
      .footer-actions, .no-print{ display:none !important; }
    }
  </style>
</head>

<body>
  <div class="invoice-wrap">
    <div class="invoice">
      <div class="invoice-inner">

        <!-- HEADER -->
        <div class="invoice-header">
          <div class="title-row">
            <div class="invoice-title">
              <h2>Factura de Venta</h2>
              <div class="submeta">
                <span class="chip"><span>Factura:</span> <b>{{ $venta->nro_factura }}</b></span>
                <span class="chip"><span>Timbrado:</span> <b>{{ $venta->nro_timbrado }}</b></span>
                <span class="chip"><span>Válida:</span> <b>{{ date('d/m/Y', strtotime($venta->fecha_inicial)) }}</b> → <b>{{ date('d/m/Y', strtotime($venta->fecha_vencimiento)) }}</b></span>
              </div>
            </div>

            <div class="logo-box">
              <span class="badge-status">✅ Documento de Venta</span>
              <div class="invoice-logo">
                <img src="/img/TM Soluciones.jpg" alt="TM Soluciones" />
              </div>
            </div>
          </div>
        </div>

        <!-- INFO -->
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
              Teléfono: 0981181360
            </div>
          </div>

          <div class="info-card">
            <div class="info-head">
              <h5>Cliente</h5>
              <span class="pill">Datos del receptor</span>
            </div>
            <div class="info-body">
              <strong>{{ $venta->cliente }}</strong><br>
              {{ $venta->direccion }}<br>
              {{ $venta->ciudad }}<br>
              Teléfono: {{ $venta->telefono }}
            </div>
          </div>
        </div>

        <div class="info-card mt-3">
          <div class="info-head">
            <h5>Datos de la operación</h5>
            <span class="pill purple">Resumen</span>
          </div>
          <div class="kvs">
            <div class="kv">
              <div class="k">Fecha</div>
              <div class="v">{{ date('d/m/Y', strtotime($venta->fecha)) }}</div>
            </div>
            <div class="kv">
              <div class="k">Condición de venta</div>
              <div class="v">{{ $venta->condicion }}</div>
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-shell">
          <div class="table-topbar">
            <div class="left">
              <span class="pill purple">Detalle de ítems</span>
              <span class="pill">IVA / Gravadas / Exentas</span>
            </div>
            <div class="right text-muted" style="font-size:12px;font-weight:900;">
              Moneda: <span style="color:#0f172a;">Gs.</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Items</th>
                  <th>Descripción</th>
                  <th>Cantidad</th>
                  <th>Precio Unitario</th>
                  <th>IVA 10%</th>
                  <th>IVA 5%</th>
                  <th>Gravada 10%</th>
                  <th>Gravada 5%</th>
                  <th>Exenta</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($venta_detalle as $detalle)
                <tr>
                  <td>{{ $detalle->items }}</td>
                  <td>{{ $detalle->producto }}</td>
                  <td>{{ $detalle->cantidad }}</td>
                  <td>{{ number_format( $detalle->precio_venta, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->iva10, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->iva5, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->gravada10, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->gravada5, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->exenta, 0, ',', '.') }}</td>
                  <td>{{ number_format($detalle->totalitems, 0, ',', '.') }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- TOTALS -->
        <div class="totals-grid">
          <div class="note-card">
            <div class="note-head">
              <h5>Observaciones</h5>
              <span class="pill" style="border-color: rgba(245,158,11,.25); background: rgba(245,158,11,.10); color:#7a4a00;">Info</span>
            </div>
            <div class="note-body">
              • Gracias por su compra.<br>
              • Verifique los datos antes de archivar.<br>
              • Conserve esta factura para respaldo contable.
            </div>
          </div>

          <div class="summary-card">
            <div class="sum-head">
              <h5>Totales</h5>
              <span class="pill purple">Cierre</span>
            </div>
            <div class="sum-body">
              <div class="sum-row">
                <div class="label">Subtotal</div>
                <div class="value">{{ number_format($venta->totalventa, 0, ',', '.') }}</div>
              </div>
              <div class="sum-row">
                <div class="label">IVA (10%)</div>
                <div class="value">{{ number_format($venta->totaliva10, 0, ',', '.') }}</div>
              </div>
              <div class="sum-row">
                <div class="label">IVA (5%)</div>
                <div class="value">{{ number_format($venta->totaliva5, 0, ',', '.') }}</div>
              </div>
              <div class="sum-row">
                <div class="label">Gravada (10%)</div>
                <div class="value">{{ number_format($venta->totalgravada10, 0, ',', '.') }}</div>
              </div>
              <div class="sum-row">
                <div class="label">Gravada (5%)</div>
                <div class="value">{{ number_format($venta->totalgravada5, 0, ',', '.') }}</div>
              </div>
              <div class="sum-row">
                <div class="label">Exenta</div>
                <div class="value">{{ number_format($venta->totalexenta, 0, ',', '.') }}</div>
              </div>

              <div class="grand-total">
                <div class="label">Total</div>
                <div class="value">{{ number_format($venta->totalventa, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- ACTIONS (NO PRINT) -->
        <div class="footer-actions no-print">
          <button class="btn-pro btn-print" onclick="window.print()">Imprimir Factura</button>
          <button class="btn-pro btn-ghost" onclick="window.scrollTo({top:0, behavior:'smooth'})">Volver arriba</button>
        </div>

        @include('ventas.partials.hash_integridad', [
          'hash' => $venta->hash_documento ?? null,
          'hashValido' => $hashValido ?? null,
        ])

      </div>
    </div>
  </div>
</body>
</html>
