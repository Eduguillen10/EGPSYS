@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="idnota_debito_venta">Nro. Nota Debito</label>
				<p>{{$nota_debitov->idnota_debitov}}</p>
			</div>
		</div>
		@if($nota_debitov->idventa)
			<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
				<div class="form-group">
					<label for="idventa">NÃºmero de Venta</label>
					<p>{{ $nota_debitov->idventa }}</p>
				</div>
			</div>
		@endif
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="sucursal">Sucursal</label>
				<p>{{$nota_debitov->sucursal}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="sucursal">Depósito</label>
				<p>{{$nota_debitov->deposito}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="usuario">Usuario</label>
				<p>{{$nota_debitov->usuario}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="factura">Fecha Factura</label>
				<p>{{date('d/m/Y', strtotime($nota_debitov->fecha_factura))}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="cliente">Razón Social</label>
				<p>{{$nota_debitov->cliente}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="cliente">RUC-C.I.</label>
				<p>{{$nota_debitov->num_documento}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="nro_factura">Nro. Factura</label>
				<p>{{$nota_debitov->nro_factura}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="timbrado">Timbrado</label>
				<p>{{$nota_debitov->timbrado}}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="estado">Estado</label>
				<?php
	$estado = '';

	if ($nota_debitov->estado == 'R' || $nota_debitov->estado == 'Realizado') {
		$estado = 'Realizado';
	} elseif ($nota_debitov->estado == 'P' || $nota_debitov->estado == 'Pendiente') {
		$estado = 'Pendiente';
	} elseif ($nota_debitov->estado == 'A' || $nota_debitov->estado == 'Anulado' || $nota_debitov->estado == 'Anulada') {
		$estado = 'Anulado';
	} else {
		$estado = $nota_debitov->estado; // fallback
	}

						?>
				<p>{{$estado}}</p>
			</div>
		</div>
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="concepto">Concepto</label>
				<p>{{$nota_debitov->concepto}}</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="panel penel-primary">
			<div class="panel-body">

				<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
					<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#A9D0F5">

							<th>Producto</th>
							<th>Cantidad</th>
							<th>Precio Venta</th>
							<th>Iva 10</th>
							<th>Iva 5</th>
							<th>Gravada 10</th>
							<th>Gravada 5</th>
							<th>Exenta</th>
							<th>Total</th>
						</thead>
						<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th>
								<h4 id="total"></h4>
							</th>
						</tfoot>
						<tbody>
							<?php
	$sumcantidad = 0;
							?>
							@foreach($detalles as $det)
													<tr>
														<td>{{$det->producto}}</td>
														<td>{{ number_format($det->cantidad, 0, ',', '.') }}</td>
														<td>{{ number_format($det->precio_venta, 0, ',', '.') }}</td>
														<td>{{ number_format($det->iva10, 0, ',', '.') }}</td>
														<td>{{ number_format($det->iva5, 0, ',', '.') }}</td>
														<td>{{ number_format($det->gravada10, 0, ',', '.') }}</td>
														<td>{{ number_format($det->gravada5, 0, ',', '.') }}</td>
														<td>{{ number_format($det->exenta, 0, ',', '.') }}</td>
														<td>{{ number_format($det->totalitems, 0, ',', '.') }}</td>
													</tr>
													<?php
								$sumcantidad = $sumcantidad + $det->totalitems;
														?>
							@endforeach
							<td colspan="8">Total</td>
							<th>{{ $sumcantidad != 0 ? number_format($sumcantidad, 0, ',', '.') : 0 }}</th>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>

	<button class="btn btn-light" onclick="window.location.href='{{ url('ventas/nota_debitov') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>

@endsection
