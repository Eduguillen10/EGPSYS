@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ajustes <a href="ajuste/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('compras.ajuste.search')
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif
		@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif
		@if(session('info'))
			<div class="alert alert-info">{{ session('info') }}</div>
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
					<th>Deposito</th>
					<th>Tipo de Ajuste</th>
					<th>Motivo</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($ajustes as $aj)
					<tr>
						<td>{{ $aj->idajuste }}</td>
						<td>{{ date('d/m/Y', strtotime($aj->fecha)) }}</td>
						<td>{{ $aj->usuario }}</td>
						<td>{{ $aj->sucursal }}</td>
						<td>{{ $aj->deposito }}</td>
						<td>{{ $aj->tipo_ajuste }}</td>
						<td>{{ $aj->motivo }}</td>
						<td>{{ $aj->estado }}</td>
						<td>
							<a href="{{ URL('compras/ajuste/'.$aj->idajuste) }}"><button class="btn btn-primary">Detalles</button></a>
							@if(! in_array($aj->estado, ['Cancelado', 'Anulado', 'Anulada']))
								<a href="" data-target="#modal-delete-{{ $aj->idajuste }}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
							@endif
						</td>
					</tr>
					@include('compras.ajuste.modal')
				@endforeach
			</table>
		</div>
		{{ $ajustes->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5']))->render() }}
	</div>
</div>
@endsection
