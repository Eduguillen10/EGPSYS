@extends ('layouts.admin')
@section ('contenido')
@include('compras.partials.create-styles')

	<div class="row tm-create-header">
		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
			<h3>Nuevo Pedido</h3>
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
			<form action="{{url('compras/pedido')}}" method="POST" autocomplete="off" enctype="multipart/form-data"> 
        	{{ csrf_field() }}
    <div class="tm-create-panel">
    	<div class="row">
    		<div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
				<div class="form-group">
					<label for="idsucursal">Sucursal</label>
					@if($sucursales)
						<input type="text" class="form-control" value="{{ $sucursales->descripcion }}" readonly>
					@else
						<input type="text" class="form-control" value="No hay sucursal seleccionada" readonly>
					@endif
					<input type="hidden" name="idsucursal" value="{{ $sucursales ? $sucursales->idsucursal : '' }}">
				</div>
			</div>

	    	<div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
				<div class="form-group">
					<label for="usuario">Usuario</label>
					<input type="text" value="{{ Auth::user()->name }}" class="form-control" placeholder="Usuario..." readonly>
				</div>
	    	</div>

	    	<div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
				<div class="form-group">
					<label for="fecha">Fecha</label>
					<input type="date" value="{{ date('Y-m-d') }}" class="form-control" readonly>
				</div>
	    	</div>
	    </div>

	    <div class="row">
	    	<div class="col-lg-9 col-sm-12 col-md-9 col-xs-12">
				<div class="form-group">
					<label for="observacion">Observacion</label>
					<input type="text" name="observacion" value="{{old('observacion')}}" class="form-control" placeholder="Observacion...">
				</div>
	    	</div>
	    </div>
    </div>

    <div class="tm-detail-panel">
    	<div class="tm-detail-heading">Detalle del Pedido</div>
    	<div class="tm-detail-body">
    		<div class="row">
    			<div class="col-lg-7 col-sm-12 col-md-7 col-xs-12">
    				<div class="form-group">
    					<label>Producto</label>
    					<select name="pidproducto" class="form-control selectpicker" id="pidproducto" data-live-search="true" data-width="100%" data-size="10" data-container="body" title="Seleccione producto...">
    						@foreach($productos as $producto)
    						<option value="{{$producto->idproducto}}">{{$producto->productos}} {{$producto->marcas}}</option>
    						@endforeach
    					</select>
   				</div>
    			</div>

		    	<div class="col-lg-2 col-sm-6 col-md-2 col-xs-12">
		    		<div class="form-group">
		    			<label for="cantidad">Cantidad</label>
		    			<input type="number" name="pcantidad" id="pcantidad" class="form-control" placeholder="Cantidad">
		    		</div>
		    	</div>
		    	
		    	<div class="col-lg-2 col-sm-6 col-md-2 col-xs-12">
		    		<div class="form-group">
		    			<label>&nbsp;</label>
		    			<button type="button" id="bt_add" class="btn btn-primary form-control">Agregar</button>
		    		</div>
		    	</div>
    		</div>

	    	<div class="table-responsive">
	    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
	    			<thead>
	    				<th>Opciones</th>
	    				<th>Producto</th>
	    				<th>Cantidad</th>
	    				</thead>
	    			<tfoot>
	    				<th></th>
	    				<th></th>
	    				<th></th>
	    			</tfoot>
	    			<tbody>

	    			</tbody>
	    		</table>
	    	</div>

    	</div>	
    </div>
    	
    	<div class="tm-create-panel" id="guardar">
				<div class="form-group">
					<input name="_token" value="{{ csrf_token() }}" type="hidden">
					<button class="btn btn-success" type="submit">Guardar</button>
					<button class="btn btn-danger" type="reset">Cancelar</button>
					<button class="btn btn-default" onclick="window.location.href='{{ url('compras/pedido') }}'" type="button">
						<i class="fa fa-arrow-left"></i> Volver
					</button>
				</div>
    	</div>			
			</form>	
			
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@push ('scripts')
<script>
	$(document).ready(function(){
    console.log('Documento listo');
    $('#bt_add').click(function(){
        console.log('Botón Agregar clickeado');
        agregar();
    });
});

var cont=0;
//total=0;
//subtotal=[];
//$("#guardar").hide();
	
	function agregar()
	{
		console.log('Función agregar ejecutada');
		idproducto=$("#pidproducto").val();
		producto=$("#pidproducto option:selected").text();
		cantidad=$("#pcantidad").val();
		//precio_compra=$("#pprecio_compra").val();
		//precio_venta=$("#pprecio_venta").val();

		if (idproducto!="" && cantidad!="" && cantidad>0) 
		{
			//subtotal[cont]=(cantidad*precio_compra);
			//total=total+subtotal[cont];

			var    fila = '<tr class="selected" id="fila' + cont + '">' ;
            fila = fila + '<td><button type="button" class="btn btn-warning" onclick="eliminar(' + cont + ');">X</td>' ;
            fila = fila + '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>' ;
            fila = fila + '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>' ;
            //fila = fila + '<td><input type="number" name="precio_compra[]" value="' + precio_compra + '"></td>' ;
            //fila = fila + '<td><input type="number" name="precio_venta[]" value="' + precio_venta + '"></td>' ;
            //fila = fila + '<td>' + subtotal[cont] + '</td>' ;
            fila = fila + '</tr>' ;
            cont++;
            limpiar();
            //$("#total").html("S/ "+total);
            //evaluar();
            $("#detalles").append(fila) ;
		}
		else
		{
			alert("Error al ingresar el detalle del pedido, revise los datos del producto");
		}
	}

	function limpiar(){
		$("#pcantidad").val("");
		//$("#pprecio_compra").val("");
		//$("#pprecio_venta").val("");
	}

	//function evaluar()
	//{
		//if (total>0)
		//{
		//	$("#guardar").show();
		//}
		//else
		//{
		//	$("guardar").hide();
		//}
	//}

	function eliminar(index){
		$("#fila" + index).remove(); // Eliminar la fila con el índice especificado
   		//evaluar(); // Llamar a la función evaluar si es necesario

		//total=total-subtotal[index];
		//$("#total").html("Gs/. "+ total);
		//$("#fila" + index).remove();
		//evaluar();
	}

</script>
@endpush
@endsection
