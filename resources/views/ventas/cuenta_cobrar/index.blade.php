@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Ctas. a Cobrar</h3>
		@include('ventas/cuenta_cobrar.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Sucursal</th>
					<th>Nro. Venta</th>
					<th>Nro. Factura</th>
					<th>Fecha</th>
					<th>Fecha de Vencimiento</th>				
					<th>Razón Social</th>
					<th>Importe</th>					
					<th>Saldo</th>						
										
				</thead>
				@foreach ($cuenta_cobrar as $ctacob)
				<tr>
					<td>{{ $ctacob->idcuenta_cobrar}}</td>
					<td>{{ $ctacob->sucursal}}</td>
					<td>{{ $ctacob->idventa}}</td>
					<td>{{ $ctacob->nro_factura}}</td>
					<td>{{ date('d/m/Y', strtotime($ctacob->fecha))}}</td>
					<td>{{ date('d/m/Y', strtotime($ctacob->fecha_vencimiento))}}</td>										
					<td>{{ $ctacob->cliente}}</td>
					<td>{{ $ctacob->importe}}</td>
					<td>{{ $ctacob->saldo}}</td>										
										
				</tr>
				
				@endforeach
			</table>
		</div>
		{{$cuenta_cobrar->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection