@extends('layouts.admin')
@section('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Ciudades <a href="{{ url('referenciales/ciudades/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales.ciudades.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre de la Ciudad</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($ciudades as $ciudad)
				<tr>
					<td>{{ $ciudad->idciudad}}</td>
					<td>{{ $ciudad->descripcion}}</td>
					<td>@include('referenciales.partials.estado', ['estado' => $ciudad->estado ?? 'Activo'])</td>
					<td>
						<a href="{{ url('referenciales/ciudades/'.$ciudad->idciudad.'/edit') }}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{ $ciudad->idciudad }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.ciudades.modal')
				@endforeach
			</table>
		</div>
		{{ $ciudades->appends(Request::only(['searchText']))->render() }}
	</div>
</div>

@endsection
