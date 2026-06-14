@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Listado de Stock</h3>
		@include('referenciales/stock.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Sucursal</th>
					<th>Depósito</th>
					<th>Producto</th>					
					<th>Cantidad</th>					
				</thead>
				@foreach ($stock as $stk)
				<tr>
					<td>{{ $stk->sucursal}}</td>
					<td>{{ $stk->deposito}}</td>
					<td>{{ $stk->producto}}</td>					
					<td>{{ \App\Helpers\NumberFormatter::cantidad($stk->cantidad) }}</td>					
				</tr>
				
				@endforeach
			</table>
		</div>
		{{$stock->appends(Request::only(['searchText']))->render()}}
	</div>
</div>

@endsection
