@extends ('layouts.listar')
@section ('contenido')

<div class="row">
	<div class="text-center col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Libro de Compras</h3>
		<button class="btn btn-primary no-print" onclick="window.print()" style="margin-bottom: 12px;">Imprimir</button>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<p><b>Fecha Desde:</b> {{ date('d/m/Y', strtotime($fecha_desde)) }} <b>Fecha Hasta:</b> {{ date('d/m/Y', strtotime($fecha_hasta)) }}</p>
	</div>
	<div class="col-sm-12">
		@if (count($libroCompras) <= 0)
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				No se encontraron registros con esas caracteristicas de busqueda.
			</div>
		@endif
	</div>
</div>

@if (count($libroCompras) > 0)
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
				</thead>
				@foreach ($libroCompras as $libro)
					@php
						$totalGravada10 += (int) $libro->totalgravada10;
						$totalGravada5 += (int) $libro->totalgravada5;
						$totalIVA10 += (int) $libro->totaliva10;
						$totalIVA5 += (int) $libro->totaliva5;
						$totalExenta += (int) $libro->totalexenta;
						$totalCompras += (int) $libro->total;
					@endphp
					<tr>
						<td>{{ date('d/m/Y', strtotime($libro->fecha_factura)) }}</td>
						<td>{{ $libro->nro_factura }}</td>
						<td>{{ $libro->timbrado }}</td>
						<td>{{ $libro->proveedor }}</td>
						<td>{{ $libro->ruc }}</td>
						<td align="right">{{ number_format((int) $libro->totalgravada10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->totalgravada5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->totaliva10, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->totaliva5, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->totalexenta, 0, ',', '.') }}</td>
						<td align="right">{{ number_format((int) $libro->total, 0, ',', '.') }}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalGravada5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA10, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalIVA5, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalExenta, 0, ',', '.') }}</strong></td>
					<td align="right"><strong>{{ number_format($totalCompras, 0, ',', '.') }}</strong></td>
				</tr>
			</table>
		</div>
	</div>
</div>
@endif
@endsection
