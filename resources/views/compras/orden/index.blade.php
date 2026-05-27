@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Orden de Compra 
            <a href="orden/create"><button class="btn btn-success">Nuevo</button></a>
            <span>Total de Registros: {{ $total }}</span>        
        </h3>
		@include('compras/orden.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>ID</th>
					<th>Fecha</th>
					<th>Sucursal</th>
					<th>Proveedor</th>
					<th>RUC</th>
					<th>Dirección</th>
					<th>Presupuesto</th>
					<th>Observacion</th>
					<th>Total</th>
					<th>Estado</th>
					<th>Opciones</th>
				</thead>
				@foreach ($orden_compras as $ord)
				<tr>
					<td>{{ $ord->idordencompra}}</td>
					<td>{{date('d/m/Y', strtotime($ord->fecha))}}</td>
					<td>{{ $ord->sucursal_descripcion}}</td>
					<td>{{ $ord->razonsocial}}</td>
					<td>{{ $ord->ruc}}</td>
					<td>{{ $ord->direccion}}</td>
					<td>{{ $ord->idpresupuestocompra}}</td> 
					<td>
						{{ $ord->idordencompra ? $ord->orden_observacion : $ord->observacion }}
					</td>					
					<td>{{ number_format($ord->total_orden_compra, 0, ',', '.')}}</td>
					<td>{{ $ord->estado}}</td>
					<td>
						<a href="{{URL('compras/orden/'.$ord->idordencompra.'show')}}"><button class="btn btn-primary">Detalles</button></a>
						<a href="" data-target="#modal-delete-{{$ord->idordencompra}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('compras.orden.modal')
				@endforeach
			</table>
		</div>
		{{$orden_compras->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection