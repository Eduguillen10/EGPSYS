<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{$cob->id_cobro}}">
    <form action="{{ route('cobro.destroy', $cob->id_cobro) }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Anular Cobro</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea anular el cobro #{{ $cob->id_cobro }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </div>
        </div>
    </form>
</div>
