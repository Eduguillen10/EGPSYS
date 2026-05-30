@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="idpresupuestocompra">Número Presupuesto</label>
					<p>{{$orden->idpresupuestocompra}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="idordencompra">Número Orden</label>
					<p>{{$orden->idordencompra}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$orden->sucursal}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Depósito</label>
					<p>{{$orden->deposito}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="emplado">Usuario</label>
					<p>{{$orden->usuario}}</p>
			</div>
    	</div>      	  
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Fecha</label>
					<p>{{ date('d/m/Y', strtotime($orden->fecha)) }}</p>
			</div>
    	</div>    	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Proveedor</label>
					<p>{{$orden->proveedor}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<p>{{$orden->ruc}}</p>
			</div>
    	</div>
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Direccion</label>
					<p>{{$orden->direccion}}</p>
			</div>
    	</div>   
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="estado">Estado</label>
					<?php
    					$estado='';
    					if ($orden->estado == 'Pendiente') {
    						$estado='Pendiente';
    					}else {
    						$estado='Cancelado';
    					}
    				?>
					<p>{{$estado}}</p>
			</div>
    	</div>   	     	 
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="observacion">Observacion</label>
				<p>{{ $orden->idpresupuestocompra ? $orden->observacion_presupuesto : $orden->observacion }}</p>
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
								<td>{{ number_format($det->montoitems, 0, ',', '.') }}</td>
		    				</tr>
		    				<?php
    							$sumcantidad= $sumcantidad + $det->montoitems;
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
	<button class="btn btn-light" onclick="window.location.href='{{ url('compras/orden') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>

@endsection