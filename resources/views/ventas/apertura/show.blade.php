@extends('layouts.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
			<h3>Datos de la Apertura</h3>
			@if(count($errors) > 0)
				<div class="alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			@if(session('error'))
				<div class="alert alert-danger">
					{{ session('error') }}
				</div>
			@endif
		</div>
	</div>

	<div class="row" style="padding-right: 1em; padding-left: 1em;">
		<div class="panel panel-primary">
			<div class="panel-body">
				<div class="row">
					<!-- Número de Apertura -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="idapertura">N° Apertura</label>
							<input type="text" name="idapertura" class="form-control" value="{{ $apertura->idapertura }}" readonly>
						</div>
					</div>
					<!-- Fecha de Apertura -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="fecha_apertura">Fecha Apertura</label>
							<input type="date" name="fecha_apertura" class="form-control" value="{{ date('Y-m-d', strtotime($apertura->fecha_apertura)) }}" readonly>
						</div>
					</div>
					<!-- Caja -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="caja">Caja</label>
							<input type="text" name="caja" class="form-control" value="{{ $apertura->idcaja }}" readonly>
						</div>
					</div>
					<!-- Sucursal -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="sucursal">Sucursal</label>
							<input type="text" name="sucursal" class="form-control" value="{{ $apertura->idsucursal }}" readonly>
						</div>
					</div>
					<!-- Usuario -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="usuario">Usuario</label>
							<input type="text" class="form-control" value="{{ $apertura->usuario }}" readonly>
						</div>
					</div>
				</div><!-- fin row -->

				<div class="row">
					<!-- Monto Inicial -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="monto_inicial">Monto Inicial</label>
							<input type="text" name="monto_inicial" class="form-control" value="{{ number_format($apertura->monto_inicial, 0, ',', '.') }}" readonly>
						</div>
					</div>
					<!-- Fecha de Cierre -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="fecha_cierre">Fecha Cierre</label>
							<input type="date" name="fecha_cierre" class="form-control" value="{{ $apertura->fecha_cierre ? date('Y-m-d', strtotime($apertura->fecha_cierre)) : '' }}" readonly>
						</div>
					</div>
					<!-- Monto de Cierre -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="monto_cierre">Monto Cierre</label>
							<input type="text" name="monto_cierre" class="form-control" value="{{ $apertura->monto_cierre ? number_format($apertura->monto_cierre, 0, ',', '.') : 'Pendiente' }}" readonly>
						</div>
					</div>
					<!-- Estado -->
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label for="estado">Estado</label>
							<input type="text" name="estado" class="form-control" value="{{ $apertura->estado }}" readonly>
						</div>
					</div>
				</div><!-- fin row -->
			</div><!-- panel-body -->
		</div><!-- panel panel-primary -->
	</div><!-- fin row panel -->

	<!-- Nav tabs para mostrar detalles adicionales (opcional) -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detalle" aria-controls="detalle" role="tab" data-toggle="tab">Detalle de Apertura</a></li>
		<!-- Puedes agregar más pestañas si deseas mostrar otros datos -->
	</ul>

	<!-- Contenido de las pestañas -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="detalle">
			<br>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							<thead style="background-color:#ffd966">
								<th>Campo</th>
								<th>Valor</th>
							</thead>
							<tbody>
								<tr>
									<td>ID Apertura</td>
									<td>{{ $apertura->idapertura }}</td>
								</tr>
								<tr>
									<td>Caja</td>
									<td>{{ $apertura->idcaja }}</td>
								</tr>
								<tr>
									<td>Sucursal</td>
									<td>{{ $apertura->idsucursal }}</td>
								</tr>
								<tr>
									<td>Usuario</td>
									<td>{{ $apertura->usuario }}</td>
								</tr>
								<tr>
									<td>Fecha de Apertura</td>
									<td>{{ date('d/m/Y', strtotime($apertura->fecha_apertura)) }}</td>
								</tr>
								<tr>
									<td>Monto Inicial</td>
									<td>{{ number_format($apertura->monto_inicial, 0, ',', '.') }}</td>
								</tr>
								<tr>
									<td>Fecha de Cierre</td>
									<td>
										@if($apertura->fecha_cierre)
											{{ date('d/m/Y', strtotime($apertura->fecha_cierre)) }}
										@else
											<span class="text-muted">Sin cerrar</span>
										@endif
									</td>
								</tr>
								<tr>
									<td>Monto de Cierre</td>
									<td>
										@if($apertura->monto_cierre)
											{{ number_format($apertura->monto_cierre, 0, ',', '.') }}
										@else
											<span class="text-muted">Pendiente</span>
										@endif
									</td>
								</tr>
								<tr>
									<td>Estado</td>
									<td>
										<span class="badge {{ ($apertura->estado == 'Abierto') ? 'badge-success' : 'badge-danger' }}">
											{{ $apertura->estado }}
										</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<button class="btn btn-light" onclick="window.location.href='{{ url('ventas/apertura') }}'" type="button">
			<i class="fa fa-arrow-left"></i> Volver
		</button>
	</div><!-- fin tab-content -->
@endsection
