@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ctas. a Cobrar</h3>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form method="GET" action="{{ route('cuenta_cobrar.index') }}" class="form-inline" style="margin-bottom: 12px;">
			<input type="text" name="searchText" class="form-control" placeholder="Cliente, documento, factura..." value="{{ $filtros['searchText'] ?? '' }}" style="width: 275px; margin-bottom: 6px;">
			<select name="estado" class="form-control" style="width: 145px; margin-bottom: 6px;">
				<option value="">Estado...</option>
				@foreach (['Generado', 'Parcial', 'Pagado', 'Anulado'] as $estado)
					<option value="{{ $estado }}" {{ ($filtros['estado'] ?? '') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
				@endforeach
			</select>
			<input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
			<input type="date" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] ?? '' }}" style="width: 150px; margin-bottom: 6px;">
			<button type="submit" class="btn btn-primary" style="margin-bottom: 6px;"><i class="fa fa-search"></i> Buscar</button>
			<a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-default" style="margin-bottom: 6px;">Limpiar</a>
		</form>
	</div>
</div>

@php
	$totalImporte = 0;
	$totalSaldo = 0;
@endphp

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>ID</th>
					<th>Sucursal</th>
					<th>Nro. Venta</th>
					<th>Nro. Factura</th>
					<th>Fecha</th>
					<th>Vencimiento</th>
					<th>Cliente</th>
					<th>Doc.</th>
					<th>Importe</th>
					<th>Saldo</th>
					<th>Estado</th>
				</thead>
				@forelse ($cuenta_cobrar as $ctacob)
					@php
						$totalImporte += (int) $ctacob->importe;
						$totalSaldo += (int) $ctacob->saldo;
					@endphp
					<tr>
						<td>{{ $ctacob->idcuenta_cobrar }}</td>
						<td>{{ $ctacob->sucursal }}</td>
						<td>{{ $ctacob->idventa }}</td>
						<td>{{ $ctacob->nro_factura }}</td>
						<td>{{ $ctacob->fecha ? date('d/m/Y', strtotime($ctacob->fecha)) : '-' }}</td>
						<td>{{ $ctacob->fecha_vencimiento ? date('d/m/Y', strtotime($ctacob->fecha_vencimiento)) : '-' }}</td>
						<td>{{ $ctacob->cliente }}</td>
						<td>{{ $ctacob->num_documento }}</td>
						<td align="right">{{ number_format((int) $ctacob->importe, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ctacob->saldo, 0, ',', '.') }}</td>
						<td>
							<span class="label label-{{ in_array($ctacob->estado, ['Pagado', 'Saldado'], true) ? 'success' : (in_array($ctacob->estado, ['Anulado', 'Cancelado'], true) ? 'danger' : 'warning') }}">
								{{ $ctacob->estado }}
							</span>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="11" class="text-center">No se encontraron cuentas a cobrar.</td>
					</tr>
				@endforelse
				<tr>
					<td colspan="8" align="right"><strong>Total de la pagina:</strong></td>
					<td align="right"><strong>{{ number_format($totalImporte, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalSaldo, 0, ',', '.') }}</strong></td>
					<td></td>
				</tr>
			</table>
		</div>
		{{ $cuenta_cobrar->appends(request()->query())->render() }}
	</div>
</div>
@endsection
