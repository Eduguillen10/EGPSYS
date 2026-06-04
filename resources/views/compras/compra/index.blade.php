@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Compras <a href="compra/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif
		@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif
		@if(session('info'))
			<div class="alert alert-info">{{ session('info') }}</div>
		@endif
		@include('compras/compra.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>ID</th>
					<th>Fecha</th>
					<th>Sucursal</th>
					<th>Proveedor</th>
					<th>RUC</th>
					<th>Nro.Factura</th>
					<th>Timbrado</th>
					<th>Condicion</th>
					<th>Concepto</th>
					<th>Nro.Orden</th>		
					<th>Estado</th>
					<th>Monto</th>
					<th>Opciones</th>
				</thead>
				@foreach ($compras as $comp)
				<tr>
					<td>{{ $comp->idcompra}}</td>
					<td>{{date('d/m/Y', strtotime($comp->fecha))}}</td>
					<td>{{ $comp->sucursal}}</td>
					<td>{{ $comp->proveedor}}</td>
					<td>{{ $comp->ruc}}</td>
					<td>{{ $comp->nro_factura}}</td>
					<td>{{ $comp->timbrado}}</td>
					<td>{{ $comp->condicion}}</td>
					<td>{{ $comp->concepto}}</td>
					<td>{{ $comp->idordencompra}}</td>
					<td>{{ $comp->estado}}</td>					
					<td>{{ number_format($comp->montocompra, 0, ',', '.')}}</td>					
					<td>
						<a href="{{URL('compras/compra/'.$comp->idcompra)}}"><button class="btn btn-primary">Detalles</button></a>
						@if($comp->estado == 'Pendiente')
							<a href="{{URL('compras/compra/'.$comp->idcompra.'/edit')}}"><button class="btn btn-warning">Actualizar</button></a>
						@endif
						@if(! in_array($comp->estado, ['Cancelado', 'Anulado', 'Anulada']))
							<a href="" data-target="#modal-delete-{{$comp->idcompra}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
						@endif
					</td>
				</tr>
				@include('compras.compra.modal')
				@endforeach
			</table>
		</div>
		{{$compras->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5', 'searchText6', 'searchText7']))->render()}}
	</div>
</div>
@endsection
