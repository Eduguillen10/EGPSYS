@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        <h3>Registrar firma fisica - Venta #{{ $venta->idventa }}</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            Imprima el compromiso, haga firmar al cliente o receptor autorizado y adjunte la foto o PDF firmado.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <a href="{{ route('venta_credito_aceptacion.plantilla', $venta->idventa) }}" target="_blank" class="btn btn-warning">
            <i class="fa fa-print"></i> Imprimir compromiso
        </a>
        <a href="{{ route('venta.show', $venta->idventa) }}" class="btn btn-default">
            Volver
        </a>
    </div>
</div>

<br>

<form action="{{ route('venta_credito_aceptacion.store', $venta->idventa) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
    @csrf

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="col-lg-3">
                        <label>Cliente</label>
                        <input class="form-control" value="{{ $venta->cliente }} - {{ $venta->cliente_documento }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Factura</label>
                        <input class="form-control" value="{{ $venta->nro_factura }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Condicion</label>
                        <input class="form-control" value="{{ $venta->condicion }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Vencimiento</label>
                        <input class="form-control" value="{{ $cuenta?->fecha_vencimiento ? date('d/m/Y', strtotime($cuenta->fecha_vencimiento)) : '-' }}" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label>Monto</label>
                        <input class="form-control" value="{{ number_format($venta->totalventa, 0, ',', '.') }}" readonly>
                    </div>

                    <div class="col-lg-12" style="margin-top:15px;">
                        <label>Texto aceptado</label>
                        <textarea class="form-control" rows="3" readonly>{{ $textoAceptado }}</textarea>
                    </div>

                    <div class="col-lg-4" style="margin-top:15px;">
                        <label>Firmado / recibido por</label>
                        <input name="recibido_por" class="form-control" value="{{ old('recibido_por') }}" required>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Documento receptor</label>
                        <input name="documento_receptor" class="form-control" value="{{ old('documento_receptor') }}" required>
                    </div>
                    <div class="col-lg-2" style="margin-top:15px;">
                        <label>Telefono</label>
                        <input name="telefono_receptor" class="form-control" value="{{ old('telefono_receptor') }}">
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Relacion</label>
                        <input name="relacion_receptor" class="form-control" value="{{ old('relacion_receptor') }}" placeholder="Titular, encargado, familiar...">
                    </div>

                    <div class="col-lg-6" style="margin-top:15px;">
                        <label>Adjunto firmado</label>
                        <input type="file" name="archivo_respaldo" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <div class="col-lg-6" style="margin-top:15px;">
                        <label>Observacion</label>
                        <input name="observacion" class="form-control" value="{{ old('observacion') }}">
                    </div>

                    <div class="col-lg-12" style="margin-top:18px;">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-check"></i> Registrar firma fisica
                        </button>
                        <a href="{{ route('venta.show', $venta->idventa) }}" class="btn btn-danger">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
