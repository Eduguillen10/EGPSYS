@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Nota de Crédito <a href="nota_creditoc/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('compras/nota_creditoc.search')
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
					<th>Nro. Nta. Crédito</th>
					<th>Timbrado</th>					
					<th>Concepto</th>
					<th>Nro.Compra</th>		
					<th>Estado</th>
					<th>Total</th>
					<th>Opciones</th>
				</thead>
				@foreach ($nota_creditoc as $ntc)
				<tr>
					<td>{{ $ntc->idnota_creditoc}}</td>
					<td>{{date('d/m/Y', strtotime($ntc->fecha_registro))}}</td>
					<td>{{ $ntc->sucursal}}</td>
					<td>{{ $ntc->proveedor}}</td>
					<td>{{ $ntc->num_documento}}</td>
					<td>{{ $ntc->nro_factura}}</td>
					<td>{{ $ntc->timbrado}}</td>					
					<td>{{ $ntc->concepto}}</td>
					<td>{{ $ntc->idcompra}}</td>
					<td>{{ $ntc->estado}}</td>					
					<td>{{ number_format($ntc->totalcompra, 0, ',', '.')}}</td>					
					<td>
						<a href="{{URL('compras/nota_creditoc/'.$ntc->idnota_creditoc.'show')}}"><button class="btn btn-primary">Detalles</button></a>
						<a href="" data-target="#modal-delete-{{$ntc->idnota_creditoc}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
					</td>
				</tr>
				@include('compras.nota_creditoc.modal')
				@endforeach
			</table>
		</div>
		{{$nota_creditoc->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection