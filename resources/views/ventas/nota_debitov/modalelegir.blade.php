<div class="modal fade modal-slide-in-right" aria-hidden="true" role="dialog" tabindex="-1" id="modal-elegir">
<form action="{{ URL('ventas/nota_debitov/insertar_facturas') }}" method="POST">
        <input type="hidden" name="_method" value="POST">
        {{ csrf_field() }}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-lavel="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <h4 class="modal-title">Seleccionar factura</h4>
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
                            <div class="form-group">
                                <label for="factura">Nro. Factura</label>
                                <select name="factura_modal" id="nro_factura" class="form-control selectpicker" data-live-search="true" onchange="datafactura();" onkeyup="datafactura();" onclick="datafactura();" onfocus="datafactura();">
                                    @foreach($venta as $fac)
                                        <option value="{{$fac->idventa}}" data-condicion="{{$fac->condicion}}">{{$fac->idventa}} - {{date('d/m/Y', strtotime($fac->fecha))}} - {{$fac->nro_factura}} - {{$fac->cliente}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>      

                        <!-- Campo hidden para el nÃºmero de factura -->
                        <input type="hidden" name="idventa" id="idventa_hidden" value="">
                        <!-- Campo hidden para el nÃºmero de factura -->
                        <input type="hidden" name="nro_factura" id="nro_factura_hidden" value="">
                        
                        <!-- Campo hidden para la condición -->
                        <input type="hidden" name="condicion" id="condicion_hidden" value="">    


                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                    <label for="fecha">Fecha Factura</label>
                                    <input type="date" name="fecha" id="idfecha" value="{{ old('fecha', date('Y-m-d') )}}" class="form-control" placeholder="Fecha Factura...">
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
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                    </div>
                </div>
        </div>
    </form> 
</div>
@push ('scripts')
<script>

    $(document).ready(function() {
            // Llamar a la función datafactura() cuando se cargue la pÃ¡gina
            datafactura();
        });

    function datafactura() {
        var selectedOption = $('#nro_factura').find(':selected');
        var facturaText = selectedOption.text();
        var parts = facturaText.split('-');
        var condicion = selectedOption.data('condicion');
        var idventa = parts[0].trim(); // Obtener el ID de la factura
        
        var nro_factura_value = parts[2].trim() + '-' + parts[3].trim() + '-' + parts[4].trim(); // Concatenar los campos del nÃºmero de factura
        //var condicion = parts[1].trim(); // Obtener la segunda parte, que es la condición completa
        $('#idventa_hidden').val(idventa); // Asignar el ID de la factura
        $('#nro_factura_hidden').val(nro_factura_value);
        $('#condicion_hidden').val(condicion);
        // Agregar un console.log para verificar si la función se estÃ¡ llamando correctamente
        console.log("Evento change activado. Condición:", condicion);
        console.log("Evento change activado. idventa:", idventa);
        console.log("Evento change activado. nro_factura:", nro_factura_value);
    }
    
</script>
@endpush
