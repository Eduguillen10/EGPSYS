<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Listado')</title>

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">

    <style>
        body { background: #fff; }
        .container { width: 95%; margin: 20px auto; }
        @media print {
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
