@php
    $valor = trim((string) ($estado ?? ''));
    $clave = strtoupper($valor);

    $estados = [
        'R' => 'Realizado',
        'P' => 'Pendiente',
        'A' => 'Anulado',
        'F' => 'Finalizado',
        'PC' => 'Pendiente de Cobro',
        'C' => 'Cancelado',
        'ANULADA' => 'Anulado',
        'ANULADO' => 'Anulado',
    ];
@endphp

{{ $estados[$clave] ?? ($valor !== '' ? $valor : 'Sin estado') }}
