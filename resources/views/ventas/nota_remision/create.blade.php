@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        <h3>Nueva Nota de Remision</h3>

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

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="{{ route('nota_remision_venta.create') }}" method="GET" autocomplete="off">
                    <div class="col-lg-9">
                        <label>Factura / venta a remitir (opcional)</label>
                        <select name="idventa" class="form-control selectpicker" data-live-search="true">
                            <option value="">Sin venta asociada</option>
                            @foreach($ventas as $item)
                                <option value="{{ $item->idventa }}" {{ $venta && (int)$venta->idventa === (int)$item->idventa ? 'selected' : '' }}>
                                    Venta #{{ $item->idventa }} - Factura {{ $item->nro_factura }} - {{ $item->cliente }} - {{ $item->condicion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3" style="margin-top:24px;">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i> Cargar contexto
                        </button>
                        <a href="{{ route('nota_remision_venta.index') }}" class="btn btn-default">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('nota_remision_venta.store') }}" method="POST" autocomplete="off">
    @csrf
    @if($venta)
        <input type="hidden" name="idventa" value="{{ $venta->idventa }}">
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">Datos del comprobante y traslado</div>
                <div class="panel-body">
                    <div class="col-lg-2">
                        <label>Nro. Remision</label>
                        <input class="form-control" value="{{ $previewNroRemision ?? 'Sin timbrado activo' }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Timbrado</label>
                        <input class="form-control" value="{{ $previewTimbrado ?? '-' }}" readonly>
                    </div>

                    @if($venta)
                        <div class="col-lg-2">
                            <label>Venta</label>
                            <input class="form-control" value="#{{ $venta->idventa }}" readonly>
                        </div>
                        <div class="col-lg-2">
                            <label>Factura</label>
                            <input class="form-control" value="{{ $venta->nro_factura }}" readonly>
                        </div>
                        <div class="col-lg-4">
                            <label>Cliente</label>
                            <input class="form-control" value="{{ $venta->cliente }} - {{ $venta->cliente_documento }}" readonly>
                        </div>
                        <div class="col-lg-2">
                            <label>Condicion</label>
                            <input class="form-control" value="{{ $venta->condicion }}" readonly>
                        </div>
                    @else
                        <div class="col-lg-4">
                            <label>Destinatario de remision</label>
                            <select name="iddestinatario_remision" class="form-control selectpicker" data-live-search="true">
                                <option value="">Sin destinatario externo</option>
                                @foreach($destinatarios as $destinatario)
                                    <option value="{{ $destinatario->iddestinatario_remision }}" {{ (string)old('iddestinatario_remision') === (string)$destinatario->iddestinatario_remision ? 'selected' : '' }}>
                                        {{ $destinatario->nombre }} {{ $destinatario->documento ? '- '.$destinatario->documento : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-lg-2">
                        <label>Fecha emision</label>
                        <input type="date" name="fecha_emision" class="form-control" value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Motivo traslado</label>
                        <select name="motivo_traslado" class="form-control" required>
                            @foreach($motivosTraslado as $motivo)
                                @php $motivoDefault = $venta ? 'Venta' : 'Traslado entre locales de la misma empresa'; @endphp
                                <option value="{{ $motivo }}" {{ old('motivo_traslado', $motivoDefault) === $motivo ? 'selected' : '' }}>{{ $motivo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Inicio traslado</label>
                        <input type="date" name="fecha_inicio_traslado" class="form-control" value="{{ old('fecha_inicio_traslado', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Fin traslado</label>
                        <input type="date" name="fecha_fin_traslado" class="form-control" value="{{ old('fecha_fin_traslado', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Comprobante asociado</label>
                        @if($venta)
                            <input class="form-control" value="Factura {{ $venta->nro_factura }} / Timbrado {{ $venta->nro_timbrado ?? '-' }}" readonly>
                        @else
                            <input class="form-control" value="Sin venta asociada" readonly>
                        @endif
                    </div>

                    @if(!$venta)
                        <div class="col-lg-6" style="margin-top:15px;">
                            <label>Deposito origen</label>
                            <select name="iddeposito_origen" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($depositos as $deposito)
                                    <option value="{{ $deposito->iddeposito }}" {{ (string)old('iddeposito_origen') === (string)$deposito->iddeposito ? 'selected' : '' }}>
                                        {{ $deposito->sucursal }} - {{ $deposito->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6" style="margin-top:15px;">
                            <label>Deposito destino</label>
                            <select name="iddeposito_destino" class="form-control">
                                <option value="">Sin destino interno</option>
                                @foreach($depositos as $deposito)
                                    <option value="{{ $deposito->iddeposito }}" {{ (string)old('iddeposito_destino') === (string)$deposito->iddeposito ? 'selected' : '' }}>
                                        {{ $deposito->sucursal }} - {{ $deposito->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-lg-6" style="margin-top:15px;">
                        <label>Punto partida</label>
                        <input name="punto_partida" class="form-control" value="{{ old('punto_partida', $venta ? $venta->sucursal : '') }}" required>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Ciudad partida</label>
                        <input name="ciudad_partida" class="form-control" value="{{ old('ciudad_partida') }}">
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Departamento partida</label>
                        <input name="departamento_partida" class="form-control" value="{{ old('departamento_partida') }}">
                    </div>

                    <div class="col-lg-6" style="margin-top:15px;">
                        <label>Punto llegada</label>
                        <input name="punto_llegada" class="form-control" value="{{ old('punto_llegada', $venta ? $venta->cliente_direccion : '') }}" required>
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Ciudad llegada</label>
                        <input name="ciudad_llegada" class="form-control" value="{{ old('ciudad_llegada') }}">
                    </div>
                    <div class="col-lg-3" style="margin-top:15px;">
                        <label>Departamento llegada</label>
                        <input name="departamento_llegada" class="form-control" value="{{ old('departamento_llegada') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$venta)
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">Detalle de mercaderia</div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="tabla-detalle-remision">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width:160px;">Cantidad</th>
                                        <th style="width:90px;">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldProductos = old('idproducto', [null]);
                                        $oldCantidades = old('cantidad', [null]);
                                    @endphp
                                    @foreach($oldProductos as $idx => $oldProducto)
                                        <tr>
                                            <td>
                                                <select name="idproducto[]" class="form-control">
                                                    <option value="">Seleccione producto...</option>
                                                    @foreach($productos as $producto)
                                                        <option value="{{ $producto->idproducto }}" {{ (string)$oldProducto === (string)$producto->idproducto ? 'selected' : '' }}>
                                                            {{ $producto->codigo }} {{ $producto->descripcion }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="cantidad[]" min="0.001" step="0.001" class="form-control" value="{{ $oldCantidades[$idx] ?? '' }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm js-remove-row">Quitar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-info" id="agregar-detalle-remision">Agregar producto</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">Transporte y recepcion</div>
                <div class="panel-body">
                    <div class="col-lg-4">
                        <label>Chofer</label>
                        <select name="idchofer" class="form-control selectpicker" data-live-search="true" required>
                            <option value="">Seleccione chofer...</option>
                            @foreach($choferes as $chofer)
                                <option value="{{ $chofer->idchofer }}" {{ (string)old('idchofer') === (string)$chofer->idchofer ? 'selected' : '' }}>
                                    {{ $chofer->nombre }} {{ $chofer->apellido }} - {{ $chofer->ci }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Vehiculo</label>
                        <select name="idvehiculo" class="form-control selectpicker" data-live-search="true" required>
                            <option value="">Seleccione vehiculo...</option>
                            @foreach($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->idvehiculo }}" {{ (string)old('idvehiculo') === (string)$vehiculo->idvehiculo ? 'selected' : '' }}>
                                    {{ $vehiculo->nrochapa }} - {{ $vehiculo->modelo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Transportista</label>
                        <select name="idtransportista" class="form-control selectpicker" data-live-search="true" required>
                            <option value="">Seleccione transportista...</option>
                            @foreach($transportistas as $transportista)
                                <option value="{{ $transportista->idtransportista }}" {{ (string)old('idtransportista') === (string)$transportista->idtransportista ? 'selected' : '' }}>
                                    {{ $transportista->nombre }} {{ $transportista->documento ? '- '.$transportista->documento : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4" style="margin-top:15px;">
                        <label>Recibido por</label>
                        <input name="recibido_por" class="form-control" value="{{ old('recibido_por') }}" placeholder="Se puede completar al entregar">
                    </div>

                    <div class="col-lg-4" style="margin-top:15px;">
                        <label>Documento receptor</label>
                        <input name="documento_receptor" class="form-control" value="{{ old('documento_receptor') }}">
                    </div>
                    <div class="col-lg-8" style="margin-top:15px;">
                        <label>Observacion</label>
                        <input name="observacion" class="form-control" value="{{ old('observacion') }}">
                    </div>

                    <div class="col-lg-12" style="margin-top:18px;">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-check"></i> Guardar Nota de Remision
                        </button>
                        <a href="{{ route('nota_remision_venta.index') }}" class="btn btn-danger">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
@if(!$venta)
<script>
    $(function () {
        $('#agregar-detalle-remision').on('click', function () {
            var $tbody = $('#tabla-detalle-remision tbody');
            var $row = $tbody.find('tr:first').clone();

            $row.find('select').val('');
            $row.find('input').val('');
            $tbody.append($row);
        });

        $(document).on('click', '.js-remove-row', function () {
            var $tbody = $('#tabla-detalle-remision tbody');

            if ($tbody.find('tr').length > 1) {
                $(this).closest('tr').remove();
            }
        });
    });
</script>
@endif
@endpush
