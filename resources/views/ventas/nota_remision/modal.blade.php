<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{ $nr->idnota_remision_venta }}">
    <form action="{{ route('nota_remision_venta.destroy', $nr->idnota_remision_venta) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Anular Nota de Remision</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea anular la nota de remision {{ $nr->nro_remision ?? '#' . $nr->idnota_remision_venta }}.</p>
                    <div class="form-group">
                        <label>Motivo de anulacion</label>
                        <input type="text" name="motivo_anulacion" class="form-control" maxlength="255" placeholder="Motivo...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </form>
</div>
