@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        <h3>Completar Recepcion - Nota de Remision {{ $remision->nro_remision ?? '#' . $remision->idnota_remision_venta }}</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
        <div class="panel panel-default">
            <div class="panel-heading">Datos de emision no editables</div>
            <div class="panel-body">
                <div class="col-lg-2"><label>Nro. Remision</label><p>{{ $remision->nro_remision ?? '-' }}</p></div>
                <div class="col-lg-2"><label>Venta</label><p>{{ $ventaLabel }}</p></div>
                <div class="col-lg-2"><label>Comprobante</label><p>{{ $comprobanteLabel }}</p></div>
                <div class="col-lg-4"><label>Destinatario</label><p>{{ $destinatarioNombre }} - {{ $destinatarioDocumento }}</p></div>
                <div class="col-lg-2"><label>Fecha emision</label><p>{{ $remision->fecha_emision ? date('d/m/Y', strtotime($remision->fecha_emision)) : '-' }}</p></div>
                <div class="col-lg-2"><label>Estado</label><p>@include('ventas.partials.estado', ['estado' => $remision->estado])</p></div>

                <div class="col-lg-6"><label>Partida</label><p>{{ $remision->punto_partida ?? '-' }}</p></div>
                <div class="col-lg-6"><label>Llegada</label><p>{{ $remision->punto_llegada ?? '-' }}</p></div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('nota_remision_venta.update', $remision->idnota_remision_venta) }}" method="POST" autocomplete="off">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">Constancia de recepcion</div>
                <div class="panel-body">
                    <div class="col-lg-4">
                        <label>Recibido por</label>
                        <input name="recibido_por" class="form-control" value="{{ old('recibido_por', $remision->recibido_por) }}" maxlength="150" required>
                    </div>
                    <div class="col-lg-3">
                        <label>Documento receptor</label>
                        <input name="documento_receptor" class="form-control" value="{{ old('documento_receptor', $remision->documento_receptor) }}" maxlength="50" required>
                    </div>
                    <div class="col-lg-3">
                        <label>Fecha entrega</label>
                        <input type="date" name="fecha_entrega" class="form-control" value="{{ old('fecha_entrega', $remision->fecha_entrega ?: date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-lg-2">
                        <label>Hora entrega</label>
                        <input type="time" name="hora_entrega" class="form-control" value="{{ old('hora_entrega', $remision->hora_entrega ? substr($remision->hora_entrega, 0, 5) : date('H:i')) }}">
                    </div>

                    <div class="col-lg-12" style="margin-top:15px;">
                        <label>Observacion de entrega</label>
                        <input name="observacion_entrega" class="form-control" value="{{ old('observacion_entrega', $remision->observacion_entrega) }}" maxlength="255">
                    </div>

                    <div class="col-lg-12" style="margin-top:18px;">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-check"></i> Registrar Recepcion
                        </button>
                        <a href="{{ route('nota_remision_venta.show', $remision->idnota_remision_venta) }}" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
