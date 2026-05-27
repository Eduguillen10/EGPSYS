@extends ('layouts.admin')
@section ('contenido')

    <div class="row">
        <div class="col-lg-12">
            <h3>Cobro #{{ $cobros->id_cobro }}</h3>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-warning">{{ session('info') }}</div>
            @endif
        </div>
    </div>

    <br>

    <div class="row" style="padding: 0 1em;">
        <div class="panel panel-primary">
            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-2">
                        <label>Fecha</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($cobros->fecha_cobro)->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label>Caja</label>
                        <input type="text" class="form-control" value="{{ $cobros->caja }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Apertura</label>
                        <input type="text" class="form-control" value="{{ $cobros->idapertura }}" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label>Sucursal</label>
                        <input type="text" class="form-control" value="{{ $cobros->sucursal }}" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label>Usuario</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="col-lg-6">
                        <label>Cliente</label>
                        <input type="text" class="form-control"
                            value="{{ $cobros->num_documento }} - {{ $cobros->cliente }}" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label>Total a Cobrar</label>
                        <input type="text" id="total_a_cobrar" class="form-control"
                            value="{{ number_format($cobros->monto_cobro, 0, ',', '.') }}" readonly>
                    </div>
                    <div class="col-lg-3">
                        <label>Total Pagado</label>
                        <input type="text" id="total_pagado" class="form-control"
                            value="{{ number_format($totalPagado, 0, ',', '.') }}" readonly>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#factura" aria-controls="factura" role="tab"
                data-toggle="tab">Factura</a></li>
        <li role="presentation"><a href="#forma_pago" aria-controls="forma_pago" role="tab" data-toggle="tab">Forma de
                Pago</a></li>
    </ul>

    <div class="tab-content">

        {{-- TAB FACTURA --}}
        <div role="tabpanel" class="tab-pane active" id="factura">
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color:#ffd966">
                                <th>Items</th>
                                <th>Factura</th>
                                <th>Condición</th>
                                <th>Total Factura</th>
                                <th>Cobrado</th>
                                <th>Opciones</th>
                            </thead>

                            @php $sum = 0; @endphp
                            @foreach ($cobrodetalle as $co)
                                @php $sum += $co->monto_detcobro; @endphp
                                <tr>
                                    <td>{{ $co->items }}</td>
                                    <td>{{ $co->nro_factura }}</td>
                                    <td>{{ $co->condicion }}</td>
                                    <td style="text-align:right;">{{ number_format($co->totalventa, 0, ',', '.') }}</td>
                                    <td style="text-align:right;">{{ number_format($co->monto_detcobro, 0, ',', '.') }}</td>
                                    <td style="text-align:center;">
                                        <form
                                            action="{{ route('cobro.factura.delete', ['id_cobro' => $cobros->id_cobro, 'id_detcobro' => $co->id_detcobro]) }}"
                                            method="POST" onsubmit="return confirm('Eliminar factura del cobro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs">X</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            <tfoot>
                                <tr>
                                    <td colspan="4"><b>Total Facturas</b></td>
                                    <td style="text-align:right;"><b>{{ number_format($sum, 0, ',', '.') }}</b></td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    <a class="btn btn-info" href="#forma_pago" data-toggle="tab">Siguiente</a>
                </div>
            </div>
        </div>

        {{-- TAB FORMA PAGO --}}
        <div role="tabpanel" class="tab-pane" id="forma_pago">
            <br>

            <form method="post" action="{{ route('cobro.finalizar', $cobros->id_cobro) }}">
                @csrf

                <div class="row" style="padding: 0 1em;">
                    <div class="panel panel-primary">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-lg-3">
                                    <label>Forma de Cobro</label>
                                    <select id="cobro_forma" class="form-control selectpicker" data-live-search="true">
                                        <option value="">Seleccione</option>
                                        @foreach ($forma_cobro as $foc)
                                            @php $esEfe = (mb_strtolower(trim($foc->descripcion)) === 'efectivo') ? 1 : 0; @endphp
                                            <option value="{{ $foc->id_formacobro }}"
                                                data-requiere-banco="{{ (int) $foc->requiere_banco }}"
                                                data-requiere-documento="{{ (int) $foc->requiere_documento }}"
                                                data-requiere-vencimiento="{{ (int) $foc->requiere_vencimiento }}"
                                                data-es-efectivo="{{ $esEfe }}">
                                                {{ $foc->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label>Banco</label>
                                    <select id="banco" class="form-control selectpicker" data-live-search="true">
                                        <option value="">Seleccione</option>
                                        @foreach ($banco as $ban)
                                            <option value="{{ $ban->identidademisora }}">{{ $ban->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2">
                                    <label>Documento</label>
                                    <input id="documento" type="text" class="form-control">
                                </div>

                                <div class="col-lg-2">
                                    <label>Fecha</label>
                                    <input id="fecha" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>

                                <div class="col-lg-2">
                                    <label>Vencimiento</label>
                                    <input id="fecha_vencimiento" type="date" class="form-control" value="">
                                </div>
                            </div>

                            <div class="row" style="margin-top:10px;">
                                <div class="col-lg-2">
                                    <label>Monto</label>
                                    <input id="monto" type="number" class="form-control" min="1" step="1">
                                    <small class="text-muted">Monto que se imputa al pago</small>
                                </div>

                                <div class="col-lg-2" id="wrap_recibido" style="display:none;">
                                    <label>Monto recibido</label>
                                    <input id="monto_recibido" type="number" class="form-control" min="0" step="1">
                                    <small class="text-muted">Solo efectivo</small>
                                </div>

                                <div class="col-lg-2" id="wrap_vuelto" style="display:none;">
                                    <label>Vuelto</label>
                                    <input id="vuelto_view" type="text" class="form-control" readonly>
                                </div>

                                <div class="col-lg-6" style="margin-top:25px;">
                                    <button type="button" id="btn_add_pago" class="btn btn-primary">Agregar</button>
                                    <button id="btn_finalizar" class="btn btn-success" type="submit" disabled>Finalizar</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row" style="padding: 0 1em;">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table id="tabla_pagos" class="table table-striped table-bordered table-condensed table-hover">
                                <thead style="background-color:#ffd966">
                                    <th>Items</th>
                                    <th>Forma</th>
                                    <th>Banco</th>
                                    <th>Documento</th>
                                    <th>Fecha</th>
                                    <th>Vencimiento</th>
                                    <th>Monto</th>
                                    <th>Recibido</th>
                                    <th>Vuelto</th>
                                    <th>Opciones</th>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($pagos as $p)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $p->formacobro }}</td>
                                            <td>{{ $p->banco }}</td>
                                            <td>{{ $p->documento }}</td>
                                            <td>{{ $p->fecha }}</td>
                                            <td>{{ $p->fecha_vencimiento }}</td>
                                            <td style="text-align:right;">{{ number_format($p->monto_detformacobro, 0, ',', '.') }}</td>
                                            <td style="text-align:right;">{{ $p->monto_recibido !== null ? number_format($p->monto_recibido, 0, ',', '.') : '-' }}</td>
                                            <td style="text-align:right;">{{ $p->vuelto !== null ? number_format($p->vuelto, 0, ',', '.') : '-' }}</td>
                                            <td style="text-align:center;">
                                                <form
                                                    action="{{ route('cobro.pago.delete', ['id_cobro' => $cobros->id_cobro, 'id_detformacobro' => $p->id_detformacobro]) }}"
                                                    method="POST" onsubmit="return confirm('Eliminar pago?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs">X</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="6"><b>Total</b></td>
                                        <td style="text-align:right;"><b><span id="total_pagos_view">{{ number_format($totalPagado, 0, ',', '.') }}</span></b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    @push('scripts')
<script>
    let cont = {{ count($pagos) + 1 }};
    let totalPagos = {{ (int) $totalPagado }};
    const totalCobro = {{ (int) $cobros->monto_cobro }};
    const allowPartial = {{ isset($allowPartial) && $allowPartial ? 'true' : 'false' }};

    function formatGs(n) {
        try { return Number(n).toLocaleString('es-ES'); } catch (e) { return n; }
    }

    function isEfectivoSelected() {
        const $opt = $('#cobro_forma option:selected');
        return Number($opt.data('es-efectivo')) === 1;
    }

    function restante() {
        return Math.max(0, totalCobro - totalPagos);
    }

    function refreshFinalizar() {
    $('#total_pagado').val(formatGs(totalPagos));
    $('#total_pagos_view').text(formatGs(totalPagos));

    let ok = false;

    if (allowPartial) {
        // CRÉDITO: permite parcial
        ok = (parseInt(totalPagos) > 0 && parseInt(totalPagos) <= parseInt(totalCobro));
    } else {
        // CONTADO: solo total exacto
        ok = (parseInt(totalPagos) === parseInt(totalCobro));
    }

    $('#btn_finalizar').prop('disabled', !ok);
}

    function calcularVueltoEnPantalla() {
        if (!isEfectivoSelected()) {
            $('#vuelto_view').val('');
            return;
        }

        let monto = parseInt($('#monto').val() || '0', 10);
        const recibido = parseInt($('#monto_recibido').val() || '0', 10);

        // Si el monto está vacío, se usa lo recibido como monto imputado,
        // sin superar el saldo restante.
        if ((!monto || monto <= 0) && recibido > 0) {
            monto = Math.min(recibido, restante());
        }

        if (!monto || monto <= 0 || !recibido || recibido <= 0) {
            $('#vuelto_view').val('');
            return;
        }

        const vuelto = recibido - monto;

        $('#vuelto_view').val(vuelto >= 0 ? formatGs(vuelto) : '');
    }

    function clampMontoToRestante() {
        const rest = restante();
        let m = parseInt($('#monto').val() || '0', 10);

        // si está vacío o 0, no fuerces nada
        if (!m || m <= 0) return;

        if (m > rest) {
            $('#monto').val(rest > 0 ? rest : '');
        }
    }

    function aplicarReglas() {
        const $opt = $('#cobro_forma option:selected');

        const requiereBanco = Number($opt.attr('data-requiere-banco') || 0) === 1;
        const requiereDoc   = Number($opt.attr('data-requiere-documento') || 0) === 1;
        const requiereVenc  = Number($opt.attr('data-requiere-vencimiento') || 0) === 1;
        const esEfectivo    = Number($opt.attr('data-es-efectivo') || 0) === 1;

        // Banco
        if (requiereBanco) {
            $('#banco').prop('disabled', false);
        } else {
            $('#banco').val('');
            $('#banco').prop('disabled', true);
        }

        // Refrescar SIEMPRE el selectpicker de banco
        $('#banco').selectpicker('refresh');

        // Documento
        $('#documento').prop('disabled', !requiereDoc);
        if (!requiereDoc) {
            $('#documento').val('');
        }

        // Vencimiento
        $('#fecha_vencimiento').prop('disabled', !requiereVenc);
        if (!requiereVenc) {
            $('#fecha_vencimiento').val('');
        }

        // Campos exclusivos de efectivo
        $('#wrap_recibido').toggle(esEfectivo);
        $('#wrap_vuelto').toggle(esEfectivo);

        const rest = restante();

        if (esEfectivo) {
            // EFECTIVO:
            // El monto imputado debe poder modificarse tanto en CONTADO como en CRÉDITO,
            // porque puede existir pago con varias formas de cobro.
            $('#monto').prop('readonly', false);

            let monto = parseInt($('#monto').val() || '0', 10);
            let recibido = parseInt($('#monto_recibido').val() || '0', 10);

            // Si el monto está vacío y ya hay monto recibido,
            // autocompleta el monto imputado con el recibido, sin superar el saldo restante.
            if ((!monto || monto <= 0) && recibido > 0) {
                $('#monto').val(Math.min(recibido, rest));
            }

            // Si el monto ingresado supera el saldo restante, lo ajusta.
            monto = parseInt($('#monto').val() || '0', 10);
            if (monto > rest) {
                $('#monto').val(rest > 0 ? rest : '');
            }

            $('#monto_recibido').focus();
            calcularVueltoEnPantalla();

        } else {
            $('#monto').prop('readonly', false);
            $('#monto_recibido').val('');
            $('#vuelto_view').val('');
        }

        clampMontoToRestante();
        calcularVueltoEnPantalla();
    }

    function limpiarInputs() {
        $('#cobro_forma').val('').selectpicker('refresh');
        $('#banco').val('').selectpicker('refresh');
        $('#documento').val('');
        $('#fecha').val('{{ date('Y-m-d') }}');
        $('#fecha_vencimiento').val('');
        $('#monto').val('');
        $('#monto_recibido').val('');
        $('#vuelto_view').val('');
        aplicarReglas();
    }

    // IMPORTANT: debe estar global para que el onclick la encuentre
    window.eliminarFila = function(idx, monto) {
        totalPagos -= monto;
        $('#fila_' + idx).remove();
        refreshFinalizar();
    }

    $(document).ready(function () {
        aplicarReglas();
        refreshFinalizar();

        $('#cobro_forma').change(aplicarReglas);

        $('#monto').on('input', function(){
            clampMontoToRestante();
            calcularVueltoEnPantalla();
        });

        $('#monto_recibido').on('input', function () {
            if (isEfectivoSelected()) {
                let monto = parseInt($('#monto').val() || '0', 10);
                let recibido = parseInt($('#monto_recibido').val() || '0', 10);

                if ((!monto || monto <= 0) && recibido > 0) {
                    $('#monto').val(Math.min(recibido, restante()));
                }
            }

            calcularVueltoEnPantalla();
        });

        $('#btn_add_pago').click(function () {
            const formaId = $('#cobro_forma').val();
            const formaTxt = $('#cobro_forma option:selected').text();

            const $opt = $('#cobro_forma option:selected');
            const requiereBanco = Number($opt.data('requiere-banco')) === 1;
            const requiereDoc = Number($opt.data('requiere-documento')) === 1;
            const requiereVenc = Number($opt.data('requiere-vencimiento')) === 1;
            const esEfectivo = Number($opt.data('es-efectivo')) === 1;

            const bancoId = $('#banco').val();
            const bancoTxt = $('#banco option:selected').text();

            const doc = $('#documento').val().trim();
            const fecha = $('#fecha').val();
            const venc = $('#fecha_vencimiento').val();

            const rest = restante();
            const monto = parseInt($('#monto').val() || '0', 10);

            if (!formaId) { alert('Seleccione una forma de cobro'); return; }
            if (requiereBanco && !bancoId) { alert('Esta forma requiere banco'); return; }
            if (requiereDoc && !doc) { alert('Esta forma requiere documento'); return; }
            if (requiereVenc && !venc) { alert('Esta forma requiere vencimiento'); return; }
            if (!fecha) { alert('Fecha obligatoria'); return; }
            // si es crédito + efectivo y no cargó monto, usamos el recibido como imputado (hasta el restante)
            let montoFinal = monto;

            if (esEfectivo && (!montoFinal || montoFinal <= 0)) {
                const recibidoTmp = parseInt($('#monto_recibido').val() || '0', 10);
                if (!recibidoTmp || recibidoTmp <= 0) { alert('En efectivo cargá el monto recibido'); return; }
                montoFinal = Math.min(recibidoTmp, rest);
            }

            if (!montoFinal || montoFinal <= 0) { alert('Monto inválido'); return; }


            // imputado nunca puede superar el restante
            if (montoFinal > rest) {
                alert('El monto no puede superar el saldo pendiente');
                return;
            }

            // imputado nunca puede superar total
            if (totalPagos + montoFinal > totalCobro) {
                alert('El total imputado no puede superar el total a cobrar');
                return;
            }

            // efectivo: recibido puede ser mayor, pero nunca menor al monto imputado
            let recibidoVal = '';
            let vueltoVal = '';
            if (esEfectivo) {
                const recibido = parseInt($('#monto_recibido').val() || '0', 10);
                if (!recibido || recibido <= 0) { alert('En efectivo cargá el monto recibido'); return; }
                if (recibido < montoFinal) { alert('En efectivo, el recibido no puede ser menor al monto'); return; }
                const vuelto = recibido - montoFinal;
                recibidoVal = recibido;
                vueltoVal = vuelto;
            }

            totalPagos += montoFinal;

            const fila = `
                <tr id="fila_${cont}">
                    <td>${cont}</td>

                    <td>
                        ${formaTxt}
                        <input type="hidden" name="id_formacobro[]" value="${formaId}">
                    </td>

                    <td>
                        ${requiereBanco ? bancoTxt : ''}
                        <input type="hidden" name="identidademisora[]" value="${requiereBanco ? bancoId : ''}">
                    </td>

                    <td>
                        ${requiereDoc ? doc : ''}
                        <input type="hidden" name="documento[]" value="${requiereDoc ? doc : ''}">
                    </td>

                    <td>
                        ${fecha}
                        <input type="hidden" name="fecha[]" value="${fecha}">
                    </td>

                    <td>
                        ${requiereVenc ? venc : ''}
                        <input type="hidden" name="fecha_vencimiento[]" value="${requiereVenc ? venc : ''}">
                    </td>

                    <td style="text-align:right;">
                        ${formatGs(montoFinal)}
                        <input type="hidden" name="monto[]" value="${montoFinal}">
                    </td>

                    <td style="text-align:right;">
                        ${recibidoVal !== '' ? formatGs(recibidoVal) : ''}
                        <input type="hidden" name="monto_recibido[]" value="${recibidoVal}">
                    </td>

                    <td style="text-align:right;">
                        ${vueltoVal !== '' ? formatGs(vueltoVal) : ''}
                        <input type="hidden" name="vuelto[]" value="${vueltoVal}">
                    </td>

                    <td style="text-align:center;">
                        <button type="button" class="btn btn-danger btn-xs" onclick="eliminarFila(${cont}, ${montoFinal})">X</button>
                    </td>
                </tr>
            `;

            $('#tabla_pagos tbody').append(fila);
            cont++;

            refreshFinalizar();
            limpiarInputs();
        });
    });
</script>
@endpush
@endsection
