@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Cobro Detalle</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
			
		</div>
		@endif
	</div>

</div>
<br>
<div class="row" style="padding-right: 1em;padding-left: 1em;">
	<div class="panel panel-primary">
		<div class="panel-body">
			<div class="row">
			@foreach ($cobro as $c)
				<div class="col-lg-2 col-sm-2 col-xs-6">
					<div class="form-group">
						<label for="nombre">Registro Nro</label>
						<input type="text" name="id_cobro" id="id_cobro" value="{{ $c->id_cobro }}" class="form-control" readonly>
					</div>
				</div>

				<div class="col-lg-2 col-sm-2 col-xs-6">
					<div class="form-group">
						<label for="nombre">Fecha</label>
						<input type="text" name="fecha_cobro" id="fecha_cobro" class="form-control" value="{{date('d/m/Y', strtotime($c->fecha_cobro))}}" readonly>
					</div>
				</div>
				
				<div class="col-lg-2 col-sm-2 col-xs-6">
					<div class="form-group">
						<label for="nombre">Caja</label>
						<input type="text" name="" value="{{$caja->idcaja}} - {{$caja->descripcion}} " class="form-control" disabled>
					</div>
				</div>
				<div class="col-lg-2 col-sm-2 col-xs-6">
					<div class="form-group">
						<label for="nombre">Nro Apertura</label>
						<input type="text" name="" value="{{$c->idapertura}}" class="form-control" disabled>
					</div>
				</div>

				<div class="col-lg-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<label for="nombre">Cliente</label>
						<input type="text" name="" value="{{$cliente->idcliente}} - {{$cliente->nombre}} " class="form-control" disabled>
					</div>
				</div>
				
				<div class="col-lg-3 col-sm-3 col-xs-6">
					<div class="form-group">
						<label>Total Factura</label>
						<input type="text" name="" value="{{number_format($c->total_cobrofactura, 2, ',', '.')}}" class="form-control" disabled>
					</div>
				</div>
				<div class="col-lg-3 col-sm-3 col-xs-6">
					<div class="form-group">
						<label>Total Cobrado</label>
						<input type="text" name="" value="{{number_format($c->total_cobro, 2, ',', '.')}}" class="form-control" disabled>
					</div>
				</div>
			@endforeach
			</div>
		</div>
	</div>
</div>
<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Factura</a></li>
		<li id="forma_de_pago" role="presentation"><a href="#forma_pago" aria-controls="profile" role="tab" data-toggle="">Forma de Pago</a></li>
		
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="home">
			<br>
	
			<form action="{{url('ventas/cobrodetalle')}}" method="POST" autocomplete="off" enctype="multipart/form-data"> 
        	{{ csrf_field() }}
    <div class="row">
	<input type="hidden" name="id_cobro" value="{{$cobro->id_cobro}}" class="form-control">
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
			<div class="form-group">
				<label for="factura">Nro. Factura</label>
				<select name="nro_factura" id="nro_factura" class="form-control selectpicker" data-live-search="true" onchange="datafactura();" onkeyup="datafactura();" onclick="datafactura();" onfocus="datafactura();">
					@foreach($factura_venta as $fac)
						<option value="{{$fac->idventa}}" data-condicion="{{$fac->condicion}}">{{$fac->idventa}} - {{date('d/m/Y', strtotime($fac->fecha))}} - {{$fac->nro_factura}} - {{$fac->cliente}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-xs-6">
			<div class="form-group">
				<label for="nro_factura">Nro Factura</label>
				<input id="nro_factura" type="text" name="" value="" class="form-control" readonly>
			</div>
		</div>
		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="timbrado">Timbrado</label>
				<input id="timbrado" type="text" name="" value="" class="form-control" readonly>
			</div>
		</div>
		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="condicion_venta">Condicion Venta</label>
				<input id="condicion_venta" type="text" name="" value="" class="form-control" readonly>
				<input id="condicionvta_cta_cte_cli" type="hidden" name="" value="" class="form-control">
			</div>
		</div>
		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="total_factura">Total Factura</label>
				<input id="total_factura" type="text" name="" value="" class="form-control" readonly>
			</div>
		</div>
		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="total_cobro">Total a Cobrar</label>
				<input id="total_cobro" type="text" name="" value="" class="form-control" readonly>
			</div>
		</div>
		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="importe_cobro">Importe de Cobro</label>
				<input id="importe_cobro" type="text" name="importe_cobro" value="" class="form-control">
			</div>
		</div>
	</div>
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
				<label for="cliente">Razón Social</label>
					<select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true" onchange="datacliente();" onkeyup="datacliente();" onclick="datacliente();" onfocus="datacliente();">
					@foreach($clientes as $cli)
						<option value="{{$cli->idcliente}}" data-num_documento="{{$cli->num_documento}}" data-direccion="{{$cli->direccion}}" data-nombre="{{$cli->nombre}}">{{$cli->nombre}} - {{$cli->num_documento}}</option>
					@endforeach										
					</select>					
			</div>
		</div> 		
					<input type="hidden" id="nombre" name="nombre" value="">		
								
					<input type="hidden" name="num_documento" id="num_documento" class="form-control" readonly>

		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="fecha">Fecha</label>
					<input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d') )}}" class="form-control" placeholder="Fecha...">
			</div>
    	</div>  			
	         	    	
	</div>   
	
    <div class="row">
    	<div class="panel penel-primary">
    		<div class="panel-body">
    			<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
    				<div class="form-group">
    					<label>Producto</label>
    					<select name="pidproducto" class="form-control selectpicker" id="pidproducto" data-Live-search="true">
    						@foreach($productos as $producto)
    						<option value="{{$producto->idproducto}}_{{$producto->cantidad}}_{{$producto->precio_venta}}">{{$producto->producto}}</option>
    						@endforeach
    					</select>
    				</div>
    			</div>

    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<label for="cantidad">Cantidad</label>
    			<input type="number" name="pcantidad" id="pcantidad" class="form-control" placeholder="Cantidad">
    		</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<label for="stock">Stock</label>
    			<input type="number" disabled name="pstock" id="pstock" class="form-control" placeholder="Stock">
    		</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<label for="precio_venta">Precio Venta</label>
    			<input type="number" disabled name="pprecio_venta" id="pprecio_venta" class="form-control" placeholder="P. Venta"> 
    		</div>
    	</div>  	
    	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
    		</div>
    	</div>

    	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
    			<thead style="background-color:#ffd966">
    				<th>Opciones</th>
    				<th>Producto</th>
    				<th>Cantidad</th>
    				<th>Precio de Venta</th>    				
    				<th>Subtotal</th>
    			</thead>
    			<tfoot>
    				<th>TOTAL</th>
    				<th></th>
    				<th></th>
    				<th></th>    				
    				<th><h4 id="total">Gs/.0</h4><input type="hidden" name="total_venta" id="total_venta"></th>
    			</tfoot>
    			<tbody>

    			</tbody>
    		</table>
    	</div>

    		</div>
    	</div>	
    	
    	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
				<div class="form-group">
					<input name="_token" value="{{ csrf_token() }}" type="hidden">
					<button class="btn btn-primary" type="submit">Guardar</button>
					<button class="btn btn-danger" type="reset">Cancelar</button>
				</div>
    	</div>			
	</div>
			</form>	

@push ('scripts')
<script>
	$(document).ready(function(){

		mostrarValores();

		$('#bt_add').click(function(){
			agregar();
		});
	});
	$('#iddeposito').change(function() {
                var iddeposito = $(this).val();
				console.log(iddeposito);
                $.ajax({
                    type: 'GET',
                    url: '/obtener-productos/' + iddeposito,
                    success: function(data) {
                        $('#pidproducto').empty();
                        $.each(data, function(key, value) {
                            $('#pidproducto').append('<option value="' + value.idproducto + '\_' + value.cantidad + '">' + value.producto + '</option>');
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        

var cont=0;
total=0;
subtotal=[];
$("#guardar").hide();
$("#pidproducto").change(mostrarValores);

	function mostrarValores()
	{
		datosProducto=document.getElementById('pidproducto').value.split('_');
		$("#pprecio_venta").val(datosProducto[2]);
		$("#pstock").val(datosProducto[1]);
	}
	
	function agregar()
	{
		datosProducto=document.getElementById('pidproducto').value.split('_');
		
		idproducto=datosProducto[0];
		producto=$("#pidproducto option:selected").text();
		cantidad=$("#pcantidad").val();

		//descuento=$("#pdescuento").val();
		precio_venta=$("#pprecio_venta").val();
		stock=$("#pstock").val();

		if (idproducto!="" && cantidad!="" && cantidad>0 && precio_venta!="") 
		{
			stock = stock.trim(); // Eliminar espacios en blanco
			cantidad = cantidad.trim(); // Eliminar espacios en blanco
			stock = parseInt(stock);
			cantidad = parseInt(cantidad);
			console.log("Stock disponible:", stock);
			console.log("Cantidad a vender:", cantidad);

			if (stock>=cantidad) 
			{
				console.log("Stock suficiente para la venta.");
				subtotal[cont]=(cantidad*precio_venta);
				total=total+subtotal[cont];

				var    fila = '<tr class="selected" id="fila' + cont + '">' ;
	            fila = fila + '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
	            fila = fila + '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>' ;
	            fila = fila + '<td><input type="number" name="precio_venta[]" value="' + precio_venta + '"></td>' ;
	            //fila = fila + '<td><input type="number" name="descuento[]" value="' + descuento + '"></td>' ;
	            fila = fila + '<td>' + subtotal[cont] + '</td>' ;
				fila = fila + '<td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(' + cont + ')">Eliminar</button></td>';
	            fila = fila + '</tr>' ;
	            cont++;
	            limpiar();
	            $("#total").html("Gs/ "+total);
	            $("#total_venta").val(total);
	            evaluar();
	            $("#detalles").append(fila);
			}
			else
			{
				console.log("La cantidad a vender supera el stock.");
				alert ('La cantidad a vender supera el stock');
			}
		}
		else
		{
			alert("Error al ingresar el detalle de la venta, revise los datos del producto");
		}
	}

	function limpiar(){
		$("#pcantidad").val("");
		//$("#pdescuento").val("");
		$("#pprecio_venta").val("");
	}

	function evaluar()
	{
		if (total>0)
		{
			$("#guardar").show();
		}
		else
		{
			$("#guardar").hide();
		}
	}

	function eliminar(index){
		total=total-subtotal[index];
		$("#total").html("Gs/. "+ total);
		$("#total_venta").val(total);
		$("#fila" + index).remove();
		evaluar();
	}

	function datacliente() {
            var num_documento = $('#idcliente').find('option:selected').data('num_documento');
            $('#num_documento').val(num_documento);			

            var direccion = $('#idcliente').find('option:selected').data('direccion');
            $('#direccion').val(direccion);

			var razon_social = $('#idcliente').find('option:selected').data('nombre'); // Obtener la razón social
    		$('#nombre').val(nombre); // Asignar la razón social al campo oculto
        }

		datacliente();

</script>
@endpush
@endsection