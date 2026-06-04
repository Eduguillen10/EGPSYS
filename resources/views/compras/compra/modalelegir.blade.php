<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
	<form action="{{ URL('compras/compra/insertar_ordenes') }}" method="POST" autocomplete="off">
		{{ csrf_field() }}
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title">Seleccionar Orden de Compra</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
							<div class="form-group">
								<label for="numero_orden">Orden de Compra</label>
								<select name="numero_orden" id="numero_orden" required class="form-control selectpicker" data-live-search="true">
									<option value="">Seleccione una orden...</option>
									@foreach($ordenes as $ord)
										<option value="{{ $ord->idordencompra }}" {{ old('numero_orden') == $ord->idordencompra ? 'selected' : '' }}>
											Orden #{{ $ord->idordencompra }} - {{ $ord->razonsocial }} - {{ $ord->deposito }}
										</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
							<div class="form-group">
								<label>Fecha Registro</label>
								<input type="date" name="fecha" id="fecha_compra_modal" class="form-control" value="{{ old('fecha', $fecha) }}" readonly>
							</div>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
							<div class="form-group">
								<label>Fecha Factura</label>
								<input type="date" name="fecha_factura" id="fecha_factura_modal" class="form-control" value="{{ old('fecha_factura', $fecha) }}" required>
							</div>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
							<div class="form-group">
								<label>Nro. Factura</label>
								<input type="text" name="nro_factura" required value="{{ old('nro_factura') }}" class="form-control" maxlength="30" placeholder="001-001-0000001">
							</div>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
							<div class="form-group">
								<label>Timbrado</label>
								<input type="text" name="timbrado" required value="{{ old('timbrado') }}" class="form-control" maxlength="25" placeholder="Timbrado...">
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<div class="form-group">
								<label>Condicion</label>
								<select name="condicion" class="form-control" id="condicion_compra_modal" required>
									@foreach(['Contado', 'Credito 8 dias', 'Credito 15 dias', 'Credito 30 dias', 'Credito 60 dias', 'Credito 90 dias', 'Credito 120 dias'] as $condicion)
										<option value="{{ $condicion }}" {{ old('condicion') == $condicion ? 'selected' : '' }}>{{ $condicion }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
							<div class="form-group">
								<label>Fecha Vencimiento</label>
								<input type="date" name="fecha_vencimiento" id="fecha_vencimiento_modal" class="form-control" value="{{ old('fecha_vencimiento', $fecha) }}" readonly>
							</div>
						</div>
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
							<div class="form-group">
								<label>Concepto</label>
								<input type="text" name="concepto" value="{{ old('concepto') }}" class="form-control" maxlength="100" placeholder="Concepto de la compra...">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Confirmar</button>
				</div>
			</div>
		</div>
	</form>
</div>

@push('scripts')
<script>
	(function () {
		function actualizarFechaVencimientoCompra() {
			var condicion = document.getElementById('condicion_compra_modal').value;
			var fechaFactura = document.getElementById('fecha_factura_modal').value;
			var dias = 0;

			if (condicion !== 'Contado') {
				var matches = condicion.match(/\d+/g);
				dias = matches ? matches.reduce(function (acc, num) {
					return acc + parseInt(num, 10);
				}, 0) : 0;
			}

			var fecha = new Date(fechaFactura + 'T00:00:00');
			fecha.setDate(fecha.getDate() + dias);
			document.getElementById('fecha_vencimiento_modal').value = fecha.toISOString().split('T')[0];
		}

		document.addEventListener('DOMContentLoaded', function () {
			var condicion = document.getElementById('condicion_compra_modal');
			var fechaFactura = document.getElementById('fecha_factura_modal');

			if (condicion && fechaFactura) {
				condicion.addEventListener('change', actualizarFechaVencimientoCompra);
				fechaFactura.addEventListener('change', actualizarFechaVencimientoCompra);
				actualizarFechaVencimientoCompra();
			}
		});
	})();
</script>
@endpush
