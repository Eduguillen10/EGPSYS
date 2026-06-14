@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        <h3>Aceptacion de Credito - Venta #{{ $venta->idventa }}</h3>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('info')) <div class="alert alert-warning">{{ session('info') }}</div> @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body tm-detail-row">
                <div class="col-lg-3"><label>Factura</label><p>{{ $venta->nro_factura }}</p></div>
                <div class="col-lg-3"><label>Cliente</label><p>{{ $venta->cliente }}</p></div>
                <div class="col-lg-2"><label>Monto</label><p>{{ number_format($aceptacion->monto, 0, ',', '.') }}</p></div>
                <div class="col-lg-2"><label>Condicion</label><p>{{ $aceptacion->condicion }}</p></div>
                <div class="col-lg-2"><label>Estado</label><p><span class="label label-success">{{ $aceptacion->estado }}</span></p></div>

                <div class="col-lg-3"><label>Firmado por</label><p>{{ $aceptacion->recibido_por }}</p></div>
                <div class="col-lg-3"><label>Documento</label><p>{{ $aceptacion->documento_receptor }}</p></div>
                <div class="col-lg-3"><label>Metodo</label><p>{{ $aceptacion->metodo_aceptacion }}</p></div>
                <div class="col-lg-3"><label>Registrado</label><p>{{ $aceptacion->created_at ? date('d/m/Y H:i', strtotime($aceptacion->created_at)) : '-' }}</p></div>

                <div class="col-lg-12">
                    <label>Texto aceptado</label>
                    <p>{{ $aceptacion->texto_aceptado }}</p>
                </div>

                @if($aceptacion->observacion)
                    <div class="col-lg-12">
                        <label>Observacion</label>
                        <p>{{ $aceptacion->observacion }}</p>
                    </div>
                @endif

                <div class="col-lg-12">
                    @include('ventas.partials.hash_integridad', [
                        'hash' => $aceptacion->hash_documento ?? null,
                        'hashValido' => $hashValido ?? null,
                    ])
                </div>

                <div class="col-lg-12">
                    @if($aceptacion->archivo_respaldo)
                        <a href="{{ asset($aceptacion->archivo_respaldo) }}" target="_blank" class="btn btn-info">
                            <i class="fa fa-file"></i> Ver adjunto firmado
                        </a>
                    @endif
                    <a href="{{ route('venta_credito_aceptacion.comprobante', [$venta->idventa, $aceptacion->idaceptacion_credito]) }}" target="_blank" class="btn btn-warning">
                        <i class="fa fa-print"></i> Comprobante
                    </a>
                    <a href="{{ route('venta.show', $venta->idventa) }}" class="btn btn-default">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
