@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Histórico de Stock</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <form method="GET" action="{{ route('movimiento_stock.index') }}" class="form-inline" style="margin-bottom: 12px;">
            <input type="text" name="producto" class="form-control" placeholder="Producto o código..." value="{{ $filtros['producto'] ?? '' }}" style="width: 190px; margin-bottom: 6px;">
            <input type="text" name="sucursal" class="form-control" placeholder="Sucursal..." value="{{ $filtros['sucursal'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
            <input type="text" name="deposito" class="form-control" placeholder="Depósito..." value="{{ $filtros['deposito'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
            <select name="tipo_origen" class="form-control" style="width: 155px; margin-bottom: 6px;">
                <option value="">Origen...</option>
                @foreach (['COMPRA', 'VENTA', 'AJUSTE', 'NC_COMPRA', 'ND_COMPRA', 'NC_VENTA', 'ND_VENTA', 'REMISION_COMPRA', 'REMISION_VENTA'] as $origen)
                    <option value="{{ $origen }}" {{ ($filtros['tipo_origen'] ?? '') === $origen ? 'selected' : '' }}>{{ $origen }}</option>
                @endforeach
            </select>
            <select name="operacion" class="form-control" style="width: 125px; margin-bottom: 6px;">
                <option value="">Operación...</option>
                <option value="ENTRADA" {{ ($filtros['operacion'] ?? '') === 'ENTRADA' ? 'selected' : '' }}>ENTRADA</option>
                <option value="SALIDA" {{ ($filtros['operacion'] ?? '') === 'SALIDA' ? 'selected' : '' }}>SALIDA</option>
            </select>
            <input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}" style="width: 145px; margin-bottom: 6px;">
            <input type="date" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] ?? '' }}" style="width: 145px; margin-bottom: 6px;">
            <button type="submit" class="btn btn-primary" style="margin-bottom: 6px;">
                <i class="fa fa-search"></i> Buscar
            </button>
            <a href="{{ route('movimiento_stock.index') }}" class="btn btn-default" style="margin-bottom: 6px;">Limpiar</a>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Sucursal</th>
                        <th>Depósito</th>
                        <th>Origen</th>
                        <th>ID Origen</th>
                        <th>Detalle</th>
                        <th>Operación</th>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->idmovimiento_stock }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($movimiento->fecha)) }}</td>
                            <td>{{ trim(($movimiento->producto_codigo ? $movimiento->producto_codigo . ' - ' : '') . $movimiento->producto) }}</td>
                            <td>{{ $movimiento->sucursal }}</td>
                            <td>{{ $movimiento->deposito }}</td>
                            <td>{{ $movimiento->tipo_origen }}</td>
                            <td>{{ $movimiento->id_origen }}</td>
                            <td>{{ $movimiento->detalle_origen }}</td>
                            <td>
                                @if ($movimiento->operacion === 'ENTRADA')
                                    <span class="label label-success">ENTRADA</span>
                                @else
                                    <span class="label label-danger">SALIDA</span>
                                @endif
                            </td>
                            <td>{{ \App\Helpers\NumberFormatter::cantidad($movimiento->cantidad) }}</td>
                            <td>{{ $movimiento->costo_unitario !== null ? number_format((float) $movimiento->costo_unitario, 2, ',', '.') : '-' }}</td>
                            <td>{{ $movimiento->usuario }}</td>
                            <td>{{ $movimiento->estado }}</td>
                            <td>{{ $movimiento->observacion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">No hay movimientos de stock registrados con estos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $movimientos->appends(request()->query())->render() }}
    </div>
</div>
@endsection
