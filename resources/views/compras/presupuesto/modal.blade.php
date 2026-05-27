<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-delete-{{$pre->idpresupuestocompra}}">
    <form action="{{ route('presupuesto.destroy', $pre->idpresupuestocompra) }}" method="POST"> 
        @method('DELETE') {{-- Importante para indicar que es un método DELETE --}}
        @csrf  {{-- Proteccion de ataques contra las peticiones HTTP --}}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Anular Presupuesto</h4>
                </div>
                <div class="modal-body">
                    <p>Confirme si desea Anular el Presupuesto</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </form>	
</div>
