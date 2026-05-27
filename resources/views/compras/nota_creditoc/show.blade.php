@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
	@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
	@endif
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="idnota_credito_compra">Nro. Nota Crédito</label>
					<p>{{$nota_creditoc->idnota_creditoc}}</p>
			</div>
    	</div> 
		@if($nota_creditoc->idcompra)
			<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
				<div class="form-group">
					<label for="idcompra">Número de Compra</label>
					<p>{{ $nota_creditoc->idcompra }}</p>
				</div>
			</div>
		@endif
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$nota_creditoc->sucursal}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Depósito</label>
					<p>{{$nota_creditoc->deposito}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="usu_inser">Usuario</label>
					<p>{{$nota_creditoc->usu_inser}}</p>
			</div>
    	</div>      
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Fecha Factura</label>
					<p>{{date('d/m/Y', strtotime($nota_creditoc->fecha_factura))}}</p>
			</div>
		</div>				
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Proveedor</label>
					<p>{{$nota_creditoc->proveedor}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<p>{{$nota_creditoc->num_documento}}</p>
			</div>
    	</div>     	    		
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="nro_factura">Nro. de Nota de Crédito</label>
					<p>{{$nota_creditoc->nro_factura}}</p>
			</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="timbrado">Timbrado</label>
					<p>{{$nota_creditoc->timbrado}}</p>
			</div>
    	</div>    	
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="estado">Estado</label>
					<?php
						$estado = '';

						if ($nota_creditoc->estado == 'R') {
							$estado = 'Realizado';
						} elseif ($nota_creditoc->estado == 'P') {
							$estado = 'Pendiente';
						} else {
							$estado = 'Cancelado';
						}
					?>
					<p>{{$estado}}</p>
			</div>
    	</div>
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
    		<div class="form-group">
					<label for="concepto">Concepto</label>
					<p>{{$nota_creditoc->concepto}}</p>
			</div>
    	</div>   
    </div>
    <div class="row">
    	<div class="panel penel-primary">
    		<div class="panel-body">    			

		    	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
		    			<thead style="background-color:#A9D0F5">
		    				
		    				<th>Artículo</th>
		    				<th>Cantidad</th>
		    				<th>Precio Compra</th>
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
								<td>{{ number_format($det->precio_compra, 0, ',', '.') }}</td>
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

	<button class="btn btn-light" onclick="window.location.href='{{ url('compras/nota_creditoc') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>	

@endsection