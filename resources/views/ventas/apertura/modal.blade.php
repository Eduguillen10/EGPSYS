<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-cerrar-{{$aper->idapertura}}">
    <form action="{{ url('ventas/apertura/'.$aper->idapertura) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Está a punto de cerrar la Caja</h4>
                </div>

                <div class="modal-body">
                    <p>¿Está seguro que quiere cerrar la Caja?</p>
                    <p><b>ID Apertura:</b> {{ $aper->idapertura }}</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Cierre</button>
                </div>
            </div>
        </div>
    </form>
</div>
