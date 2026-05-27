@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
			<h3>Nueva Nota de Crédito<a href="" data-target="#modal-elegir" data-toggle="modal">
				<button class="btn btn-info"> <i class="fa fa-download" aria-hidden="true"></i> Elegir</button></a>
			</h3>
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
		</div>	
	</div>	
	@include('compras.nota_creditoc.modalelegir')
			<form action="{{url('compras/nota_creditoc')}}" method="POST" autocomplete="off" enctype="multipart/form-data" > 
        	{{ csrf_field() }}
    <div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="idsucursal">Sucursal</label>
				@if($sucursales)
					<input type="text" name="idsucursal" class="form-control" value="{{ $sucursales->descripcion }}" readonly>
				@else
					<input type="text" name="idsucursal" class="form-control" value="No hay sucursal seleccionada" readonly>
				@endif
				<!-- Para el id -->
					<input type="hidden" name="idsucursal" value="{{ $sucursales->idsucursal }}">

			</div>
		</div>
   		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
				<label>Depósito</label>
				<select name="iddeposito" class="form-control selectpicker" id="psucursal" data-Live-search="true" autofocus>
					@foreach($depositos as $dep)
					<option value="{{$dep->iddeposito}}">{{$dep->descripcion}}</option>
					@endforeach
				</select>
    		</div>
   		</div>   		
		   <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
				<div class="form-group">
						<label for="usu_inser">Usuario</label>
						<input type="text" name="usu_inser"  value="{{ Auth::user()->name }}" class="form-control" placeholder="Usuario..." readonly>
				</div>
    		</div> 
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
				<label for="proveedor">Proveedor</label>
					<select name="idproveedor" id="idproveedor" class="form-control selectpicker" data-live-search="true" onchange="dataproveedor();" onkeyup="dataproveedor();" onclick="dataproveedor();" onfocus="dataproveedor();">
					@foreach($proveedores as $prov)
						<option value="{{$prov->idproveedor}}" data-ruc="{{$prov->num_documento}}" data-direccion="{{$prov->direccion}}">{{$prov->nombre}}</option>
					@endforeach
					</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<input type="text" name="ruc" id="ruc" class="form-control" readonly>
			</div>
	    </div>      	    	
	</div>

    <div class="row">
    	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="fecha_factura">Fecha Factura</label>
					<input type="date" name="fecha_factura" id="fecha_factura" value="{{ old('fecha_factura', date('Y-m-d') )}}" class="form-control" placeholder="Fecha Factura...">
			</div>
    	</div> 
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
				<label for="compra">Compra</label>
					<select name="idcompra" id="idcompra" class="form-control selectpicker" data-live-search="true" onchange="datacompra();" onkeyup="datacompra();" onclick="datacompra();" onfocus="datacompra();">
					@foreach($compras as $com)
						<option value="{{$com->idcompra}}" data-fecha="{{$com->fecha_factura}}" data-nro_factura="{{$com->nro_factura}}">{{$com->idcompra}} - {{$com->nro_factura}} - {{$com->fecha_factura}}</option>
					@endforeach
					</select>
			</div>
		</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="idcompra">Nro. Compra</label>
					<input type="number" name="idcompra" required value="{{old('idcompra')}}" class="form-control" placeholder="Nro. Compra...">
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="nro_factura">Nro. Nota de Credito</label>
					<input type="text" name="nro_factura" required value="{{old('nro_factura')}}" class="form-control" placeholder="Nro. Nota de Credito...">
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
					<label for="timbrado">Timbrado</label>
					<input type="text" name="timbrado" required value="{{old('timbrado')}}" class="form-control" placeholder="Timbrado...">
			</div>
    	</div>		
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
    		<div class="form-group">
					<label for="concepto">Concepto</label>
					<input type="text" name="concepto"  value="{{old('concepto')}}" class="form-control" placeholder="Concepto...">
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
    						<option value="{{$producto->idproducto}}">{{$producto->producto}}</option>
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
    			<label for="precio_compra">Precio Compra</label>
    			<input type="number" name="pprecio_compra" id="pprecio_compra" class="form-control" placeholder="P. Compra">
    		</div>
    	</div>
    	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<br>
    			<button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
    		</div>
    	</div>

    	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
    			<thead style="background-color:#A9D0F5">
    				<th>Opciones</th>
    				<th>Producto</th>
    				<th>Cantidad</th>
    				<th>Precio Compra</th>
    				<th>Total</th>
    			</thead>
    			<tfoot>
    				<th>TOTAL</th>
    				<th></th>
    				<th></th>
    				<th></th>
    				<th><h4 id="total">Gs/.0</h4></th>
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

	function actualizarFechaVencimiento() {
			var condicion = document.getElementById('condicion').value;
			var fechaFactura = document.getElementById('fecha_factura').value;

			// Lógica para extraer números y calcular la fecha de vencimiento (similar a lo que hiciste en PHP)
			var dias = 0;

			if (condicion !== 'Contado') {
				var matches = condicion.match(/\d+/g);
				dias = matches ? matches.reduce((acc, num) => acc + parseInt(num), 0) : 0;
			}

			var fechaFacturaObj = new Date(fechaFactura);
			var fechaVencimientoObj = new Date(fechaFacturaObj.getTime() + dias * 24 * 60 * 60 * 1000);
			var fechaVencimiento = fechaVencimientoObj.toISOString().split('T')[0];
			console.log("La función actualizarFechaVencimiento se está ejecutando.");
			// Actualizar el campo de fecha de vencimiento
			document.getElementById('fecha_vencimiento').value = fechaVencimiento;
		}
		
    $(document).ready(function(){	
       
        $('#bt_add').click(function(){
            agregar();
        });

        var cont = 0;
        total = 0;
        subtotal = [];
        $("#guardar").hide();
       

        function agregar() {
            var idproducto = $("#pidproducto").val();
            var producto = $("#pidproducto option:selected").text();
            var cantidad = $("#pcantidad").val();
            var precio_compra = $("#pprecio_compra").val();

            if (idproducto !== "" && cantidad !== "" && cantidad > 0 && precio_compra !== "") {
                subtotal[cont] = cantidad * precio_compra;
                total = total + subtotal[cont];

                var fila = '<tr class="selected" id="fila' + cont + '">';
                fila += '<td><button type="button" class="btn btn-warning" onclick="eliminar(' + cont + ');">X</td>';
                fila += '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
                fila += '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>';
                fila += '<td><input type="number" name="precio_compra[]" value="' + precio_compra + '"></td>';
                fila += '<td>' + subtotal[cont] + '</td>';
                fila += '</tr>';
                cont++;
                limpiar();
                $("#total").html("Gs/ " + total);
                evaluar();
                $("#detalles").append(fila);
            } else {
                alert("Error al ingresar el detalle del ingreso, revise los datos del producto");
            }
        }

        function limpiar() {
            $("#pcantidad").val("");
            $("#pprecio_compra").val("");
        }

        function evaluar() {
            if (total > 0) {
                $("#guardar").show();
            } else {
                $("#guardar").hide();
            }
        }

        function eliminar(index) {
            total = total - subtotal[index];
            $("#total").html("Gs/. " + total);
            $("#fila" + index).remove();
            evaluar();
        }

       		
    });
	 // Para que me estire el número de documento en el campo ruc al seleccionar el proveedor
	 function dataproveedor() {
            var ruc = $('#idproveedor').find('option:selected').data('ruc');
            $('#ruc').val(ruc);

            var direccion = $('#idproveedor').find('option:selected').data('direccion');
            $('#direccion').val(direccion);
        }

        dataproveedor();
</script>
@endpush

@endsection