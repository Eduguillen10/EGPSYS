@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="presupuesto">Nro Presupuesto</label>
					<p>{{$presupuesto->idpresupuestocompra}}</p>
			</div>
    	</div>
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$presupuesto->descripcion}}</p>
			</div>
    	</div> 
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Proveedor</label>
					<p>{{$presupuesto->razonsocial}}</p>
			</div>
    	</div> 
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<p>{{$presupuesto->ruc}}</p>
			</div>
    	</div>  
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="idpedido">Número Pedido</label>
					<p>{{$presupuesto->idpedidocompra}}</p>
			</div>
    	</div>  	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="emplado">Usuario</label>
					<p>{{$presupuesto->usuario}}</p>
			</div>
    	</div>
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="observacion">Observacion</label>
				<p>{{ $presupuesto->idpedidocompra != null ? $presupuesto->observacion_pedido : '' }}</p>
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
								<td>{{ number_format($det->precio, 0, ',', '.') }}</td>
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
	<button class="btn btn-light" onclick="window.location.href='{{ url('compras/presupuesto') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>

@endsection