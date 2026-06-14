@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        <h3>Nota de Remision {{ $remision->nro_remision ?? '#' . $remision->idnota_remision_venta }}</h3>
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('info')) <div class="alert alert-warning">{{ session('info') }}</div> @endif
    </div>
</div>

@php
    $destinatarioNombre = $venta ? $venta->cliente : ($remision->destinatario_nombre ?? '-');
    $destinatarioDocumento = $venta ? $venta->cliente_documento : ($remision->destinatario_documento ?? '-');
    $ventaLabel = $venta ? '#' . $venta->idventa : 'Sin venta asociada';
    $comprobanteLabel = $venta ? $venta->nro_factura : '-';
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Datos legales del traslado</div>
            <div class="panel-body tm-detail-row">
                <div class="col-lg-2"><label>Nro. Remision</label><p>{{ $remision->nro_remision ?? '-' }}</p></div>
                <div class="col-lg-2"><label>Timbrado</label><p>{{ $remision->nro_timbrado_remision ?? '-' }}</p></div>
                <div class="col-lg-2"><label>Venta</label><p>{{ $ventaLabel }}</p></div>
                <div class="col-lg-2"><label>Comprobante</label><p>{{ $comprobanteLabel }}</p></div>
                <div class="col-lg-2"><label>Fecha emision</label><p>{{ $remision->fecha_emision ? date('d/m/Y', strtotime($remision->fecha_emision)) : '-' }}</p></div>
                <div class="col-lg-2"><label>Estado</label><p>@include('ventas.partials.estado', ['estado' => $remision->estado])</p></div>

                <div class="col-lg-4"><label>Destinatario</label><p>{{ $destinatarioNombre }} - {{ $destinatarioDocumento }}</p></div>
                <div class="col-lg-2"><label>Motivo</label><p>{{ $remision->motivo_traslado ?? 'Venta' }}</p></div>
                <div class="col-lg-3"><label>Inicio traslado</label><p>{{ $remision->fecha_inicio_traslado ? date('d/m/Y', strtotime($remision->fecha_inicio_traslado)) : '-' }}</p></div>
                <div class="col-lg-3"><label>Fin traslado</label><p>{{ $remision->fecha_fin_traslado ? date('d/m/Y', strtotime($remision->fecha_fin_traslado)) : '-' }}</p></div>

                <div class="col-lg-6"><label>Punto partida</label><p>{{ $remision->punto_partida ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Ciudad partida</label><p>{{ $remision->ciudad_partida ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Departamento partida</label><p>{{ $remision->departamento_partida ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Sucursal origen</label><p>{{ $remision->sucursal_origen ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Deposito origen</label><p>{{ $remision->deposito_origen ?? '-' }}</p></div>

                <div class="col-lg-6"><label>Punto llegada</label><p>{{ $remision->punto_llegada ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Ciudad llegada</label><p>{{ $remision->ciudad_llegada ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Departamento llegada</label><p>{{ $remision->departamento_llegada ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Sucursal destino</label><p>{{ $remision->sucursal_destino ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Deposito destino</label><p>{{ $remision->deposito_destino ?? '-' }}</p></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Transporte y recepcion</div>
            <div class="panel-body tm-detail-row">
                <div class="col-lg-3"><label>Transportista</label><p>{{ $remision->transportista_nombre ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Doc. transportista</label><p>{{ $remision->transportista_documento ?? '-' }}</p></div>
                <div class="col-lg-6"><label>Direccion transportista</label><p>{{ $remision->transportista_direccion ?? '-' }}</p></div>

                <div class="col-lg-3"><label>Chofer</label><p>{{ trim($remision->chofer) ?: '-' }}</p></div>
                <div class="col-lg-3"><label>CI chofer</label><p>{{ $remision->chofer_ci ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Vehiculo</label><p>{{ $remision->nrochapa ?? '-' }} {{ $remision->modelo ? '- '.$remision->modelo : '' }}</p></div>
                <div class="col-lg-3"><label>Chasis</label><p>{{ $remision->chasis ?? '-' }}</p></div>

                <div class="col-lg-3"><label>Recibido por</label><p>{{ $remision->recibido_por ?? '-' }}</p></div>
                <div class="col-lg-3"><label>Documento receptor</label><p>{{ $remision->documento_receptor ?? '-' }}</p></div>
                <div class="col-lg-2"><label>Fecha entrega</label><p>{{ $remision->fecha_entrega ? date('d/m/Y', strtotime($remision->fecha_entrega)) : '-' }}</p></div>
                <div class="col-lg-2"><label>Hora entrega</label><p>{{ $remision->hora_entrega ? substr($remision->hora_entrega, 0, 5) : '-' }}</p></div>
                <div class="col-lg-2"><label>Usuario recepcion</label><p>{{ $remision->usuario_recepcion ?? '-' }}</p></div>

                <div class="col-lg-6"><label>Observacion de emision</label><p>{{ $remision->observacion ?? '-' }}</p></div>
                <div class="col-lg-6"><label>Observacion de entrega</label><p>{{ $remision->observacion_entrega ?? '-' }}</p></div>

                <div class="col-lg-12">
                    @include('ventas.partials.hash_integridad', [
                        'hash' => $remision->hash_documento ?? null,
                        'hashValido' => $hashValido ?? null,
                    ])
                </div>
                <div class="col-lg-12">
                    <label>Hash de recepcion</label>
                    <p style="word-break:break-all;">{{ $remision->hash_recepcion ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed table-hover">
        <thead style="background-color:#ffd966">
            <th>Item</th>
            <th>Producto</th>
            <th>Cantidad</th>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->items }}</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td>{{ \App\Helpers\NumberFormatter::cantidad($detalle->cantidad) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<a href="{{ route('nota_remision_venta.comprobante', $remision->idnota_remision_venta) }}" target="_blank" class="btn btn-warning">
    <i class="fa fa-print"></i> Imprimir
</a>
@if(!in_array(strtoupper(trim((string)$remision->estado)), ['A', 'ANULADO', 'ANULADA'], true))
    <a href="{{ route('nota_remision_venta.edit', $remision->idnota_remision_venta) }}" class="btn btn-success">
        <i class="fa fa-check"></i> Completar Recepcion
    </a>
@endif
<a href="{{ route('nota_remision_venta.index') }}" class="btn btn-default">Volver</a>
@endsection
