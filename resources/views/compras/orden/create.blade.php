@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
			<h3>Nueva Orden
			    <a href="" data-target="#modal-elegir" data-toggle="modal">
				<button class="btn btn-info"> <i class="fa fa-download" aria-hidden="true"></i> Seleccionar Presupuesto</button></a>
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
	@include('compras.orden.modalelegir')
            <form action="{{url('compras/orden')}}" method="POST" autocomplete="off" enctype="multipart/form-data" > 
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

<!-- Campo RUC -->
<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
        <label for="ruc">RUC</label>
        <input type="text" name="ruc" id="ruc" class="form-control" readonly>
    </div>
</div>

	<div class=" col-xxl-4 col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="form-group">
                            <label>Dirección</label>
                            <textarea name="direccion" id="direccion" class="form-control" rows="4" placeholder="Direccion..." ></textarea>
                            <p id="error_direccion" style="display: none; color: red;">La Descripcion debe tener al menos 10 caracteres.</p>
                            <script>
                                $(document).ready(function () {
                                    $("#direccion").keyup(function () {
                                        var direccion = $(this).val();
                                        if (direccion.length < 10) {
                                            $("#error_direccion").show();
                                        } else {
                                            $("#error_direccion").hide();
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>

	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="form-group">
                            <label>Observación</label>
                            <textarea name="observacion" id="observacion" class="form-control" rows="4" placeholder="Observación..." ></textarea>
                            <p id="error_observacion" style="display: none; color: red;">La observación debe tener al menos 10 caracteres.</p>
                            <script>
                                $(document).ready(function () {
                                    $("#observacion").keyup(function () {
                                        var observacion = $(this).val();
                                        if (observacion.length < 10) {
                                            $("#error_observacion").show();
                                        } else {
                                            $("#error_observacion").hide();
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
    
	<div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-body">

                    <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}" readonly>
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
                            <a href="{{url('compras/orden')}}" class="btn btn-default btn-sm">Cancelar</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</form>

<button class="btn btn-light" onclick="window.location.href='{{ url('compras/orden') }}'" type="button">
				<i class="fa fa-arrow-left"></i> Volver
			</button>
				
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@push ('scripts')
<script>
	$(document).ready(function(){
		$('#btnagregar').click(function(){
			agregar();
		});
	});

var cont=0;
total=0;
subtotal=[];
$("#guardar").hide();
	
	function agregar()
	{
		idproducto=$("#pidproducto").val();
		producto=$("#pidproducto option:selected").text();
		cantidad=$("#pcantidad").val();
		precio_compra=$("#pprecio_compra").val();
		
		if (idproducto!="" && cantidad!="" && cantidad>0 && precio_compra!="") 
		{
			subtotal[cont]=(cantidad*precio_compra);
			total=total+subtotal[cont];

			var    fila = '<tr class="selected" id="fila' + cont + '">' ;
            fila = fila + '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>' ;
            fila = fila + '<td><input type="number" name="cantidad[]" value="' + cantidad + '"></td>' ;
            fila = fila + '<td><input type="number" name="precio_compra[]" value="' + precio_compra + '"></td>' ;
            fila = fila + '<td>' + subtotal[cont] + '</td>' ;
			fila = fila + '<td><button type="button" class="btn btn-danger btn-xs" onclick="eliminar(' + cont + ')">Eliminar</button></td>';
            fila = fila + '</tr>' ;
            cont++;
            limpiar();
            $("#total").html("Gs/ "+total);
            evaluar();
            $("#detalles").append(fila) ;
		}
		else
		{
			alert("Error al ingresar el detalle del ingreso, revise los datos del producto");
		}
	}


	function limpiar(){
		$("#pcantidad").val("");
		$("#pprecio_compra").val("");
	}

	function evaluar()
	{
		if (total>0)
		{
			$("#guardar").show();
		}
		else
		{
			$("guardar").hide();
		}
	}

	function eliminar(index){
		total=total-subtotal[index];
		$("#total").html("Gs/. "+ total);
		$("#fila" + index).remove();
		evaluar();
	}

	//Para que me estire el numero de documento en el campo ruc al seleccionar el proveedor
	/*function dataproveedor (){
		$(document).ready(function() {
	        
            var ruc = $('#idproveedor').find('option:selected').data('ruc');
            $('#ruc').val(ruc);

            var direccion = $('#idproveedor').find('option:selected').data('direccion');
	        $('#direccion').val(direccion);

	        
	    });	
	}*/
	
	dataproveedor();

	function dataproveedor() {
    let ruc = $('#idproveedor option:selected').data('ruc'); // Obtiene el RUC del atributo data-ruc
    let direccion = $('#idproveedor option:selected').data('direccion'); // Obtiene la dirección del atributo data-direccion
    $('#ruc').val(ruc); // Asigna el RUC al campo correspondiente
    $('#direccion').val(direccion); // Asigna la dirección al campo correspondiente
}
	
	

</script>
@endpush
@endsection