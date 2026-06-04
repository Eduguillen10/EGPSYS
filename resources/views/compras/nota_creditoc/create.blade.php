@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <h3>Nueva Nota de Credito de Compra
            <a href="" data-target="#modal-elegir" data-toggle="modal">
                <button class="btn btn-info" type="button">
                    <i class="fa fa-search"></i> Seleccionar Compra
                </button>
            </a>
            <a href="{{ route('nota_creditoc.index') }}">
                <button class="btn btn-light" type="button"><i class="fa fa-arrow-left"></i> Volver</button>
            </a>
        </h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

@include('compras.nota_creditoc.modalelegir')

@if($compra)
    <form action="{{ route('nota_creditoc.store') }}" method="POST" autocomplete="off">
        @csrf
        <input type="hidden" name="idcompra" value="{{ $compra->idcompra }}">

        <div class="row">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">Datos de la compra y comprobante</div>
                    <div class="panel-body">
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Sucursal</label>
                                <input type="text" class="form-control" value="{{ $compra->sucursal }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Deposito</label>
                                <input type="text" class="form-control" value="{{ $compra->deposito }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Usuario</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                            <div class="form-group">
                                <label>Proveedor</label>
                                <input type="text" class="form-control" value="{{ $compra->proveedor }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-1 col-md-1 col-xs-12">
                            <div class="form-group">
                                <label>RUC</label>
                                <input type="text" class="form-control" value="{{ $compra->ruc }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Saldo Cuenta</label>
                                <input type="text" class="form-control" value="{{ number_format((int) $compra->montoapagar, 0, ',', '.') }}" readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Nro. Compra</label>
                                <input type="text" class="form-control" value="{{ $compra->idcompra }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Factura Compra</label>
                                <input type="text" class="form-control" value="{{ $compra->nro_factura }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Fecha Comprobante</label>
                                <input type="date" name="fecha_factura" class="form-control" value="{{ old('fecha_factura', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Nro. Nota Credito</label>
                                <input type="text" name="nro_factura" class="form-control" value="{{ old('nro_factura') }}" maxlength="20" required>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Timbrado</label>
                                <input type="text" name="timbrado" class="form-control" value="{{ old('timbrado') }}" maxlength="20" required>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Estado</label>
                                <input type="text" class="form-control" value="Realizado" readonly>
                            </div>
                        </div>
                        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <div class="form-group">
                                <label>Concepto</label>
                                <input type="text" name="concepto" class="form-control" value="{{ old('concepto') }}" maxlength="100" placeholder="Concepto de la devolucion..." required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">Detalle de la nota de credito</div>
                    <div class="panel-body">
                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group">
                                <label>Producto</label>
                                <select id="pidproducto" class="form-control selectpicker" data-live-search="true">
                                    @foreach($detalles as $detalle)
                                        <option value="{{ $detalle->idproducto }}"
                                            data-producto="{{ $detalle->producto }}"
                                            data-precio="{{ $detalle->precio_compra }}"
                                            data-max="{{ $detalle->cantidad_disponible }}">
                                            {{ $detalle->producto }} - disponible {{ number_format($detalle->cantidad_disponible, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($detalles->isEmpty())
                                    <p class="help-block">La compra no posee productos disponibles para devolver.</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Cantidad</label>
                                <input type="number" id="pcantidad" class="form-control" min="1" placeholder="Cantidad">
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>Precio Compra</label>
                                <input type="number" id="pprecio_compra" class="form-control" min="1" placeholder="Precio">
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="bt_add" class="btn btn-primary form-control">Agregar</button>
                            </div>
                        </div>

                        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                <thead style="background-color:#A9D0F5">
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Compra</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">TOTAL</th>
                                        <th><h4 id="total">Gs/. 0</h4></th>
                                    </tr>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="guardar">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Guardar</button>
                                <button class="btn btn-danger" type="reset">Cancelar</button>
                                <a href="{{ route('nota_creditoc.index') }}" class="btn btn-light">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@else
    <div class="alert alert-info">
        Seleccione una compra para cargar la nota de credito.
    </div>
@endif

@push('scripts')
<script>
    $(function () {
        var cont = 0;
        var total = 0;
        var subtotal = [];
        var productosAgregados = {};

        $('#guardar').hide();

        $('#bt_add').on('click', function () {
            agregar();
        });

        $('#pidproducto').on('changed.bs.select change', function () {
            var precio = $('#pidproducto option:selected').data('precio') || '';
            $('#pprecio_compra').val(precio);
        }).trigger('change');

        function agregar() {
            var $option = $('#pidproducto option:selected');
            var idproducto = $('#pidproducto').val();
            var producto = $option.data('producto') || $option.text();
            var max = parseFloat($option.data('max') || 0);
            var cantidad = parseFloat($('#pcantidad').val() || 0);
            var precioCompra = parseFloat($('#pprecio_compra').val() || 0);

            if (!idproducto || cantidad <= 0 || precioCompra <= 0) {
                alert('Revise el producto, la cantidad y el precio de compra.');
                return;
            }

            if (cantidad > max) {
                alert('La cantidad no puede superar lo disponible para devolver.');
                return;
            }

            if (productosAgregados[idproducto]) {
                alert('El producto ya fue agregado al detalle.');
                return;
            }

            subtotal[cont] = cantidad * precioCompra;
            total += subtotal[cont];
            productosAgregados[idproducto] = true;

            var fila = '<tr class="selected" id="fila' + cont + '">';
            fila += '<td><button type="button" class="btn btn-warning" onclick="eliminarDetalle(' + cont + ', ' + idproducto + ');">X</button></td>';
            fila += '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>';
            fila += '<td><input type="number" name="cantidad[]" value="' + cantidad + '" readonly></td>';
            fila += '<td><input type="number" name="precio_compra[]" value="' + precioCompra + '" readonly></td>';
            fila += '<td>' + subtotal[cont].toLocaleString('es-PY') + '</td>';
            fila += '</tr>';

            $('#detalles tbody').append(fila);
            cont++;
            limpiar();
            evaluar();
        }

        function limpiar() {
            $('#pcantidad').val('');
            $('#pprecio_compra').val($('#pidproducto option:selected').data('precio') || '');
        }

        function evaluar() {
            $('#total').html('Gs/. ' + total.toLocaleString('es-PY'));
            $('#guardar').toggle(total > 0);
        }

        window.eliminarDetalle = function (index, idproducto) {
            total -= subtotal[index] || 0;
            delete productosAgregados[idproducto];
            $('#fila' + index).remove();
            evaluar();
        };
    });
</script>
@endpush
@endsection
