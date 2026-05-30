<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $destinatario->iddestinatario_remision }}">
    <form action="{{ url('referenciales/destinatarios_remision/'.$destinatario->iddestinatario_remision) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Inactivar destinatario</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea inactivar el destinatario {{ $destinatario->nombre }}.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </div>
        </div>
    </form>
</div>
