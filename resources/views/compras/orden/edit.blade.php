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
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Nº Orden</label>
					<p>{{$orden->idordencompra}}</p>
			</div>
    	</div>
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$orden->sucursal}}</p>
			</div>
    	</div> 
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Proveedor</label>
					<p>{{$orden->proveedor}}</p>
			</div>
    	</div> 
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<p>{{$orden->ruc}}</p>
			</div>
    	</div>  
    	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
					<label for="idpresupuestocompra">Número Presupuesto</label>
					<p>{{$orden->idpresupuestocompra}}</p>
			</div>
    	</div>  	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="emplado">Usuario</label>
					<p>{{$orden->usuario}}</p>
			</div>
    	</div>
    	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="obs">Observacion</label>
				<p>{{ $orden->observacion }}</p>
			</div>
		</div>
    </div>	
	<form action="{{url('compras/orden/'.$orden->idordencompra)}}" method="POST" autocomplete="off" id="ordenForm"> 
	@method('PUT')
  	@csrf

  	<div class="row">
    	<div class="panel panel-primary">
      	<div class="panel-heading">
        	<h3 class="panel-title">Detalle de la Orden</h3>
      	</div>
      	<div class="panel-body">
        	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
          		<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
            		<thead style="background-color:#ffd966">
              			<th>Items</th>
              			<th>Producto</th>
              			<th>Cantidad</th>
              			<th>Precio Compra</th>
						<th>Total</th>
            		</thead>
            <tbody>
			@foreach ($detalles as $det)
                <tr>
                  <td>
                    <input type="hidden" name="idorden_detalle[]" value="{{ $det->idorden_detalle }}">
                    <input type="hidden" name="idproducto[{{ $det->idorden_detalle }}]" value="{{ $det->idproducto }}">
                    {{ $det->idproducto }}
                  </td>
                  <td>{{ $det->producto }}</td>
                  <td><input type="number" name="cantidad[{{ $det->idorden_detalle }}]" value="{{ $det->cantidad }}" min="1" required></td>
                  <td><input type="number" name="precio_compra[{{ $det->idorden_detalle }}]" value="{{ $det->precio_compra }}" min="1" required></td>
                  <td>{{ number_format($det->cantidad * $det->precio_compra, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
							
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
					<div class="form-group">
						<button class="btn btn-success" type="submit">Actualizar</button>
						<button class="btn btn-light" onclick="window.location.href='{{ url('compras/orden/create') }}'" type="button">
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
            document.getElementById('ordenForm').addEventListener('submit', function (event) {
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
    </script>
@endsection


@endsection