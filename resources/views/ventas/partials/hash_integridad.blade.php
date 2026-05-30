@php
    $hashTexto = $hash ?? null;
    $valido = $hashValido ?? null;
@endphp

@if($hashTexto)
    <div class="hash-box" style="margin-top:14px;border:1px solid #eaecf0;border-radius:12px;padding:12px;background:#fff;">
        <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;">
            <strong>Integridad del documento</strong>
            @if(!is_null($valido))
                <span style="font-weight:800;color:{{ $valido ? '#15803d' : '#b91c1c' }};">
                    {{ $valido ? 'Integridad valida' : 'Documento alterado' }}
                </span>
            @endif
        </div>
        <div style="word-break:break-all;font-size:11px;color:#475467;margin-top:8px;">
            {{ $hashTexto }}
        </div>
    </div>
@endif
