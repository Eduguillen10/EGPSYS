@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Presupuesto 
			<a href="presupuesto/create"><button class="btn btn-success">Nuevo</button></a>
			<span>Total de Registros: {{ $total }}</span>
		</h3>
		@if (session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif
		@if (session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif
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
					<th>Monto</th>
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
					<td>{{ $pre->montopresupuesto_compra}}</td> 
					<td>{{ $pre->estado }}</td> 
					<td>
						<a href="{{ route('presupuesto.show', $pre->idpresupuestocompra) }}"><button class="btn btn-primary">Detalles</button></a>
						@if ($pre->estado !== 'Cancelado')
							<a href="" data-target="#modal-delete-{{$pre->idpresupuestocompra}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
						@endif
					</td>
				</tr>
				@include('compras.presupuesto.modal') @endforeach
			</table>
		</div>
		{{$presupuestos_compras->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5', 'searchText6', 'searchText7']))->render()}} 
	</div>
</div>

@endsection
