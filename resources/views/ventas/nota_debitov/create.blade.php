@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
        <h3>Nueva Nota de Debito
            <a href="" data-target="#modal-elegir" data-toggle="modal">
                <button class="btn btn-info">
                    <i class="fa fa-download" aria-hidden="true"></i> Elegir
                </button>
            </a>
        </h3>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@include('ventas.nota_debitov.modalelegir')

@php
    $facturaSeleccionada = $facturaSeleccionada ?? null;
    $detallesSeleccionados = $detallesSeleccionados ?? collect();
    $selectedClienteId = old('idcliente', $facturaSeleccionada->idcliente ?? null);
    $selectedDepositoId = old('iddeposito', $facturaSeleccionada->iddeposito ?? null);
    $selectedVentaId = old('idventa', $facturaSeleccionada->idventa ?? null);
    $selectedDocumento = old('num_documento', $facturaSeleccionada->num_documento ?? '');
    $selectedFecha = old('fecha_factura', $fechaFacturaSeleccionada ?? date('Y-m-d'));
    $selectedNroNota = old('nro_factura', $facturaSeleccionada->nro_factura ?? '');
    $selectedTimbrado = old('timbrado', $facturaSeleccionada->nro_timbrado ?? '');
    $selectedConcepto = old('concepto', $conceptoSeleccionado ?? '');
    $detallesIniciales = $detallesSeleccionados->map(function ($det) {
        return [
            'idproducto' => $det->idproducto,
            'producto' => $det->producto,
            'cantidad' => $det->cantidad,
            'precio_venta' => $det->precio_venta,
        ];
    })->values();
@endphp

<form action="{{ url('ventas/nota_debitov') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
    @csrf

    <div class="row">
        {{-- SUCURSAL (visible sin name) + hidden real --}}
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Sucursal</label>
                @if($sucursales)
                    <input type="text" class="form-control" value="{{ $sucursales->descripcion }}" readonly>
                    <input type="hidden" name="idsucursal" value="{{ $sucursales->idsucursal }}">
                @else
                    <input type="text" class="form-control" value="No hay sucursal seleccionada" readonly>
                    <input type="hidden" name="idsucursal" value="">
                @endif
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="forcm-group">
                <label>Depósito</label>
                <select name="iddeposito" class="form-control selectpicker" id="iddeposito" data-live-search="true" autofocus>
                    @foreach($depositos as $dep)
                        <option value="{{ $dep->iddeposito }}" @selected((string) $selectedDepositoId === (string) $dep->iddeposito)>{{ $dep->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Usuario visible; el backend toma el id desde la sesion autenticada. --}}
        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
            </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
            <div class="form-group">
                <label>Razón Social</label>
                <select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true">
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->idcliente }}"
                                data-num_documento="{{ $cli->num_documento }}"
                                data-direccion="{{ $cli->direccion }}"
                                @selected((string) $selectedClienteId === (string) $cli->idcliente)>
                            {{ $cli->nombre }} - {{ $cli->num_documento }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>RUC-CI</label>
                <input type="text" name="num_documento" id="num_documento" class="form-control" value="{{ $selectedDocumento }}" readonly>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Fecha Factura</label>
                <input type="date" name="fecha_factura" id="fecha_factura"
                       value="{{ $selectedFecha }}"
                       class="form-control">
            </div>
        </div>

        {{-- SELECT factura (visual) + hidden idventa real; SIN duplicar name="idventa" --}}
        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
            <div class="form-group">
                <label>Factura Venta</label>
                <select id="idventa_select" class="form-control selectpicker" data-live-search="true">
                    @foreach($venta as $v)
                        @if(!in_array($v->idventa, $facturas_ya_almacenadas) || (string) $selectedVentaId === (string) $v->idventa)
                            <option value="{{ $v->idventa }}"
                                    data-nro_factura="{{ $v->nro_factura }}"
                                    data-fecha="{{ $v->fecha }}"
                                    @selected((string) $selectedVentaId === (string) $v->idventa)>
                                {{ $v->idventa }} - {{ $v->nro_factura }} - {{ date('d-m-y', strtotime($v->fecha)) }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="idventa" id="idventa_real" value="{{ $selectedVentaId }}">
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Nro. Venta</label>
                <input type="number" id="idventa_view" class="form-control" value="{{ $selectedVentaId }}" placeholder="Nro. Venta..." readonly>
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Nro. Nota de Debito</label>
                <input type="text" class="form-control" value="{{ $previewNroNota ?? 'Sin timbrado activo' }}" readonly>
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Factura afectada</label>
                <input type="text" name="nro_factura" required value="{{ $selectedNroNota }}"
                       class="form-control" placeholder="Nro. Factura..." readonly>
            </div>
        </div>

        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label>Timbrado</label>
                <input type="text" name="timbrado" required value="{{ $previewTimbrado ?? $selectedTimbrado }}"
                       class="form-control" placeholder="Timbrado..." readonly>
            </div>
        </div>

        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Concepto</label>
                <input type="text" name="concepto" required value="{{ $selectedConcepto }}"
                       class="form-control" placeholder="Concepto...">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel penel-primary">
            <div class="panel-body">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <div class="form-group">
                        <label>Producto</label>
                        <select class="form-control selectpicker" id="pidproducto" data-live-search="true">
                            @foreach($productos as $producto)
                                <option value="{{ $producto->idproducto }}">{{ $producto->producto }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" id="pcantidad" class="form-control" placeholder="Cantidad" min="1">
                    </div>
                </div>

                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <label>Precio Venta</label>
                        <input type="number" id="pprecio_venta" class="form-control" placeholder="P. venta" min="1">
                    </div>
                </div>

                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <div class="form-group">
                        <br>
                        <button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color:#A9D0F5">
                            <th>Opciones</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Venta</th>
                            <th>Total</th>
                        </thead>
                        <tfoot>
                            <th>TOTAL</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><h4 id="total">Gs/.0</h4></th>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ $facturaSeleccionada ? 'Actualizar' : 'Guardar' }}</button>
                <button class="btn btn-danger" type="reset">Cancelar</button>
                <button class="btn btn-light" onclick="window.location.href='{{ url('ventas/nota_debitov') }}'" type="button">
                    <i class="fa fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function () {
        // cliente -> num_documento
        function datacliente() {
            var num_documento = $('#idcliente').find('option:selected').data('num_documento');
            $('#num_documento').val(num_documento || '');
        }
        $('#idcliente').on('changed.bs.select change', datacliente);
        datacliente();

        // factura select -> idventa hidden + view
        var selectedVentaId = @json((string) ($selectedVentaId ?? ''));

        if (selectedVentaId) {
            $('#idventa_select').val(selectedVentaId);

            if ($.fn.selectpicker) {
                $('#idventa_select').selectpicker('refresh');
            }
        }

        function datafacturaventa() {
            var id = $('#idventa_select').val();
            $('#idventa_real').val(id || '');
            $('#idventa_view').val(id || '');
        }
        $('#idventa_select').on('changed.bs.select change', datafacturaventa);
        datafacturaventa();

        // detalles
        var cont = 0;
        var total = 0;
        var subtotal = [];
        var detallesIniciales = @json($detallesIniciales);
        $("#guardar").hide();

        $('#bt_add').click(function () {
            agregar();
        });

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function agregarDetalle(idproducto, producto, cantidad, precio_venta) {
            cantidad = parseFloat(cantidad);
            precio_venta = parseFloat(precio_venta);

            if (!idproducto || cantidad <= 0 || precio_venta <= 0) {
                return false;
            }

            subtotal[cont] = cantidad * precio_venta;
            total += subtotal[cont];

            var fila = '';
            fila += '<tr class="selected" id="fila' + cont + '">';
            fila += '<td><button type="button" class="btn btn-warning" onclick="eliminar(' + cont + ')">X</button></td>';
            fila += '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + escapeHtml(producto) + '</td>';
            fila += '<td><input type="number" name="cantidad[]" value="' + cantidad + '" min="1" required></td>';
            fila += '<td><input type="number" name="precio_venta[]" value="' + precio_venta + '" min="1" required></td>';
            fila += '<td>' + subtotal[cont] + '</td>';
            fila += '</tr>';

            cont++;
            $("#total").html("Gs/ " + total);
            $("#detalles tbody").append(fila);
            evaluar();
            return true;
        }

        function agregar() {
            var agregado = agregarDetalle(
                $("#pidproducto").val(),
                $("#pidproducto option:selected").text(),
                $("#pcantidad").val(),
                $("#pprecio_venta").val()
            );

            if (agregado) {
                $("#pcantidad").val("");
                $("#pprecio_venta").val("");
            } else {
                alert("Revise los datos del producto (cantidad y precio deben ser > 0).");
            }
        }

        window.eliminar = function (index) {
            total -= subtotal[index];
            $("#total").html("Gs/ " + total);
            $("#fila" + index).remove();
            evaluar();
        }

        function evaluar() {
            if (total > 0) $("#guardar").show();
            else $("#guardar").hide();
        }

        detallesIniciales.forEach(function (detalle) {
            agregarDetalle(detalle.idproducto, detalle.producto, detalle.cantidad, detalle.precio_venta);
        });
    });
</script>
@endpush

@endsection
