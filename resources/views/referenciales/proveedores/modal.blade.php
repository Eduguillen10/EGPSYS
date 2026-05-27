<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $proveedor->idproveedor }}">
    <form action="{{ url('referenciales/proveedores/'.$proveedor->idproveedor) }}" method="POST">
        <input type="hidden" name="_method" value="delete">
        {{ csrf_field() }}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Eliminar proveedor</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea eliminar el proveedor: {{ $proveedor->razonsocial }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </form>
</div>
