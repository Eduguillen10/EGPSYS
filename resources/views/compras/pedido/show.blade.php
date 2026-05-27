@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<label>Nro. del pedido</label>
    			<p>{{$pedidos_compras->idpedidocompra}}</p>
    		</div>
    	</div>  
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="fecha">Fecha</label>
					<p>{{date('d/m/Y', strtotime($pedidos_compras->fecha))}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="estado">Estado</label>	
					<p>{{$pedidos_compras->estado}}</p>				
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="usuario">Usuario</label>
					<p>{{$pedidos_compras->usuario}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$pedidos_compras->sucursal_descripcion}}</p>
			</div>
    	</div>  
    </div>
    <div class="row"> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="observacion">Observacion</label>
					<p>{{$pedidos_compras->observacion}}</p>
			</div>
    	</div>    	
    	    	
    </div>
    <div class="row">
    	 <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
    			<thead style="background-color:#A9D0F5">
    				<th>Item</th>
    				<th>Código Producto</th>
    				<th>Producto</th>
    				<th>Total</th>
    			</thead>
    			
    			<tbody>
    				<?php
    					$sumcantidad=0;
    				?>
    				@foreach($detallePedido as $pd)
    				<tr>
    					<td>{{$pd->items}}</td>
    					<td>{{$pd->idproducto}}</td>
    					<td>{{$pd->producto}}</td>
    					<td>{{$pd->cantidad}}</td>
    					
    				</tr>
    				<?php
    					$sumcantidad= $sumcantidad + $pd->cantidad;
    				?>
    				@endforeach
    				<tr>
    					<td colspan="3">Cantidad</td>
    					<th>{{$sumcantidad}}</th>
    				</tr>
    			</tbody>

    		</table>
    	</div>
    		   	
    			
	</div>	
	<button class="btn btn-light" onclick="window.location.href='{{ url('compras/pedido') }}'" type="button">
		<i class="fa fa-arrow-left"></i> Volver
	</button>

@endsection