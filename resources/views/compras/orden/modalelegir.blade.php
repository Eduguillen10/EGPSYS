<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
<form action="{{ URL('compras/orden/insertar_presupuestos') }}" method="POST">
        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Seleccione un Presupuesto</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="presupuesto">Nro.Presupuesto</label>
                                <select name="numero_presupuesto" id="numero_presupuesto" class="form-control selectpicker" data-live-search="true">
                                    @foreach($presupuestos as $pre)
                                        <option value="{{$pre->idpresupuestocompra}}" >{{$pre->idpresupuestocompra}} - {{date('d/m/Y', strtotime($pre->fecha))}} {{$pre->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Depósito</label>
                                <select name="iddeposito" class="form-control selectpicker" id="deposito" data-Live-search="true" autofocus>
                                    @foreach($depositos as $dep)
                                    <option value="{{$dep->iddeposito}}">{{$dep->descripcion}}</option>
                                    @endforeach
                                </select>
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