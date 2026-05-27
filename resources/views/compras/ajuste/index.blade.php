@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ajustes <a href="ajuste/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('compras/ajuste.search')
		@if (Session::has('message'))
			<div class="alert alert-success" style="color: red;">
				{{ Session::get('message') }}
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
					<th>Usuario</th>
					<th>Sucursal</th>
					<th>Depósito</th>
					<th>Tipo de Ajuste</th>	
					<th>Motivo</th>	
					<th>Opciones</th>
				</thead>
				@foreach ($ajustes as $aj)
				<tr>
					<td>{{ $aj->idajusteproducto}}</td>
					<td>{{ date('d/m/Y', strtotime($aj->fecha))}}</td>
					<td>{{ $aj->usuario}}</td>
					<td>{{ $aj->sucursales}}</td>
					<td>{{ $aj->depositos}}</td>	
					<td>{{ $aj->tipoajuste}}</td>
					<td>{{ $aj->motivo}}</td>
					<td>
						<a href="{{URL('compras/ajuste/'.$aj->idajusteproducto.'show')}}"><button class="btn btn-primary">Detalles</button></a>
						<a href="" data-target="#modal-delete-{{$aj->idajusteproducto}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('compras.ajuste.modal')
				@endforeach
			</table>
		</div>
		{{$ajustes->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection