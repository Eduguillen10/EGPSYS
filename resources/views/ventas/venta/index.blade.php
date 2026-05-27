@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ventas <a href="venta/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('ventas/venta.search')
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
					<th>Razón Social</th>
					<th>RUC - C.I.</th>
					<th>Factura</th>
					<th>Timbrado</th>
					<th>Condicion</th>
					<th>Estado</th>
					<th>Total</th>
					<th>Opciones</th>
				</thead>
				@foreach ($ventas as $ven)
				<tr>
					<td>{{ $ven->idventa}}</td>
					<td>{{ $ven->fecha}}</td>
					<td>{{ $ven->sucursal}}</td>
					<td>{{ $ven->cliente}}</td>
					<td>{{ $ven->num_documento}}</td>
					<td>{{ $ven->nro_factura}}</td>
					<td>{{ $ven->nro_timbrado}}</td>
					<td>{{ $ven->condicion}}</td>
					<td>@include('ventas.partials.estado', ['estado' => $ven->estado])</td>
					<td>{{ number_format($ven->totalventa, 0, ',', '.')}}</td>					
					<td>
						<a href="{{URL('ventas/venta/'.$ven->idventa)}}"><button class="btn btn-primary">Detalles</button></a>
						<a href="" data-target="#modal-delete-{{$ven->idventa}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
					</td>
				</tr>
				@include('ventas.venta.modal')
				@endforeach
			</table>
		</div>
		{{$ventas->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection
