@extends ('layouts.admin')
@section ('contenido')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
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

			@if(session('error'))
				<div class="alert alert-danger">
					{{ session('error') }}
				</div>
			@endif
<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Nro Nota de Crédito</label>
					<p>{{$nota_creditoc->idnota_creditoc}}</p>
			</div>
    	</div>
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
					<label for="emplado">Usuario</label>
					<p>{{$nota_creditoc->usu_inser}}</p>
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
					<label for="idcompra">Nro. Compra</label>
					<p>{{$nota_creditoc->idcompra}}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Fecha Factura</label>
					<p>{{ date('d/m/Y', strtotime($nota_creditoc->fecha_factura)) }}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="factura">Nro. de Nota de Crédito</label>
				<p>{{ $nota_creditoc->nro_factura }}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="timbrado">Timbrado</label>
				<p>{{ $nota_creditoc->timbrado }}</p>
			</div>
		</div> 
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="Concepto">Concepto</label>
				<p>{{ $nota_creditoc->concepto }}</p>
			</div>
		</div>
    </div>	
	<form action="{{url('compras/nota_creditoc/'.$nota_creditoc->idnota_creditoc)}}" method="POST" autocomplete="off" id="compraForm"> 
	<input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}
    <div class="row">
    	<div class="panel penel-primary">
    		<div class="panel-body">    			

		    	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		    		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
		    			<thead style="background-color:#A9D0F5">
							<th>Items</th>							
							<th>Producto</th>
							<th>Cantidad</th>
							<th>Precio Compra</th>
							<th>Opciones</th>
						</thead>
						<tbody>
							
							@foreach($detalles as $det)
								<tr>
									<td>
										<input type="hidden" name="idnota_creditoc_detalle[]" value="{{ $det->idnota_creditoc_detalle }}">
										<input type="hidden" name="idproducto[{{ $det->idnota_creditoc_detalle }}]" value="{{ $det->idproducto }}">
										{{ $det->idproducto }}
									</td>
									<td>{{ $det->producto }}</td>
									<td><input type="number" name="cantidad[{{ $det->idnota_creditoc_detalle }}]" value="{{ $det->cantidad }}" min="1" required></td>
									<td>
										<input type="number" name="precio_compra[{{ $det->idnota_creditoc_detalle }}]" value="{{ $det->precio_compra }}" min="1" required>
									</td>
									<td>
										<a href="{{URL('compras/nota_creditoc/eliminardetalle/'.$det->idnota_creditoc_detalle)}}"><button type="button" class="btn btn-danger">Eliminar</button></a>
									</td>	
								</tr>
								
							@endforeach
						</tbody>
		    		</table>
		    	</div>
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
					<div class="form-group">
						<button class="btn btn-success" type="submit">Actualizar</button>
						<button class="btn btn-light" onclick="window.location.href='{{ url('compras/nota_creditoc/create') }}'" type="button">
							<i class="fa fa-arrow-left"></i> Volver
						</button>						
					</div>
    			</div>
    		</div>
    	</div>    	
    			
	</div>	
</form>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('compraForm').addEventListener('submit', function (event) {
                var preciosCompra = document.querySelectorAll('input[name^="precio_compra["]');

                for (var i = 0; i < preciosCompra.length; i++) {
                    if (!preciosCompra[i].value) {
                        alert('Todos los precios de compra deben tener un valor.');
                        event.preventDefault();
                        return;
                    }
                }
            });
        });

		document.addEventListener('DOMContentLoaded', function () {
        // Agregamos un evento click a todos los botones con la clase 'eliminar-fila'
        var eliminarBotones = document.querySelectorAll('.eliminar-fila');
        eliminarBotones.forEach(function(boton) {
            boton.addEventListener('click', function() {
                // Verificar si se está ejecutando el evento click
                console.log('Botón de eliminar clickeado');
                
                // Buscamos la fila asociada al botón actual
                var fila = boton.closest('tr');
                // Verificar si se encontró la fila correctamente
                console.log('Fila a eliminar:', fila);
                
                // Eliminamos la fila
                fila.remove();
            });
        });
    });

    </script>
@endsection


@endsection