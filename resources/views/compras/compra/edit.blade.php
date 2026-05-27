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
					<label for="sucursal">Nro Compra</label>
					<p>{{$compra->idcompra}}</p>
			</div>
    	</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Sucursal</label>
					<p>{{$compra->sucursal}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="sucursal">Depósito</label>
					<p>{{$compra->deposito}}</p>
			</div>
    	</div> 
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="emplado">Usuario</label>
					<p>{{$compra->usuario}}</p>
			</div>
    	</div>
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">Proveedor</label>
					<p>{{$compra->proveedor}}</p>
			</div>
    	</div> 
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="proveedor">RUC</label>
					<p>{{$compra->ruc}}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="idordencompra">Número Orden</label>
					<p>{{$compra->idordencompra}}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="fecha">Fecha Factura</label>
					<p>{{ date('d/m/Y', strtotime($compra->fecha_factura)) }}</p>
			</div>
    	</div>  
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="factura">Nro Factura</label>
				<p>{{ $compra->nro_factura }}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="timbrado">Nro Timbrado</label>
				<p>{{ $compra->timbrado }}</p>
			</div>
		</div>  	   	
    	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
				<label for="condicion">Condición</label>
				<p>{{ $compra->condicion }}</p>
			</div>
		</div>
		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
			<div class="form-group">
					<label for="fecha_vencimiento">Fecha Vencimiento</label>
					<p>{{ date('d/m/Y', strtotime($compra->fecha_vencimiento)) }}</p>
			</div>
    	</div>
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
				<label for="concepto">Concepto</label>
				<p>{{ $compra->concepto }}</p>
			</div>
		</div>
    </div>	
	<form action="{{url('compras/compra/'.$compra->idcompra)}}" method="POST" autocomplete="off" id="compraForm"> 
	@method('PUT')
  	@csrf

  	<div class="row">
    	<div class="panel panel-primary">
      	<div class="panel-heading">
        	<h3 class="panel-title">Detalle de la Compra</h3>
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
			@if ($detalles->isNotEmpty())
    @foreach ($detalles as $det)
        <tr>
            <td>
                <input type="hidden" name="idcompra_detalle[]" value="{{ $det->idcompra_detalle }}">
                <input type="hidden" name="idproducto[{{ $det->idcompra_detalle }}]" value="{{ $det->idproducto }}">
                {{ $det->idproducto }}
            </td>
            <td>{{ $det->producto }}</td>
            <td><input type="number" name="cantidad[{{ $det->idcompra_detalle }}]" value="{{ $det->cantidad }}" min="1" required></td>
            <td><input type="number" name="precio_compra[{{ $det->idcompra_detalle }}]" value="{{ $det->precio_compra }}" min="1" required></td>
            <td>{{ number_format(($det->cantidad ?? 0) * ($det->precio_compra ?? 0), 2) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" style="text-align: center;">No se encontraron detalles para esta compra.</td>
    </tr>
@endif
            </tbody>
          </table>
        </div>
							
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
					<div class="form-group">
						<button class="btn btn-success" type="submit">Actualizar</button>
						<button class="btn btn-light" onclick="window.location.href='{{ url('compras/compra/create') }}'" type="button">
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