@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Timbrado <a href="timbrado/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales/timbrado.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nro Timbrado</th>
					<th>Nro. Inicial</th>
					<th>Nro. Actual</th>
					<th>Nro. Final</th>
					<th>Nro. Serie</th>
					<th>Fecha Inicial</th>					
					<th>Sucursal</th>
					<th>Fecha Vencimiento</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($timbrado as $tim)
				<tr>
					<td>{{ $tim->idtimbrado}}</td>
					<td>{{ $tim->nro_timbrado}}</td>
					<td>{{ $tim->nro_inicial}}</td>
					<td>{{ $tim->nro_actual}}</td>
					<td>{{ $tim->nro_final}}</td>					
					<td>{{ $tim->nro_serie}}</td>					
					<td>{{ $tim->fecha_inicial}}</td>					
					<td>{{ $tim->sucursal}}</td>
					<td>{{ $tim->fecha_vencimiento}}</td>							
					<td>{{ $tim->estado}}</td>			
					<td>
						<a href="{{URL('referenciales/timbrado/'.$tim->idtimbrado.'/edit')}}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{$tim->idtimbrado}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.timbrado.modal')
				@endforeach
			</table>
		</div>
		{{$timbrado->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection