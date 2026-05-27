@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Marcas <a href="marcas/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales/marcas.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre de la Marca</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($marcas as $mar)
				<tr>
					<td>{{ $mar->idmarca}}</td>
					<td>{{ $mar->descripcion}}</td>
					<td>@include('referenciales.partials.estado', ['estado' => $mar->estado ?? 'Activo'])</td>
					<td>
						<a href="{{URL('referenciales/marcas/'.$mar->idmarca.'/edit')}}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{$mar->idmarca}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.marcas.modal')
				@endforeach
			</table>
		</div>
		{{$marcas->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection
