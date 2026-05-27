@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
			<h3>Nueva Compra
				<a href="" data-target="#modal-elegir" data-toggle="modal">
				<button class="btn btn-info"> <i class="fa fa-download" aria-hidden="true"></i> Seleccionar Orden</button></a>
			</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif
			@if(session('success'))
				<div class="alert alert-success">
					{{ session('success') }}
				</div>
			@endif
		</div>	
	</div>	
	@include('compras.compra.modalelegir')
			<form action="{{url('compras/compra')}}" method="POST" autocomplete="off" enctype="multipart/form-data" > 
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
						<label for="usuario">Usuario</label>
						<input type="text" name="usuario"  value="{{ Auth::user()->name }}" class="form-control" placeholder="Usuario..." readonly>
				</div>
    		</div> 
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
            <div class="form-group">
                <label for="proveedor">Proveedor</label>
                <select name="idproveedor" id="idproveedor" class="form-control selectpicker" data-live-search="true" onchange="dataproveedor();" onkeyup="dataproveedor();" onclick="dataproveedor();" onfocus="dataproveedor();">
                    @foreach ($proveedores as $prov)
                        <option value="{{ $prov->idproveedor }}" data-ruc="{{ $prov->ruc }}" data-direccion="{{ $prov->direccion }}">{{ $prov->razonsocial }} ({{ $prov->ruc }})</option>
                    @endforeach
                </select>
                <script>
                    $(document).ready(function () {
                        $("#idproveedor").change(function () {
                            var idproveedor = $(this).val();
                            if (idproveedor == "") {
                                alert("Debe seleccionar un proveedor");
                                $(this).focus();
                            }
                        });
                    });
                </script>
            </div>
        </div>

		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    		<div class="form-group">
        		<label for="ruc">RUC</label>
        		<input type="text" name="ruc" id="ruc" class="form-control" readonly>
    		</div>
		</div>

	<div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-body">

                    <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d') )}}" placeholder="Fecha..." readonly>
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

					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                        <div class="form-group">
                            <label>Fecha Factura</label>
                            <input type="date" name="fecha_factura" id="fecha_factura" class="form-control" value="{{ old('fecha_factura', date('Y-m-d'))}}" placeholder="Fecha Factura...">
                            <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                            <script>
                                $(document).ready(function () {
                                    $("#fecha_factura").flatpickr({
                                        dateFormat: "d-m-y",
                                        minDate: "today",
                                        maxDate: new Date().getFullYear() + 1, -1, -1,
                                    });
                                });
                            </script>
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
							<input type="text" name="concepto" value="{{ old('concepto') }}" class="form-control" placeholder="Concepto...">
						</div>
    				</div>

					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    					<div class="form-group">
							<label for="nro_factura">Nro. Factura</label>
							<input type="text" name="nro_factura" required value="{{old('nro_factura')}}" class="form-control" placeholder="Nro. Factura...">
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

                    <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', date('Y-m-d') )}}" placeholder="Fecha Vencimiento..." readonly>
                                <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                                <script>
                                    $(document).ready(function () {
                                        $("#fecha_vencimiento").flatpickr({
                                            dateFormat: "d-m-y",
                                            minDate: "today",
                                            maxDate: new Date().getFullYear() + 1, -1, -1,
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                <thead style="background-color:#ffd966">
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Compra</th>
                                    <th>Subtotal</th>
                                    <th>Opciones</th>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="pidproducto[]" id="pidproducto" class="form-control selectpicker" data-live-search="true">
                                                @foreach($productos as $prod)
                                                <option value="{{$prod->idproducto}}">{{$prod->producto}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="pcantidad[]" id="pcantidad" class="form-control" min="1"></td>
										<td><input type="number" name="pprecio_compra[]" id="pprecio_compra" class="form-control" min="0" placeholder="Gs."></td>
                                        <td><input type="number" name="ptotal[]" id="ptotal" class="form-control" readonly></td>
                                        <td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(this)">Eliminar</button></td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">TOTAL</th>
                                        <th><input type="number" name="total" id="total" class="form-control" readonly></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="form-group">
                            <button type="button" id="btnagregar" class="btn btn-primary btn-sm">Agregar</button>
                            <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                            <a href="{{url('compras/compra')}}" class="btn btn-default btn-sm">Cancelar</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</form>

<button class="btn btn-light" onclick="window.location.href='{{ url('compras/compra') }}'" type="button">
				<i class="fa fa-arrow-left"></i> Volver
			</button>

    <script>
            function actualizarFechaVencimiento() {
			var condicion = document.getElementById('condicion').value;
			var fechaFactura = document.getElementById('fecha_factura').value;
			
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
    </script>
    	
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@push ('scripts')
<script>
    $(document).ready(function(){	
       
        $('#btnagregar').click(function(){
            agregar();
        });
	});

        var cont = 0;
        total = 0;
        subtotal = [];
        $("#guardar").hide();
       

        function agregar() {
            idproducto = $("#pidproducto").val();
            producto = $("#pidproducto option:selected").text();
            cantidad = $("#pcantidad").val();
            precio_compra = $("#pprecio_compra").val();

            if (idproducto != "" && cantidad != "" && cantidad > 0 && precio_compra != "") 
			{
                subtotal[cont] = (cantidad * precio_compra);
                total = total + subtotal[cont];

                var fila = '<tr class="selected" id="fila' + cont + '">';
                fila = fila + '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
                fila = fila + '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>';
                fila = fila +'<td><input type="number" name="precio_compra[]" value="' + precio_compra + '"></td>';
                fila = fila + '<td>' + subtotal[cont] + '</td>';
				fila = fila + '<td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(' + cont + ')">Eliminar</button></td>';
                fila = fila + '</tr>';
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

        function evaluar() 
		{
            if (total > 0) 
			{
                $("#guardar").show();
            }
			 else 
			{
                $("#guardar").hide();
            }
        }

        function eliminar(index) {
            total = total - subtotal[index];
            $("#total").html("Gs/. " + total);
            $("#fila" + index).remove();
            evaluar();
        }

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