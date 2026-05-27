@extends ('layouts.admin')
@section ('contenido')
	
	<div class="row">
		<div class="col-log-8 col-md8 col-sm-8 col-xs-12">
			<h3>Seleccionar una Sucursal</h3>
			@include('seleccionsucursales.seleccionar.search')
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					<thead>
						<th>ID</th>
						<th>Nombre</th>
						<th>Opciones</th>
					</thead>
					@foreach ($sucursales as $suc)
						<tr>
							<td>{{$suc->idsucursal}}</td>
							<td>{{$suc->descripcion}}</td>	
							<td>
								<a href="{{URL('/seleccionsucursales/seleccionar',$suc->idsucursal)}}"><button class="btn btn-info">Elegir</button></a>
								
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			{{$sucursales->render()}}
		</div>
	</div>
@endsection