<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
    <form action="{{ route('nota_creditoc.create') }}" method="GET">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Seleccionar compra</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <div class="form-group">
                                <label for="idcompra_modal">Compra / Factura</label>
                                <select name="idcompra" id="idcompra_modal" class="form-control selectpicker" data-live-search="true">
                                    @foreach($compras as $fac)
                                        <option value="{{ $fac->idcompra }}">
                                            Compra #{{ $fac->idcompra }} - {{ $fac->nro_factura }} - {{ $fac->proveedor }} - Saldo {{ number_format((int) $fac->montoapagar, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($compras->isEmpty())
                                    <p class="help-block">No existen compras con saldo pendiente para aplicar nota de credito.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" {{ $compras->isEmpty() ? 'disabled' : '' }}>Aceptar</button>
                </div>
            </div>
        </div>
    </form>
</div>
