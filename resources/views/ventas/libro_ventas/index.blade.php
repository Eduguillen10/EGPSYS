@extends ('layouts.admin')
@section ('contenido')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Libro de Ventas</h3>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form action="{{ route('libro_ventas.index') }}" method="GET" autocomplete="off" class="form-inline" style="margin-bottom: 12px;">
			<label for="fecha_desde" style="margin-right: 6px;">Fecha Desde</label>
			<input type="date" name="fecha_desde" required value="{{ $fecha_desde }}" class="form-control" style="width: 160px; margin-bottom: 6px;">
			<label for="fecha_hasta" style="margin-left: 10px; margin-right: 6px;">Fecha Hasta</label>
			<input type="date" name="fecha_hasta" required value="{{ $fecha_hasta }}" class="form-control" style="width: 160px; margin-bottom: 6px;">
			<button class="btn btn-primary" type="submit" style="margin-bottom: 6px;"><i class="fa fa-search"></i> Buscar</button>
			<a href="{{ route('libro_ventas.index') }}" class="btn btn-default" style="margin-bottom: 6px;">Limpiar</a>
		</form>

		<form action="{{ route('libro_ventas.generado') }}" method="POST" target="_blank" style="display:inline-block; margin-bottom: 12px;">
			{{ csrf_field() }}
			<input type="hidden" name="fecha_desde" value="{{ $fecha_desde }}">
			<input type="hidden" name="fecha_hasta" value="{{ $fecha_hasta }}">
			<button class="btn btn-success" type="submit"><i class="fa fa-file-text-o"></i> Generar Reporte</button>
		</form>
	</div>
</div>

@php
	$totalGravada10 = 0;
	$totalGravada5 = 0;
	$totalIVA10 = 0;
	$totalIVA5 = 0;
	$totalExenta = 0;
	$totalventaLv = 0;
	$totalventaNdv = 0;
	$totalventaNv = 0;
@endphp

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead style="background-color:#A9D0F5">
					<th>Tipo</th>
					<th>Fecha</th>
					<th>Nro. Documento</th>
					<th>Cliente</th>
					<th>Doc.</th>
					<th>Gravada 10%</th>
					<th>Gravada 5%</th>
					<th>IVA 10%</th>
					<th>IVA 5%</th>
					<th>Exenta</th>
					<th>Total</th>
				</thead>
				@forelse ($libroventas as $lv)
					@php
						$totalGravada10 += (int) $lv->totalgravada10;
						$totalGravada5 += (int) $lv->totalgravada5;
						$totalIVA10 += (int) $lv->totaliva10;
						$totalIVA5 += (int) $lv->totaliva5;
						$totalExenta += (int) $lv->totalexenta;
						$totalventaLv += (int) $lv->totalventa;
					@endphp
					<tr>
						<td>Venta</td>
						<td>{{ date('d/m/Y', strtotime($lv->fecha)) }}</td>
						<td>{{ $lv->nro_factura }}</td>
						<td>{{ $lv->cliente }}</td>
						<td>{{ $lv->num_documento }}</td>
						<td align="right">{{ number_format((int) $lv->totalgravada10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $lv->totalgravada5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $lv->totaliva10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $lv->totaliva5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $lv->totalexenta, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $lv->totalventa, 0, ',', '.') }}</td>
					</tr>
				@empty
					@if (count($nota_debitov) <= 0 && count($nota_creditov) <= 0)
						<tr>
							<td colspan="11" class="text-center">No se encontraron registros para el rango seleccionado.</td>
						</tr>
					@endif
				@endforelse

				@foreach ($nota_debitov as $ndv)
					@php
						$totalGravada10 += (int) $ndv->totalgravada10;
						$totalGravada5 += (int) $ndv->totalgravada5;
						$totalIVA10 += (int) $ndv->totaliva10;
						$totalIVA5 += (int) $ndv->totaliva5;
						$totalExenta += (int) $ndv->totalexenta;
						$totalventaNdv += (int) $ndv->totalventa;
					@endphp
					<tr>
						<td>Nota Debito</td>
						<td>{{ date('d/m/Y', strtotime($ndv->fecha_factura)) }}</td>
						<td>{{ $ndv->nro_factura }}</td>
						<td>{{ $ndv->cliente }}</td>
						<td>{{ $ndv->num_documento }}</td>
						<td align="right">{{ number_format((int) $ndv->totalgravada10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ndv->totalgravada5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ndv->totaliva10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ndv->totaliva5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ndv->totalexenta, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $ndv->totalventa, 0, ',', '.') }}</td>
					</tr>
				@endforeach

				@foreach ($nota_creditov as $nv)
					@php
						$totalGravada10 -= (int) $nv->totalgravada10;
						$totalGravada5 -= (int) $nv->totalgravada5;
						$totalIVA10 -= (int) $nv->totaliva10;
						$totalIVA5 -= (int) $nv->totaliva5;
						$totalExenta -= (int) $nv->totalexenta;
						$totalventaNv += (int) $nv->totalventa;
					@endphp
					<tr>
						<td>Nota Credito</td>
						<td>{{ date('d/m/Y', strtotime($nv->fecha_factura)) }}</td>
						<td>{{ $nv->nro_factura }}</td>
						<td>{{ $nv->cliente }}</td>
						<td>{{ $nv->num_documento }}</td>
						<td align="right">-{{ number_format((int) $nv->totalgravada10, 0, ',', '.') }}</td>
						<td align="right">-{{ number_format((int) $nv->totalgravada5, 0, ',', '.') }}</td>
						<td align="right">-{{ number_format((int) $nv->totaliva10, 0, ',', '.') }}</td>
						<td align="right">-{{ number_format((int) $nv->totaliva5, 0, ',', '.') }}</td>
						<td align="right">-{{ number_format((int) $nv->totalexenta, 0, ',', '.') }}</td>
						<td align="right">-{{ number_format((int) $nv->totalventa, 0, ',', '.') }}</td>
					</tr>
				@endforeach

				<tr>
					<td colspan="5" align="right"><strong>Total Neto:</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalExenta, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalventaLv + $totalventaNdv - $totalventaNv, 0, ',', '.') }}</strong></td>
				</tr>
			</table>
		</div>
	</div>
</div>

@endsection
