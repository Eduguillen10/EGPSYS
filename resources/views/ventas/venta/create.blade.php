@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
			<h3>Nueva Venta</h3>
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
			<form action="{{url('ventas/venta')}}" method="POST" autocomplete="off" enctype="multipart/form-data"> 
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
				<select name="iddeposito" class="form-control selectpicker" id="iddeposito" data-Live-search="true" autofocus>
					@foreach($depositos as $dep)
					<option value="{{$dep->iddeposito}}">{{$dep->descripcion}}</option>
					@endforeach
				</select>
    		</div>
   		</div>   		
		   <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
				<div class="form-group">
						<label for="usuario">Usuario</label>
						<input type="text" value="{{ Auth::user()->name }}" class="form-control" placeholder="Usuario..." readonly>
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
					<input type="hidden" id="razon_social" name="razon_social" value="">		
								
					<input type="hidden" name="num_documento" id="num_documento" class="form-control" readonly>
	</div>   
		
	<div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d') )}}" placeholder="Fecha...">
                            <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                            <script>
                                $(document).ready(function () {
                                    $("#fecha").flatpickr({
                                        dateFormat: "d-m-y",
                                        minDate: "today",
                                        maxDate: new Date().getFullYear() + 1, -1, -1,
                                    });
                                });
                            </script>
                        </div>
                    </div>

					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Condición</label>
							<select name="condicion" class="form-control" id="condicion" onchange="actualizarFechaVencimiento()"> 				
								<option value="Contado">Contado</option>
								<option value="Credito 8 dias">Credito 8 dias</option>
								<option value="Credito 15 dias">Credito 15 dias</option>
								<option value="Credito 30 dias">Credito 30 dias</option>
								<option value="Credito 60 dias">Credito 60 dias</option>
								<option value="Credito 90 dias">Credito 90 dias</option>
								<option value="Credito 120 dias">Credito 120 dias</option>
							</select>
						</div>
					</div>

					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="form-group">
								<label for="obs">Obs</label>
								<input type="text" name="obs" value="{{old('obs')}}" class="form-control" placeholder="Obs...">
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
										@foreach($timbrados as $timbrado)
											<label type="hidden" for="timbrado {{ $timbrado->idtimbrado }} "></label>
											<input type="text" name="timbrado" value="{{ $timbrado->nro_timbrado }}" class="form-control" placeholder="Timbrado..." readonly>
											<input type="hidden" name="idtimbrado" value="{{ $timbrado->idtimbrado }}">
										@endforeach
									</div>
								</div>

                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                <thead style="background-color:#ffd966">
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Venta</th>
                                    <th>Subtotal</th>
                                    <th>Opciones</th>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="pidproducto[]" id="pidproducto" class="form-control selectpicker" data-live-search="true">
                                                @foreach($productos as $producto)
												<option value="{{ $producto->idproducto . '_' . floatval($producto->cantidad) . '_' . floatval($producto->precio_venta) }}">
													{{ $producto->producto }}
												</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="pcantidad[]" id="pcantidad" class="form-control" min="1"></td>
										<td><input type="number" disabled name="pprecio_venta[]" id="pprecio_venta" class="form-control" min="0" placeholder="Gs."></td>
                                        <td><input type="number" name="ptotal[]" id="ptotal" class="form-control" readonly></td>
                                        <td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(this)">Eliminar</button></td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">TOTAL</th>
                                        <th><input type="number" name="total_venta" id="total_venta" class="form-control" readonly></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            <button type="button" id="bt_add" class="btn btn-primary btn-sm">Agregar</button>
                            <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                            <button type="reset" class="btn btn-danger btn-sm">Cancelar</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>	

			<button class="btn btn-light" onclick="window.location.href='{{ url('ventas/venta') }}'" type="button">
				<i class="fa fa-arrow-left"></i> Volver
			</button>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
							$('#pidproducto').append(
  								'<option value="'+value.idproducto+'\_'+value.cantidad+'\_'+value.precio_venta+'">'+value.producto+'</option>'
							);
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
		if (datosProducto.length < 3) {
			console.error("Error: Datos del producto incorrectos", datosProducto);
			return;
    	}

	$("#pstock").val(parseFloat(datosProducto[1]));  // Asegurar que sea número
    $("#pprecio_venta").val(parseFloat(datosProducto[2]));
		
	}
	
	function agregar()
	{
		datosProducto=document.getElementById('pidproducto').value.split('_');
		
		if (datosProducto.length < 3) {
			alert("Error al ingresar el detalle de la venta, revise los datos del producto.");
			return;
    	}

		idproducto = datosProducto[0];
		stock = parseFloat(datosProducto[1]);
		precio_venta = parseFloat(datosProducto[2]);
		cantidad = parseFloat($("#pcantidad").val());

		if (isNaN(stock) || isNaN(precio_venta) || isNaN(cantidad)) {
			alert("Error: Alguno de los valores ingresados no es válido.");
			return;
		}
		idproducto=datosProducto[0];
		producto=$("#pidproducto option:selected").text();
		cantidad=$("#pcantidad").val();

		//descuento=$("#pdescuento").val();
		precio_venta=$("#pprecio_venta").val();
		stock=$("#pstock").val();
		
		if (idproducto!="" && cantidad!="" && cantidad>0 && precio_venta!="") 
		{
			if (typeof stock === "string") {
				stock = stock.trim(); 
			}
			if (typeof cantidad === "string") {
				cantidad = cantidad.trim();
			}
			stock = parseFloat(stock);
			cantidad = parseFloat(cantidad);
			console.log("Stock disponible:", stock);
			console.log("Cantidad a vender:", cantidad);

			if (stock>=cantidad) 
			{
				console.log("Stock suficiente para la venta.");

				subtotal[cont] = (cantidad * precio_venta);
                total = total + subtotal[cont];

                var fila = '<tr class="selected" id="fila' + cont + '">';
                fila = fila + '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
                fila = fila + '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>';
                fila = fila +'<td><input type="number" name="precio_venta[]" value="' + precio_venta + '"></td>';
                fila = fila + '<td>' + subtotal[cont] + '</td>';
				fila = fila + '<td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(' + cont + ')">Eliminar</button></td>';
                fila = fila + '</tr>';
                cont++;
                limpiar();
                $("#total").html("Gs/ " + total);
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
    		$('#razon_social').val(razon_social); // Asignar la razón social al campo oculto
        }

		datacliente();

</script>
@endpush
@endsection
