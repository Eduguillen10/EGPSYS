<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-edit-{{$ac->idproducto_cliente_cuota}}">
	{!! Form::open(array('url'=>'ventas/cuota_producto/create/'.$ac->idproducto_cliente_cuota, 'method'=>'patch'))!!}
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
				<h4 class="modal-title">Editar Cuota</h4>
			</div>
			
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="idproducto_cliente">ID</label>
							<input type="text" name="idproducto_cliente" class="form-control" value="{{$vc->idproducto_cliente}}" readonly>
							<input type="hidden" name="idsucursal" class="form-control" value="{{$vc->idsucursal}}">
							<input type="hidden" name="origen" class="form-control" value="{{$_SERVER['REQUEST_URI']}}">
						</div>
					</div>

					<div class="col-lg-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="cuota">Cuota</label>
							<input type="number" name="cuota" id="cuota" class="form-control" value="{{$ac->cuota}}" readonly>
						</div>
					</div>

					<div class="col-lg-6 col-lg-6 col-xs-12">
						<div class="form-group">
							<label for="refuerzo">Cuota de Refuerzo</label>
							<select name="refuerzo" class="form-control">
								@if ($vc->refuerzo == 'S')
									<option value="S" selected> Si</option>
									<option value="N">No</option>
								@else
									<option value="S" > Si</option>
									<option value="N" selected>No</option>
								@endif								
							</select>
						</div>
					</div>
				
					<div class="col-lg-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="fecha_vto_cuota">Fecha Vto. Cuota</label>
							<input type="date" name="fecha_vto_cuota" class="form-control" value="{{$ac->fecha_vto_cuota}}" >
						</div>
					</div>
			
				
					<div class="col-lg-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="saldo_cuota">Monto Cuota</label>
							<input type="number" step="1" name="monto_cuota" id="monto_cuota-{{$ac->idproducto_cliente_cuota}}" value="{{$ac->monto_cuota}}" class="form-control" placeholder="Monto cuota... " onkeyup="copyCuota(this.value, {{$ac->idproducto_cliente_cuota}});" >
						</div>
					</div>

					<div class="col-lg-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="saldo_cuota">Saldo Cuota</label>
							<input type="number" step="1" name="saldo_cuota" id="saldo_cuota-{{$ac->idproducto_cliente_cuota}}" value="{{$ac->saldo_cuota}}" class="form-control" placeholder="Saldo cuota... " readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary" onclick="$(this).button('loading')" data-loading-text="Confirmando...">Confirmar</button>
			</div>
		</div>
	</div>
	{{ Form::Close() }}
</div>