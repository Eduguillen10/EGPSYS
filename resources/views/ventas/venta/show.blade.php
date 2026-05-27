@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif
		@if(session('error'))
			<div class="alert alert-danger">
				{{ session('error') }}
			</div>
		@endif   
	</div>
</div>
	<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
						<label for="idorden">Nro. Venta</label>
						<p>{{$ventas->idventa}}</p>
				</div>
			</div> 
		</div>	
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$ventas->sucursal}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Depósito</label>
					<p>{{$ventas->deposito}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="usuario">Usuario</label>
					<p>{{$ventas->usuario}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Fecha</label>
					<p>{{date('d/m/Y', strtotime($ventas->fecha))}}</p>
			</div>
		</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="cliente">Razón Social</label>
					<p>{{$ventas->cliente}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="cliente">RUC - C.I.</label>
					<p>{{$ventas->num_documento}}</p>
			</div>
    	</div>    	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="nro_factura">Nro. Factura</label>
					<p>{{$ventas->nro_factura}}</p>
			</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="nro_timbrado">Timbrado</label>
					<p>{{$ventas->nro_timbrado}}</p>
			</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="condicion">Condicion</label>
					<p>{{$ventas->condicion}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="estado">Estado</label>
					<?php
						$estado = '';

						if ($ventas->estado == 'Realizado' || $ventas->estado == 'R') {
							$estado = 'Realizado';
						} elseif ($ventas->estado == 'Pendiente' || $ventas->estado == 'P') {
							$estado = 'Pendiente';
						} elseif ($ventas->estado == 'Finalizado') { // Agregar esta condición para 'Finalizado'
							$estado = 'Finalizado';
						} elseif ($ventas->estado == 'PC') { // Agregar esta condición para 'Crédito'
							$estado = 'Pend. Cobro';
						}elseif ($ventas->estado == 'Anulado' || $ventas->estado == 'A') {
						 $estado = 'Anulado';
						} else {
							$estado = $ventas->estado; // por si quedó basura vieja
						}
					?>
					<p>{{$estado}}</p>
			</div>
    	</div>
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
    		<div class="form-group">
					<label for="obs">Obs</label>
					<p>{{$ventas->obs}}</p>
			</div>
    	</div>
    </div>
    <div class="row">
    	<div class="panel penel-primary">
    		<div class="panel-body">    			

		    	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
		    			<thead style="background-color:#ffd966">
		    				
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
		    				<th><h4 id="total"></h4></th>
		    			</tfoot>
		    			<tbody>
						<?php
		    				$sumcantidad=0;
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
    							$sumcantidad= $sumcantidad + $det->totalitems;
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

	<button class="btn btn-light" onclick="window.location.href='{{ url('ventas/venta') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>	
	@php
		$mostrarCobrar = false;

		if (!in_array(strtoupper(trim((string) $ventas->estado)), ['A', 'ANULADO', 'ANULADA'], true)) {

			// Saldo pendiente (fuente principal)
			$cta = DB::table('cuenta_cobrar')->where('idventa', $ventas->idventa)->first();
			$saldo = $cta ? (int)$cta->saldo : (int)$ventas->saldo_factura;

			// Cobros realizados imputados a esta venta (fuente de control)
			$cobrosRealizados = DB::table('det_cobro as dc')
				->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
				->where('dc.idventa', $ventas->idventa)
				->where('dc.monto_detcobro', '>', 0)
				->where('c.cobro_estado', 'Realizado')
				->count();

			// Mostrar si hay saldo y aún no está totalmente cobrada
			// (si manejás parcial, esto permite cobrar mientras saldo > 0)
			$mostrarCobrar = ($saldo > 0);
		}
	@endphp
@endsection
