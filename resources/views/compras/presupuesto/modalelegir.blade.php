<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
<form action="{{ URL('compras/presupuesto/insertar_pedidos') }}" method="POST">
        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Seleccione un Pedido</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="pedido">Nro. Pedido</label>
                                <select name="numero_pedido" id="numero_pedido" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Seleccione un pedido</option>
                                    @foreach($pedidos as $ped)
                                        <option value="{{$ped->idpedidocompra}}" >{{$ped->idpedidocompra}} - {{date('d/m/Y', strtotime($ped->fecha))}} - {{$ped->usuario}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="proveedor">Proveedor</label>
                                <select name="proveedor" id="proveedor" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Seleccione un proveedor</option>
                                    @foreach($proveedores as $prov)
                                        <option value="{{$prov->idproveedor}}" data-ruc="{{$prov->ruc}}">{{$prov->razonsocial}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group">
                                <label>Fecha Validez</label>
                                <input type="date" name="fechavalidez" id="fechavalidez_modal" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>
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
