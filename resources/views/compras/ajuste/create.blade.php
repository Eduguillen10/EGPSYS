@extends ('layouts.admin')
@section ('contenido')
<style>
	.ajuste-page {
		clear: both;
		display: block;
		position: relative;
		width: 100%;
		padding-left: 0;
	}

	.ajuste-panel {
		background: #fff;
		border: 1px solid #d9e2ec;
		border-radius: 4px;
		margin-bottom: 16px;
		padding: 16px;
		box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
	}

	.ajuste-panel .row {
		margin-left: -15px;
		margin-right: -15px;
	}

	.ajuste-detail-panel {
		border: 1px solid #428bca;
		border-radius: 4px;
		margin-bottom: 16px;
		overflow: visible;
	}

	.ajuste-detail-heading {
		background: #428bca;
		color: #fff;
		font-weight: 700;
		padding: 10px 14px;
	}

	.ajuste-detail-body {
		background: #fff;
		padding: 16px;
	}

	.ajuste-actions {
		margin-top: 12px;
		margin-bottom: 20px;
	}

	.ajuste-page .bootstrap-select {
		width: 100% !important;
	}

	.ajuste-page .bootstrap-select .dropdown-menu {
		left: 0 !important;
		max-width: 100% !important;
		min-width: 100% !important;
		width: 100% !important;
	}

	.ajuste-page .bootstrap-select .dropdown-menu > li > a {
		line-height: 1.35;
		overflow: visible;
		padding-bottom: 7px;
		padding-top: 7px;
		white-space: normal;
		word-break: normal;
	}

	.ajuste-page .bootstrap-select .filter-option {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.ajuste-page .bootstrap-select.btn-group .dropdown-menu.inner {
		max-width: 100% !important;
	}

</style>

@include('compras.partials.create-styles')

<div class="ajuste-page">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Nuevo Ajuste de Stock</h3>
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
		</div>
	</div>

	<form action="{{ url('compras/ajuste') }}" method="POST" autocomplete="off" id="ajusteForm">
		{{ csrf_field() }}
		<div class="ajuste-panel">
			<div class="row">
				<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
					<div class="form-group">
						<label>Sucursal</label>
						<input type="text" class="form-control" value="{{ $sucursal ? $sucursal->descripcion : 'Sin sucursal' }}" readonly>
						<input type="hidden" name="idsucursal" value="{{ $sucursal ? $sucursal->idsucursal : '' }}">
					</div>
				</div>
				<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
					<div class="form-group">
						<label>Deposito</label>
						<select name="iddeposito" class="form-control selectpicker" data-live-search="true" data-width="100%" required>
							<option value="">Seleccione deposito...</option>
							@foreach($depositos as $dep)
								<option value="{{ $dep->iddeposito }}" {{ old('iddeposito') == $dep->iddeposito ? 'selected' : '' }}>{{ $dep->descripcion }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
					<div class="form-group">
						<label>Fecha</label>
						<input type="date" name="fecha" class="form-control" value="{{ old('fecha', $fecha) }}" readonly>
					</div>
				</div>
				<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
					<div class="form-group">
						<label>Usuario</label>
						<input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
					<div class="form-group">
						<label>Tipo Ajuste</label>
						<select name="idtipo_ajuste" class="form-control selectpicker" data-live-search="true" data-width="100%" required>
							<option value="">Seleccione tipo...</option>
							@foreach($tiposAjuste as $tipo)
								<option value="{{ $tipo->idtipo_ajuste }}" {{ old('idtipo_ajuste') == $tipo->idtipo_ajuste ? 'selected' : '' }}>{{ $tipo->descripcion }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
					<div class="form-group">
						<label>Motivo Ajuste</label>
						<select name="idmotivo" class="form-control selectpicker" data-live-search="true" data-width="100%" required>
							<option value="">Seleccione motivo...</option>
							@foreach($motivos as $motivo)
								<option value="{{ $motivo->idmotivo }}" {{ old('idmotivo') == $motivo->idmotivo ? 'selected' : '' }}>{{ $motivo->descripcion }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
					<div class="form-group">
						<label>Observacion</label>
						<input type="text" name="observacion" class="form-control" maxlength="255" value="{{ old('observacion') }}" placeholder="Observacion...">
					</div>
				</div>
			</div>
		</div>

		<div class="ajuste-detail-panel">
			<div class="ajuste-detail-heading">
				Detalle del Ajuste
			</div>
			<div class="ajuste-detail-body">
				<div class="row">
					<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
						<div class="form-group">
							<label>Producto</label>
							<select class="form-control selectpicker" id="pidproducto" data-live-search="true" data-width="100%">
								@foreach($productos as $producto)
									<option value="{{ $producto->idproducto }}">{{ $producto->producto }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label>Cantidad</label>
							<input type="number" id="pcantidad" class="form-control" min="0.001" step="0.001" placeholder="Cantidad">
						</div>
					</div>
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
						<div class="form-group">
							<label>&nbsp;</label>
							<button type="button" id="bt_add" class="btn btn-primary form-control">Agregar</button>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#ffd966">
							<th>Opciones</th>
							<th>Producto</th>
							<th>Cantidad</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="ajuste-actions">
			<button class="btn btn-success" type="submit" id="guardar" disabled>Guardar</button>
			<button class="btn btn-danger" type="reset">Cancelar</button>
			<a href="{{ url('compras/ajuste') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Volver</a>
		</div>
	</form>
</div>

@push('scripts')
<script>
	$(function () {
		var cont = 0;

		function evaluar() {
			$('#guardar').prop('disabled', $('#detalles tbody tr').length === 0);
		}

		function limpiar() {
			$('#pcantidad').val('');
		}

		$('#bt_add').click(function () {
			var idproducto = $('#pidproducto').val();
			var producto = $('#pidproducto option:selected').text();
			var cantidad = parseFloat($('#pcantidad').val());

			if (!idproducto || !cantidad || cantidad <= 0) {
				alert('Debe seleccionar un producto e ingresar una cantidad mayor a cero.');
				return;
			}

			var fila = '<tr class="selected" id="fila' + cont + '">';
			fila += '<td><button type="button" class="btn btn-warning btn-xs btn-remove" data-row="fila' + cont + '">X</button></td>';
			fila += '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
			fila += '<td><input type="number" name="cantidad[]" value="' + cantidad + '" min="0.001" step="0.001" required></td>';
			fila += '</tr>';

			cont++;
			$('#detalles tbody').append(fila);
			limpiar();
			evaluar();
		});

		$(document).on('click', '.btn-remove', function () {
			$('#' + $(this).data('row')).remove();
			evaluar();
		});

		$('#ajusteForm').on('submit', function (event) {
			if ($('#detalles tbody tr').length === 0) {
				event.preventDefault();
				alert('Debe agregar al menos un producto al ajuste.');
			}
		});
	});
</script>
@endpush
@endsection
