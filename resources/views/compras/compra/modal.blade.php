<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{$comp->idcompra}}">
	<form action="{{URL('compras/compra/'.$comp->idcompra)}}" method="POST"> 
        <input type="hidden" name="_method" value="delete">
        {{ csrf_field() }}
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title">Anular Compra</h4>
				</div>
				<div class="modal-body">
					<p>Confirme si desea anular la compra. El sistema revertira el stock, generara el movimiento inverso y actualizara la cuenta a pagar asociada.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-danger">Confirmar Anulacion</button>
				</div>
			</div>
		</div>
	</form>	
</div>
