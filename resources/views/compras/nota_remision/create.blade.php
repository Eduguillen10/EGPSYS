@extends ('layouts.admin')
@section ('contenido')

<style>
    .remision-panel {
        border: 1px solid #d9e2ec;
        border-radius: 4px;
        background: #fff;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
    }

    .remision-panel-title {
        margin-top: 0;
        font-weight: 700;
        color: #2c3e50;
    }
</style>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Nueva Nota de Remision de Compra</h3>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

<div class="remision-panel">
    {!! Form::open(['route' => 'nota_remision_compra.create', 'method' => 'GET']) !!}
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <div class="form-group">
                <label>Orden de compra a remitir</label>
                <select name="orden" class="form-control selectpicker" data-live-search="true" required>
                    <option value="">Seleccione una orden...</option>
                    @foreach($ordenes as $ord)
                        <option value="{{ $ord->idordencompra }}" {{ $orden && $orden->idordencompra == $ord->idordencompra ? 'selected' : '' }}>
                            Orden #{{ $ord->idordencompra }} - {{ date('d/m/Y', strtotime($ord->fecha)) }} - {{ $ord->razonsocial }} - {{ $ord->deposito }} - {{ $ord->estado }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <label>&nbsp;</label><br>
            <button type="submit" class="btn btn-info">
                <i class="fa fa-download"></i> Cargar Orden
            </button>
            <a href="{{ route('nota_remision_compra.index') }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    {{ Form::close() }}
</div>

@if($orden)
{!! Form::open(['route' => 'nota_remision_compra.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
    <input type="hidden" name="idordencompra" value="{{ $orden->idordencompra }}">

    <div class="remision-panel">
        <h4 class="remision-panel-title">Datos de la orden</h4>
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Sucursal</label>
                    <input type="text" class="form-control" value="{{ $orden->sucursal }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" class="form-control" value="{{ $fecha }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Orden</label>
                    <input type="text" class="form-control" value="#{{ $orden->idordencompra }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Deposito</label>
                    <input type="text" class="form-control" value="{{ $orden->deposito }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Estado Orden</label>
                    <input type="text" class="form-control" value="{{ $orden->estado }}" readonly>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Proveedor</label>
                    <input type="text" class="form-control" value="{{ $orden->proveedor }}" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>RUC</label>
                    <input type="text" class="form-control" value="{{ $orden->ruc }}" readonly>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-8 col-xs-12">
                <div class="form-group">
                    <label>Direccion</label>
                    <input type="text" class="form-control" value="{{ $orden->direccion }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="remision-panel">
        <h4 class="remision-panel-title">Datos de la nota recibida</h4>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Nro. Comprobante</label>
                    <input type="text" name="nro_comprobante" class="form-control" value="{{ old('nro_comprobante') }}" maxlength="30" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Fecha Remision</label>
                    <input type="date" name="fecha_remision" class="form-control" value="{{ old('fecha_remision', $fecha) }}" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Motivo Traslado</label>
                    <input type="text" name="motivo_traslado" class="form-control" value="{{ old('motivo_traslado', 'Compra') }}" maxlength="100" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Chofer</label>
                    <input type="text" name="chofer" class="form-control" value="{{ old('chofer') }}" maxlength="100">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Documento Chofer</label>
                    <input type="text" name="documento_chofer" class="form-control" value="{{ old('documento_chofer') }}" maxlength="30">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>Vehiculo</label>
                    <input type="text" name="vehiculo" class="form-control" value="{{ old('vehiculo') }}" maxlength="100">
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                <div class="form-group">
                    <label>Chapa</label>
                    <input type="text" name="chapa" class="form-control" value="{{ old('chapa') }}" maxlength="30">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                <div class="form-group">
                    <label>Observacion</label>
                    <input type="text" name="observacion" class="form-control" value="{{ old('observacion') }}" maxlength="255">
                </div>
            </div>
        </div>
    </div>

    <div class="remision-panel">
        <h4 class="remision-panel-title">Detalle de productos recibidos</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color:#ffd966">
                    <th>Usar</th>
                    <th>Item</th>
                    <th>Producto</th>
                    <th>Cantidad Orden</th>
                    <th>Pendiente</th>
                    <th>Cantidad Remision</th>
                </thead>
                <tbody>
                    @foreach($detalles as $det)
                        @php $habilitado = (float) $det->cantidad_pendiente > 0; @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="usar-detalle" {{ $habilitado ? 'checked' : 'disabled' }}>
                            </td>
                            <td>{{ $det->items }}</td>
                            <td>{{ $det->producto }}</td>
                            <td>{{ number_format($det->cantidad, 0, ',', '.') }}</td>
                            <td>{{ number_format($det->cantidad_pendiente, 3, ',', '.') }}</td>
                            <td>
                                <input type="hidden" name="idorden_detalle[]" value="{{ $det->idorden_detalle }}" {{ $habilitado ? '' : 'disabled' }}>
                                <input type="hidden" name="idproducto[]" value="{{ $det->idproducto }}" {{ $habilitado ? '' : 'disabled' }}>
                                <input type="number" name="cantidad[]" class="form-control cantidad-remision" min="0.001" step="0.001" max="{{ $det->cantidad_pendiente }}" value="{{ $habilitado ? $det->cantidad_pendiente : 0 }}" {{ $habilitado ? 'required' : 'disabled' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa fa-check"></i> Guardar Nota de Remision
        </button>
        <a href="{{ route('nota_remision_compra.index') }}" class="btn btn-danger">Cancelar</a>
    </div>
{{ Form::close() }}

@push('scripts')
<script>
    $('.usar-detalle').on('change', function () {
        var fila = $(this).closest('tr');
        var activo = $(this).is(':checked');

        fila.find('input[name="idorden_detalle[]"], input[name="idproducto[]"], input[name="cantidad[]"]').prop('disabled', !activo);
        fila.find('input[name="cantidad[]"]').prop('required', activo);
    });
</script>
@endpush
@endif

@endsection
