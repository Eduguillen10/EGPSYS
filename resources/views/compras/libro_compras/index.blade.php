@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Libro de Compras</h3>
	</div>
</div>

<form action="{{ route('libro_compras.index') }}" method="GET" autocomplete="off">
	<div class="row">
		<div class="col-lg-3 col-sm-4 col-xs-12">
			<div class="form-group">
				<label for="fecha_desde">Fecha Desde</label>
				<input type="date" name="fecha_desde" required value="{{ $fecha_desde }}" class="form-control">
			</div>
		</div>
		<div class="col-lg-3 col-sm-4 col-xs-12">
			<div class="form-group">
				<label for="fecha_hasta">Fecha Hasta</label>
				<input type="date" name="fecha_hasta" required value="{{ $fecha_hasta }}" class="form-control">
			</div>
		</div>
		<div class="col-lg-3 col-sm-4 col-xs-12">
			<div class="form-group" style="padding-top: 24px;">
				<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
			</div>
		</div>
	</div>
</form>

<form action="{{ route('libro_compras.reporte') }}" method="POST" autocomplete="off" target="_blank" style="margin-bottom: 12px;">
	{{ csrf_field() }}
	<input type="hidden" name="fecha_desde" value="{{ $fecha_desde }}">
	<input type="hidden" name="fecha_hasta" value="{{ $fecha_hasta }}">
	<button class="btn btn-success" type="submit"><i class="fa fa-file-text-o"></i> Generar Reporte</button>
</form>

@php
	$totalGravada10 = 0;
	$totalGravada5 = 0;
	$totalIVA10 = 0;
	$totalIVA5 = 0;
	$totalExenta = 0;
	$totalCompras = 0;
@endphp

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead style="background-color:#A9D0F5">
					<th>Fecha</th>
					<th>Nro. Factura</th>
					<th>Timbrado</th>
					<th>Proveedor</th>
					<th>RUC</th>
					<th>Gravada 10%</th>
					<th>Gravada 5%</th>
					<th>IVA 10%</th>
					<th>IVA 5%</th>
					<th>Exenta</th>
					<th>Total</th>
					<th>Estado</th>
				</thead>
				@forelse ($libroCompras as $libro)
					@php
						$totalGravada10 += (int) $libro->montogravada10;
						$totalGravada5 += (int) $libro->montogravada5;
						$totalIVA10 += (int) $libro->montoiva10;
						$totalIVA5 += (int) $libro->montoiva5;
						$totalExenta += (int) $libro->montoexenta;
						$totalCompras += (int) $libro->monto;
					@endphp
					<tr>
						<td>{{ date('d/m/Y', strtotime($libro->fecha_factura)) }}</td>
						<td>{{ $libro->nro_factura }}</td>
						<td>{{ $libro->timbrado }}</td>
						<td>{{ $libro->proveedor }}</td>
						<td>{{ $libro->ruc }}</td>
						<td align="right">{{ number_format((int) $libro->montogravada10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->montogravada5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->montoiva10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->montoiva5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->montoexenta, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->monto, 0, ',', '.') }}</td>
						<td>{{ $libro->estado_libro }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="12" class="text-center">No se encontraron compras para el rango seleccionado.</td>
					</tr>
				@endforelse
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalExenta, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalCompras, 0, ',', '.') }}</strong></td>
					<td></td>
				</tr>
			</table>
		</div>
	</div>
</div>
@endsection
