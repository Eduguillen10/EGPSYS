@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Listado de Nota de Debito <a href="nota_debitov/create"><button class="btn btn-success">Nuevo</button></a>
			</h3>
			@include('ventas/nota_debitov.search')
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- Verificar si hay mensajes de error -->
				@if(session('error'))
					<div class="alert alert-danger">
						{{ session('error') }}
					</div>
				@endif

				<!-- Verificar si hay mensajes de Ã©xito -->
				@if(session('success'))
					<div class="alert alert-success">
						{{ session('success') }}
					</div>
				@endif
			</div>
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
						<th>Razón Social</th>
						<th>Nro. Doc</th>
						<th>Nro. Nta. Debito</th>
						<th>Timbrado</th>
						<th>Concepto</th>
						<th>Nro.Venta</th>
						<th>Estado</th>
						<th>Total</th>
						<th>Opciones</th>
					</thead>
					@foreach ($nota_debitov as $ntv)
						<tr>
							<td>{{ $ntv->idnota_debitov}}</td>
							<td>{{date('d/m/Y', strtotime($ntv->fecha_registro))}}</td>
							<td>{{ $ntv->sucursal}}</td>
							<td>{{ $ntv->cliente}}</td>
							<td>{{ $ntv->num_documento}}</td>
							<td>{{ $ntv->nro_nota_debito ?? '-' }}</td>
							<td>{{ $ntv->timbrado}}</td>
							<td>{{ $ntv->concepto}}</td>
							<td>{{ $ntv->idventa}}</td>
							<td>@include('ventas.partials.estado', ['estado' => $ntv->estado])</td>
							<td>{{ number_format($ntv->totalventa, 0, ',', '.')}}</td>
							<td>
								<a href="{{ url('ventas/nota_debitov/' . $ntv->idnota_debitov) }}">
									<button class="btn btn-primary">Detalles</button>
								</a>
								<a href="{{ route('nota_debitov.comprobante', $ntv->idnota_debitov) }}" target="_blank">
									<button class="btn btn-warning">Imprimir Nota de Debito</button>
								</a>
								<button type="button" class="btn btn-danger" data-toggle="modal"
									data-target="#modal-delete-{{$ntv->idnota_debitov}}">
									Anular
								</button>
							</td>
						</tr>
						@include('ventas.nota_debitov.modal')
					@endforeach
				</table>
			</div>
			{{$nota_debitov->appends(Request::only(['searchText']))->render()}}
		</div>
	</div>

@endsection
