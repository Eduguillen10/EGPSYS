@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Listado de Nota de Crédito <a href="nota_creditov/create"><button class="btn btn-success">Nuevo</button></a>
			</h3>
			@include('ventas/nota_creditov.search')
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- Verificar si hay mensajes de error -->
				@if(session('error'))
					<div class="alert alert-danger">
						{{ session('error') }}
					</div>
				@endif

				<!-- Verificar si hay mensajes de éxito -->
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
						<th>Nro. Nta. Crédito</th>
						<th>Timbrado</th>
						<th>Concepto</th>
						<th>Nro.Venta</th>
						<th>Estado</th>
						<th>Total</th>
						<th>Opciones</th>
					</thead>
					@foreach ($nota_creditov as $ntv)
						<tr>
							<td>{{ $ntv->idnota_creditov}}</td>
							<td>{{date('d/m/Y', strtotime($ntv->fecha_registro))}}</td>
							<td>{{ $ntv->sucursal}}</td>
							<td>{{ $ntv->cliente}}</td>
							<td>{{ $ntv->num_documento}}</td>
							<td>{{ $ntv->nro_nota_credito ?? '-' }}</td>
							<td>{{ $ntv->timbrado}}</td>
							<td>{{ $ntv->concepto}}</td>
							<td>{{ $ntv->idventa}}</td>
							<td>@include('ventas.partials.estado', ['estado' => $ntv->estado])</td>
							<td>{{ number_format($ntv->totalventa, 0, ',', '.')}}</td>
							<td>
								<a href="{{ url('ventas/nota_creditov/' . $ntv->idnota_creditov) }}">
									<button class="btn btn-primary">Detalles</button>
								</a>
								<a href="{{ route('nota_creditov.comprobante', $ntv->idnota_creditov) }}" target="_blank">
									<button class="btn btn-warning">Imprimir Nota de Credito</button>
								</a>
								<button type="button" class="btn btn-danger" data-toggle="modal"
									data-target="#modal-delete-{{$ntv->idnota_creditov}}">
									Anular
								</button>
							</td>
						</tr>
						@include('ventas.nota_creditov.modal')
					@endforeach
				</table>
			</div>
			{{$nota_creditov->appends(Request::only(['searchText']))->render()}}
		</div>
	</div>

@endsection
