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
<div class="row">
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Nro Nota de Debito</label>
					<p>{{ $nota_debitov->nro_nota_debito ?? ('#' . $nota_debitov->idnota_debitov) }}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$nota_debitov->sucursal}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Depósito</label>
					<p>{{$nota_debitov->deposito}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="emplado">Usuario</label>
					<p>{{$nota_debitov->usuario}}</p>
			</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="cliente">Razón Social</label>
					<p>{{$nota_debitov->cliente}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="cliente">RUC-C.I.</label>
					<p>{{$nota_debitov->num_documento}}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="idventa">Nro. Venta</label>
					<p>{{$nota_debitov->idventa}}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="cliente">Fecha Factura</label>
					<p>{{ date('d/m/Y', strtotime($nota_debitov->fecha_factura)) }}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="factura">Nro. de Nota de Debito</label>
				<p>{{ $nota_debitov->nro_factura }}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="timbrado">Timbrado</label>
				<p>{{ $nota_debitov->timbrado }}</p>
			</div>
		</div> 
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="Concepto">Concepto</label>
				<p>{{ $nota_debitov->concepto }}</p>
			</div>
		</div>
    </div>	
	<form action="{{url('ventas/nota_debitov/'.$nota_debitov->idnota_debitov)}}" method="POST" autocomplete="off" id="ventaForm"> 
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
							<th>Precio Venta</th>
							<th>Opciones</th>
						</thead>
						<tbody>
							
							@foreach($detalles as $det)
								<tr>
									<td>
										<input type="hidden" name="idnota_debitov_detalle[]" value="{{ $det->idnota_debitov_detalle }}">
										<input type="hidden" name="idproducto[{{ $det->idnota_debitov_detalle }}]" value="{{ $det->idproducto }}">
										{{ $det->idproducto }}
									</td>
									<td>{{ $det->producto }}</td>
									<td><input type="number" name="cantidad[{{ $det->idnota_debitov_detalle }}]" value="{{ $det->cantidad }}" min="1" required></td>
									<td>
										<input type="number" name="precio_venta[{{ $det->idnota_debitov_detalle }}]" value="{{ $det->precio_venta }}" min="1" required>
									</td>
									<td>
										<button type="button"
											class="btn btn-danger"
											data-toggle="modal"
											data-target="#modal-delete-{{ $det->idnota_debitov_detalle }}">
											Eliminar
										</button>
									</td>
								</tr>
								
							@endforeach
						</tbody>
		    		</table>
		    	</div>
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
					<div class="form-group">
						<button class="btn btn-success" type="submit">Actualizar</button>
						<button class="btn btn-light" onclick="window.location.href='{{ url('ventas/nota_debitov/create') }}'" type="button">
							<i class="fa fa-arrow-left"></i> Volver
						</button>						
					</div>
    			</div>
    		</div>
    	</div>    	
    			
	</div>	
</form>

@foreach($detalles as $det)
    @include('ventas.nota_debitov.modaldetalles', ['det' => $det])
@endforeach

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('ventaForm').addEventListener('submit', function (event) {
                var preciosVenta = document.querySelectorAll('input[name^="precio_venta["]');

                for (var i = 0; i < preciosVenta.length; i++) {
                    if (!preciosVenta[i].value) {
                        alert('Todos los precios de venta deben tener un valor.');
                        event.preventDefault();
                        return;
                    }
                }
            });
        });
    </script>
@endsection


@endsection
