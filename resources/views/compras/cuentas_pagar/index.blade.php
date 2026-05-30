@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ctas. a Pagar</h3>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form method="GET" action="{{ route('cuentas_pagar.index') }}" class="form-inline" style="margin-bottom: 12px;">
			<input type="text" name="searchText" class="form-control" placeholder="Proveedor, RUC, factura..." value="{{ $filtros['searchText'] ?? '' }}" style="width: 260px; margin-bottom: 6px;">
			<select name="estado" class="form-control" style="width: 145px; margin-bottom: 6px;">
				<option value="">Estado...</option>
				@foreach (['Pendiente', 'Cancelado', 'Pagado'] as $estado)
					<option value="{{ $estado }}" {{ ($filtros['estado'] ?? '') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
				@endforeach
			</select>
			<input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
			<input type="date" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
			<button type="submit" class="btn btn-primary" style="margin-bottom: 6px;"><i class="fa fa-search"></i> Buscar</button>
			<a href="{{ route('cuentas_pagar.index') }}" class="btn btn-default" style="margin-bottom: 6px;">Limpiar</a>
		</form>
	</div>
</div>

@php
	$totalMonto = 0;
@endphp

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>ID</th>
					<th>Sucursal</th>
					<th>Nro. Compra</th>
					<th>Nro. Factura</th>
					<th>Timbrado</th>
					<th>Fecha Factura</th>
					<th>Vencimiento</th>
					<th>Proveedor</th>
					<th>RUC</th>
					<th>Monto</th>
					<th>Estado</th>
				</thead>
				@forelse ($cuentas as $cuenta)
				@php
					$totalMonto += (int) $cuenta->montoapagar;
				@endphp
				<tr>
					<td>{{ $cuenta->idcuentaapagar }}</td>
					<td>{{ $cuenta->sucursal }}</td>
					<td>{{ $cuenta->idcompra }}</td>
					<td>{{ $cuenta->nro_factura }}</td>
					<td>{{ $cuenta->timbrado }}</td>
					<td>{{ date('d/m/Y', strtotime($cuenta->fecha_factura)) }}</td>
					<td>{{ $cuenta->fecha_vencimiento ? date('d/m/Y', strtotime($cuenta->fecha_vencimiento)) : '-' }}</td>
					<td>{{ $cuenta->proveedor }}</td>
					<td>{{ $cuenta->ruc }}</td>
					<td align="right">{{ number_format((int) $cuenta->montoapagar, 0, ',', '.') }}</td>
					<td>
						<span class="label label-{{ $cuenta->estado === 'Pagado' ? 'success' : ($cuenta->estado === 'Cancelado' ? 'danger' : 'warning') }}">
							{{ $cuenta->estado }}
						</span>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="11" class="text-center">No se encontraron cuentas a pagar.</td>
				</tr>
				@endforelse
				<tr>
					<td colspan="9" align="right"><strong>Total de la pagina:</strong></td>
					<td align="right"><strong>{{ number_format($totalMonto, 0, ',', '.') }}</strong></td>
					<td></td>
				</tr>
			</table>
		</div>
		{{ $cuentas->appends(request()->query())->render() }}
	</div>
</div>
@endsection
