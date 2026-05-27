@extends('layouts.admin')
@section('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Bancos <a href="{{ url('referenciales/bancos/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('referenciales.bancos.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre del Banco</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($bancos as $banco)
				<tr>
					<td>{{ $banco->idbanco}}</td>
					<td>{{ $banco->descripcion}}</td>
					<td>@include('referenciales.partials.estado', ['estado' => $banco->estado ?? 'Activo'])</td>
					<td>
						<a href="{{ url('referenciales/bancos/'.$banco->idbanco.'/edit') }}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{ $banco->idbanco }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('referenciales.bancos.modal')
				@endforeach
			</table>
		</div>
		{{ $bancos->appends(Request::only(['searchText']))->render() }}
	</div>
</div>

@endsection
