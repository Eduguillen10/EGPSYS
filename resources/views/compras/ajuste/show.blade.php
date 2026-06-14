@extends ('layouts.admin')
@section ('contenido')
@if(session('success'))
	<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row tm-detail-row">
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 tm-detail-wide">
		<div class="form-group">
			<label>Nro. Ajuste</label>
			<p>{{ $ajuste->idajuste }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Fecha</label>
			<p>{{ date('d/m/Y', strtotime($ajuste->fecha)) }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Usuario</label>
			<p>{{ $ajuste->usuario }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Sucursal</label>
			<p>{{ $ajuste->sucursal }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Deposito</label>
			<p>{{ $ajuste->deposito }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Tipo</label>
			<p>{{ $ajuste->tipo_ajuste }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Motivo</label>
			<p>{{ $ajuste->motivo }}</p>
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Estado</label>
			<p>{{ $ajuste->estado }}</p>
		</div>
	</div>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
		<div class="form-group">
			<label>Observacion</label>
			<p>{{ $ajuste->observacion ?: 'Sin observacion' }}</p>
		</div>
	</div>
</div>

<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Detalle del Ajuste</h3>
		</div>
		<div class="panel-body">
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
				<table class="table table-striped table-bordered table-condensed table-hover">
					<thead style="background-color:#ffd966">
						<th>Items</th>
						<th>Codigo</th>
						<th>Producto</th>
						<th>Cantidad</th>
					</thead>
					<tbody>
						@foreach($detalles as $det)
							<tr>
								<td>{{ $det->items }}</td>
								<td>{{ $det->codigo }}</td>
								<td>{{ $det->producto }}</td>
								<td>{{ \App\Helpers\NumberFormatter::cantidad($det->cantidad) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<a href="{{ url('compras/ajuste') }}" class="btn btn-default">
	<i class="fa fa-arrow-left"></i> Volver
</a>
@endsection
