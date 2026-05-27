@extends('layouts.admin')
@section('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Cargos <a href="{{ url('referenciales/cargos/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales.cargos.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre del Cargo</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($cargos as $cargo)
				<tr>
					<td>{{ $cargo->idcargo}}</td>
					<td>{{ $cargo->descripcion}}</td>
					<td>@include('referenciales.partials.estado', ['estado' => $cargo->estado ?? 'Activo'])</td>
					<td>
						<a href="{{ url('referenciales/cargos/'.$cargo->idcargo.'/edit') }}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{ $cargo->idcargo }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.cargos.modal')
				@endforeach
			</table>
		</div>
		{{ $cargos->appends(Request::only(['searchText']))->render() }}
	</div>
</div>

@endsection
