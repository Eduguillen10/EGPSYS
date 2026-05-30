<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Listado')</title>

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">

    <style>
        :root {
            --paper: #ffffff;
            --ink: #101828;
            --muted: #667085;
            --line: #eaecf0;
            --brand1: #6d28d9;
            --brand2: #06b6d4;
            --brand3: #22c55e;
        }

        body {
            color: var(--ink);
            background:
                radial-gradient(1000px 500px at 15% 5%, rgba(109,40,217,.35), transparent 55%),
                radial-gradient(900px 520px at 85% 0%, rgba(6,182,212,.28), transparent 55%),
                linear-gradient(180deg, #070b16 0%, #0b1020 100%);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 95%;
            max-width: 1160px;
            margin: 28px auto;
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(16,24,40,.16);
            padding: 22px;
            overflow: hidden;
        }

        h2 {
            font-weight: 800;
            color: var(--ink);
        }

        h4 {
            margin-top: 18px;
            padding: 10px 12px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(109,40,217,.08), rgba(6,182,212,.06));
            color: var(--muted);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .table thead th {
            background: #0b1220 !important;
            color: #fff !important;
            border-color: #0b1220 !important;
            font-size: 11px;
            text-transform: uppercase;
        }

        .btn-primary {
            border: 0;
            background: linear-gradient(135deg, var(--brand1), var(--brand2));
            font-weight: 800;
        }

        @media print {
            body {
                background: #fff !important;
            }

            .container {
                width: 100%;
                max-width: none;
                margin: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 8px;
            }

            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('contenido')
    </div>
</body>
</html>
