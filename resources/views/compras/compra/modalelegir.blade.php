<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
<form action="{{ URL('compras/compra/insertar_ordenes') }}" method="POST">
        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Seleccione una Orden</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
                            <div class="form-group">
                                <label for="orden">Nro.Orden</label>
                                <select name="numero_orden" id="numero_orden" class="form-control selectpicker" data-live-search="true">
                                    @foreach($ordenes as $ord)
                                        <option value="{{$ord->idordencompra}}" >{{$ord->idordencompra}} - {{date('d/m/Y', strtotime($ord->fecha))}} {{$ord->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d') )}}" placeholder="Fecha..." readonly>
                                <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                                <script>
                                    $(document).ready(function () {
                                        $("#fecha").flatpickr({
                                            dateFormat: "d-m-y",
                                            minDate: "today",
                                            maxDate: new Date().getFullYear() + 1, -1, -1,
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Fecha Factura</label>
                                <input type="date" name="fecha_factura" id="fecha_factura" class="form-control" value="{{ old('fecha_factura', date('Y-m-d'))}}" placeholder="Fecha Factura...">
                                <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                                <script>
                                    $(document).ready(function () {
                                        $("#fecha_factura").flatpickr({
                                            dateFormat: "d-m-y",
                                            minDate: "today",
                                            maxDate: new Date().getFullYear() + 1, -1, -1,
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                            <div class="form-group">
                                    <label for="nrofactura">Nro.Factura</label>
                                    <input type="text" name="nro_factura" required value="{{old('nro_factura')}}" class="form-control" placeholder="Nro.Factura...">
                            </div>
    	                </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                    <label for="nrotimbrado">Nro.Timbrado</label>
                                    <input type="text" name="timbrado" required value="{{old('timbrado')}}" class="form-control" placeholder="Timbrado...">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <div class="form-group">
                                <label>Condición</label>
                                <select name="condicion" class="form-control" id="condicionmodal" onchange="actualizarFechaVencimientomodal()">    				
                                    <option value="Contado">Contado</option>
                                    <option value="Credito 8 dias">Credito 8 dias</option>
                                    <option value="Credito 15 dias">Credito 15 dias</option>
                                    <option value="Credito 30 dias">Credito 30 dias</option>
                                    <option value="Credito 60 dias">Credito 60 dias</option>
                                    <option value="Credito 90 dias">Credito 90 dias</option>
                                    <option value="Credito 120 dias">Credito 120 dias</option>
                                </select>
                            </div>
                        </div>

    	                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', date('Y-m-d') )}}" placeholder="Fecha Vencimiento..." readonly>
                                <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.11/dist/flatpickr.min.js"></script>
                                <script>
                                    $(document).ready(function () {
                                        $("#fecha_vencimiento").flatpickr({
                                            dateFormat: "d-m-y",
                                            minDate: "today",
                                            maxDate: new Date().getFullYear() + 1, -1, -1,
                                        });
                                    });
                                </script>
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
        var fechaFactura = document.getElementById('fecha_factura').value;

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
        document.getElementById('fecha_vencimiento').value = fechaVencimiento;
    }
</script>