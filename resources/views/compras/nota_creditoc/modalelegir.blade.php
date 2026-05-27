<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
<form action="{{ URL('compras/nota_creditoc/insertar_facturas') }}" method="POST">
        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Elegir una Factura para insertar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
                            <div class="form-group">
                                <label for="factura">Nro. Factura</label>
                                <select name="numero_factura" id="numero_factura" class="form-control selectpicker" data-live-search="true">
                                    @foreach($compras as $fac)
                                        <option value="{{$fac->idcompra}}" >{{$fac->idcompra}} - {{date('d/m/Y', strtotime($fac->fecha_factura))}} - {{$fac->nro_factura}} - {{$fac->proveedor}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                         
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                    <label for="fecha_factura">Fecha Factura</label>
                                    <input type="date" name="fecha_factura" id="idfecha_factura" value="{{ old('fecha_factura', date('Y-m-d') )}}" class="form-control" placeholder="Fecha Factura...">
                            </div>
                        </div>                         
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                            <div class="form-group">
                                    <label for="nro_factura">Nro. De Nota de Credito</label>
                                    <input type="text" name="nro_factura" required value="{{old('nro_factura')}}" class="form-control" placeholder="Nro. Nota de Credito...">
                            </div>
    	                </div>    
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                    <label for="timbrado">Timbrado</label>
                                    <input type="text" name="timbrado" required value="{{old('timbrado')}}" class="form-control" placeholder="Timbrado...">
                            </div>
                        </div> 
                        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                            <div class="form-group">
                                    <label for="concepto">Concepto</label>
                                    <input type="text" name="concepto"  value="{{old('concepto')}}" class="form-control" placeholder="Concepto...">
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

<script>
    function actualizarFechaVencimientomodal() {
        var condicionmodal = document.getElementById('condicionmodal').value;
        var fechaFactura = document.getElementById('idfecha_factura').value;

        // Lógica para extraer números y calcular la fecha de vencimiento (similar a lo que hiciste en PHP)
        var dias = 0;

        if (condicionmodal !== 'Contado') {
            var matches = condicionmodal.match(/\d+/g);
            dias = matches ? matches.reduce((acc, num) => acc + parseInt(num), 0) : 0;
        }

        var fechaFacturaObj = new Date(fechaFactura);
        var fechaVencimientoObj = new Date(fechaFacturaObj.getTime() + dias * 24 * 60 * 60 * 1000);
        var fechaVencimiento = fechaVencimientoObj.toISOString().split('T')[0];

        // Actualizar el campo de fecha de vencimiento
        document.getElementById('idfecha_vencimiento').value = fechaVencimiento;
    }
</script>