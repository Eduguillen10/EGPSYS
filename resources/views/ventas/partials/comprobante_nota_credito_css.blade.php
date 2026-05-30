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
            --shadow:0 18px 50px rgba(16,24,40,.16);
            --shadow2:0 10px 30px rgba(16,24,40,.10);
            --radius:18px;
        }

        *{ box-sizing:border-box; }

        body{
            padding:28px 0;
            background:
                radial-gradient(1000px 500px at 15% 5%, rgba(109,40,217,.35), transparent 55%),
                radial-gradient(900px 520px at 85% 0%, rgba(6,182,212,.28), transparent 55%),
                radial-gradient(900px 600px at 85% 95%, rgba(34,197,94,.18), transparent 60%),
                linear-gradient(180deg, #070b16 0%, #0b1020 60%, #070b16 100%);
            color:var(--ink);
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,Helvetica,sans-serif;
        }

        .invoice-wrap{
            position:relative;
            max-width:1160px;
            margin:0 auto;
            padding:0 14px;
        }

        .invoice{
            position:relative;
            background:var(--paper);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            overflow:hidden;
            border:1px solid rgba(234,236,240,.9);
        }

        .invoice::before{
            content:"";
            position:absolute;
            inset:0 0 auto 0;
            height:140px;
            background:linear-gradient(135deg, var(--brand1) 0%, var(--brand2) 40%, var(--brand3) 100%);
            opacity:.95;
        }

        .invoice::after{
            content:"";
            position:absolute;
            inset:0;
            pointer-events:none;
            background-image:
                radial-gradient(circle at 12px 12px, rgba(255,255,255,.22) 2px, transparent 3px),
                radial-gradient(circle at 38px 38px, rgba(255,255,255,.14) 2px, transparent 3px);
            background-size:64px 64px;
            opacity:.22;
            mix-blend-mode:soft-light;
        }

        .invoice-inner{
            position:relative;
            z-index:1;
            padding:22px 22px 18px;
        }

        .invoice-header{
            border-radius:16px;
            background:rgba(255,255,255,.92);
            backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.55);
            box-shadow:var(--shadow2);
            padding:18px 18px 14px;
            margin-top:54px;
        }

        .title-row{
            display:flex;
            gap:16px;
            align-items:flex-start;
            justify-content:space-between;
            flex-wrap:wrap;
        }

        .invoice-title h2{
            margin:0;
            font-size:22px;
            letter-spacing:.2px;
            color:var(--ink);
            font-weight:800;
        }

        .invoice-title .description{
            margin-top:6px;
            color:var(--muted);
            font-size:13px;
            font-weight:700;
        }

        .submeta{
            margin-top:8px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            color:var(--muted);
            font-size:12px;
            font-weight:600;
        }

        .chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:6px 10px;
            border-radius:999px;
            background:var(--soft);
            border:1px solid var(--line);
            white-space:nowrap;
        }

        .chip b{ color:var(--ink); font-weight:800; }

        .logo-box{
            display:flex;
            align-items:flex-start;
            gap:10px;
            justify-content:flex-end;
            min-width:220px;
        }

        .invoice-logo{
            background:#fff;
            border:1px solid var(--line);
            border-radius:14px;
            padding:10px;
            box-shadow:0 8px 18px rgba(16,24,40,.06);
        }

        .invoice-logo img{
            max-width:170px;
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
            background:linear-gradient(135deg, rgba(109,40,217,.10), rgba(6,182,212,.10));
            border:1px dashed rgba(109,40,217,.35);
            color:var(--ink);
            font-weight:800;
            font-size:12px;
            white-space:nowrap;
        }

        .info-grid{
            margin-top:14px;
            display:grid;
            grid-template-columns:1.2fr 1fr;
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
            background:linear-gradient(135deg, rgba(109,40,217,.08), rgba(6,182,212,.06));
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
                radial-gradient(600px 220px at 10% 0%, rgba(109,40,217,.20), transparent 60%),
                radial-gradient(520px 240px at 100% 40%, rgba(6,182,212,.18), transparent 60%),
                linear-gradient(135deg, rgba(109,40,217,.08), rgba(6,182,212,.08));
            border:1px solid rgba(109,40,217,.25);
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
            background:linear-gradient(135deg, rgba(109,40,217,.09), rgba(6,182,212,.06));
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
            border:1px solid rgba(6,182,212,.25);
            background:rgba(6,182,212,.08);
            font-size:12px;
            font-weight:900;
            color:#055b69;
        }

        .pill.purple{
            border-color:rgba(109,40,217,.25);
            background:rgba(109,40,217,.08);
            color:#3b1c92;
        }

        .table{ margin-bottom:0; }

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

        .totals-grid{
            display:grid;
            grid-template-columns:1.2fr .8fr;
            gap:14px;
            margin-top:14px;
        }

        .note-card,
        .summary-card{
            border:1px solid var(--line);
            border-radius:16px;
            background:#fff;
            box-shadow:0 10px 25px rgba(16,24,40,.06);
            overflow:hidden;
        }

        .note-card .note-head,
        .summary-card .sum-head{
            padding:12px 14px;
            background:linear-gradient(135deg, rgba(109,40,217,.08), rgba(6,182,212,.06));
            border-bottom:1px solid var(--line);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .note-card .note-head h5,
        .summary-card .sum-head h5{
            margin:0;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.8px;
            color:var(--muted);
            font-weight:900;
        }

        .note-card .note-body,
        .summary-card .sum-body{
            padding:12px 14px 14px;
            color:var(--ink);
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

        .sum-row:last-child{ border-bottom:none; padding-bottom:4px; }

        .sum-row .label{ color:var(--muted); font-weight:800; }
        .sum-row .value{ color:var(--ink); font-weight:900; font-variant-numeric:tabular-nums; white-space:nowrap; }

        .grand-total{
            margin-top:10px;
            padding:12px;
            border-radius:14px;
            background:linear-gradient(135deg, rgba(109,40,217,.12), rgba(6,182,212,.12), rgba(34,197,94,.12));
            border:1px solid rgba(109,40,217,.20);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .grand-total .label{
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.8px;
            font-weight:900;
            color:#2b2f3a;
        }

        .grand-total .value{
            font-size:18px;
            font-weight:1000;
            color:#0f172a;
            font-variant-numeric:tabular-nums;
            white-space:nowrap;
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

        .btn-pro:active{ transform:translateY(1px); }

        .btn-print{
            color:#fff;
            background:linear-gradient(135deg, var(--brand1), var(--brand2));
        }

        .btn-print:hover{ filter:brightness(.96); color:#fff; }

        .btn-ghost{
            color:var(--ink);
            background:#fff;
            border:1px solid var(--line);
            box-shadow:0 12px 26px rgba(16,24,40,.08);
        }

        .btn-ghost:hover{ filter:brightness(.98); }

        @media(max-width:991px){
            .info-grid,
            .totals-grid{ grid-template-columns:1fr; }
            .kvs{ grid-template-columns:1fr 1fr; }
            .logo-box{ min-width:100%; justify-content:flex-start; }
            .invoice-header{ margin-top:44px; }
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

            .invoice::before,
            .invoice::after{ display:none !important; }

            .invoice{
                border:0 !important;
                box-shadow:none !important;
                border-radius:0 !important;
                overflow:visible !important;
                transform:scale(.97);
                transform-origin:top left;
                width:calc(100% / .97);
            }

            .invoice-inner{ padding:8px 8px 6px !important; }

            .invoice-header{
                margin-top:0 !important;
                padding:10px !important;
                border-radius:10px !important;
                box-shadow:none !important;
                border:1px solid #ddd !important;
                background:#fff !important;
            }

            .title-row{
                flex-wrap:nowrap !important;
                align-items:flex-start !important;
                justify-content:space-between !important;
                gap:10px !important;
            }

            .invoice-title h2{
                font-size:15px !important;
                margin:0 !important;
            }

            .invoice-title .description{ font-size:10px !important; }

            .submeta{
                margin-top:6px !important;
                gap:6px !important;
                font-size:10px !important;
                flex-wrap:wrap !important;
            }

            .chip{
                padding:4px 8px !important;
                border:1px solid #ddd !important;
                background:#f6f7f9 !important;
                white-space:normal !important;
            }

            .badge-status{ display:none !important; }

            .logo-box{
                flex:0 0 150px !important;
                min-width:150px !important;
                justify-content:flex-end !important;
                align-items:flex-start !important;
                gap:0 !important;
            }

            .invoice-logo{
                padding:0 !important;
                border:0 !important;
                box-shadow:none !important;
                background:transparent !important;
            }

            .invoice-logo img{
                max-width:115px !important;
                height:auto !important;
                display:block !important;
                object-fit:contain !important;
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
            .note-card .note-head,
            .summary-card .sum-head{
                padding:7px 9px !important;
                background:#f6f7f9 !important;
            }

            .info-card .info-head h5,
            .note-card .note-head h5,
            .summary-card .sum-head h5{
                font-size:10px !important;
            }

            .info-body,
            .note-card .note-body,
            .summary-card .sum-body{
                padding:8px 9px !important;
                font-size:11px !important;
                line-height:1.25 !important;
            }

            .kv{
                padding:6px 7px !important;
                border-radius:8px !important;
            }

            .kv .k{ font-size:9px !important; margin-bottom:2px !important; }
            .kv .v{ font-size:11px !important; }

            .amount-banner{
                margin-top:10px !important;
                padding:10px !important;
                border-radius:10px !important;
                box-shadow:none !important;
                background:#f6f7f9 !important;
            }

            .amount-banner .amount{ font-size:18px !important; }
            .amount-banner .label,
            .amount-banner .status-pill{ font-size:10px !important; }

            .table-shell{
                margin-top:10px !important;
                overflow:visible !important;
            }

            .table-responsive{ overflow:visible !important; }

            .table thead th{
                font-size:8px !important;
                padding:5px 4px !important;
                background:#111827 !important;
                color:#fff !important;
            }

            .table tbody td{
                font-size:9px !important;
                padding:5px 4px !important;
            }

            .totals-grid{
                grid-template-columns:1.1fr .9fr !important;
                gap:8px !important;
                margin-top:10px !important;
            }

            .sum-row{
                padding:4px 0 !important;
                font-size:10px !important;
            }

            .grand-total{
                padding:8px !important;
                border-radius:8px !important;
            }

            .grand-total .label{ font-size:10px !important; }
            .grand-total .value{ font-size:14px !important; }

            .footer-actions,
            .no-print{ display:none !important; }

            tr, td, th{ page-break-inside:avoid !important; }
        }
