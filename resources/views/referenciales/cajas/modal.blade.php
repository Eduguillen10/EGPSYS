<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $caja->idcaja }}">
    <form action="{{ url('referenciales/cajas/'.$caja->idcaja) }}" method="POST">
        @csrf
        @method('DELETE') 
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Desactivar caja</h4>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea desactivar la caja?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Desactivar</button>
                </div>
            </div>
        </div>
    </form>
</div>

