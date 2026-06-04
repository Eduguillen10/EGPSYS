<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $aj->idajuste }}">
	<form action="{{ URL('compras/ajuste/'.$aj->idajuste) }}" method="POST">
		<input type="hidden" name="_method" value="delete">
		{{ csrf_field() }}
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title">Anular Ajuste</h4>
				</div>
				<div class="modal-body">
					<p>Confirme si desea anular el ajuste. El sistema registrara el movimiento inverso y actualizara nuevamente el stock.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-danger">Confirmar Anulacion</button>
				</div>
			</div>
		</div>
	</form>
</div>
