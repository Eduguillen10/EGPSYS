@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Presupuesto 
			<a href="presupuesto/create"><button class="btn btn-success">Nuevo</button></a>
			<span>Total de Registros: {{ $total }}</span>
		</h3>
		@include('compras/presupuesto.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>ID</th>
					<th>Fecha</th> 
					<th>Fecha Validez</th> 
					<th>Sucursal</th>
					<th>Proveedor</th>  
					<th>RUC</th>
					<th>Pedido</th> 
					<th>Observacion</th>
					<th>Total</th>
					<th>Estado</th>
					<th>Opciones</th>  
				</thead>
				@foreach ($presupuestos_compras as $pre)
				<tr>
					<td>{{ $pre->idpresupuestocompra}}</td> 
					<td>{{date('d/m/Y', strtotime($pre->fecha))}}</td>  
					<td>{{date('d/m/Y', strtotime($pre->fechavalidez))}}</td> 
					<td>{{ $pre->descripcion}}</td> 
					<td>{{ $pre->razonsocial}}</td>   
					<td>{{ $pre->ruc}}</td> 
					<td>{{ $pre->idpedidocompra}}</td>
					<td>{{ $pre->observacion}}</td> 					
					<td>{{ $pre->totalpresupuesto_compra}}</td> 
					<td>{{ $pre->estado }}</td> 
					<td>
						<a href="{{URL('compras/presupuesto/'.$pre->idpresupuestocompra.'show')}}"><button class="btn btn-primary">Detalles</button></a>
						<a href="" data-target="#modal-delete-{{$pre->idpresupuestocompra}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('compras.presupuesto.modal') @endforeach
			</table>
		</div>
		{{$presupuestos_compras->appends(Request::only(['searchText']))->render()}} 
	</div>
</div>

@endsection
