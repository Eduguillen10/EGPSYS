@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de Usuarios<a href="usuario/create"><button class="btn btn-success">Nuevo</button></a></h3>
		@include('acceso/usuario.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre</th>
					<th>Email</th>
					<th>Opciones</th>
				</thead>
				@foreach ($usuarios as $usu)
				<tr>
					<td>{{ $usu->id}}</td>
					<td>{{ $usu->name}}</td>
					<td>{{ $usu->email}}</td>
					<td>
						<a href="{{URL('acceso/usuario/'.$usu->id.'/edit')}}"><button class="btn btn-info">Editar</button></a>
						<a href="" data-target="#modal-delete-{{$usu->id}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('acceso.usuario.modal')
				@endforeach
			</table>
		</div>
		{{$usuarios->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection