<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{$ntv->idnota_creditov}}">
	<form action="{{ route('nota_creditov.destroy', $ntv->idnota_creditov) }}" method="POST">
		@csrf
		@method('DELETE')
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title">Anular la Nota de Crédito</h4>
				</div>
				<div class="modal-body">
					<p>Confirme si desea Anular la Nota de Crédito</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Confirmar</button>
				</div>
			</div>
		</div>
	</form>	
</div>