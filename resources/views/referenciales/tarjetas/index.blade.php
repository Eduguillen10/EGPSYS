@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Tarjetas <a href="tarjetas/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales/tarjetas.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Tarjeta</th>	
					<th>Entidad Emisora</th>				
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($tarjetas as $tar)
				<tr>
					<td>{{ $tar->id_tarjeta}}</td>
					<td>{{ $tar->descripcion}}</td>
					<td>{{ $tar->entidademisora}}</td>
					<td>@include('referenciales.partials.estado', ['estado' => $tar->estado ?? 'Activo'])</td>
					<td>
						<a href="{{URL('referenciales/tarjetas/'.$tar->id_tarjeta.'/edit')}}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{$tar->id_tarjeta}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.tarjetas.modal')
				@endforeach
			</table>
		</div>
		{{$tarjetas->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection
