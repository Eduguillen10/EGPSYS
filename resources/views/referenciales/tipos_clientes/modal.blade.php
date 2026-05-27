<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $tipoCliente->idtipo_cliente }}">
    <form action="{{ URL('referenciales/tipos_clientes/'.$tipoCliente->idtipo_cliente) }}" method="POST">
        <input type="hidden" name="_method" value="delete">
        {{ csrf_field() }}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Eliminar tipo de Cliente</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea eliminar el tipo de cliente</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </form>
</div>
