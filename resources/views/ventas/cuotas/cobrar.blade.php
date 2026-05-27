@extends('layouts.admin')
@section('contenido')

<div class="row">
  <div class="col-lg-12">
    <h3>Cobrar Cuotas - Producto Cliente #{{ $pc->idproducto_cliente }}</h3>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  </div>
</div>

<form method="POST" action="{{ route('cuotas.cobrar.crear') }}">
  @csrf
  <input type="hidden" name="idproducto_cliente" value="{{ $pc->idproducto_cliente }}">

  <div class="row" style="padding: 0 1em;">
    <div class="panel panel-primary">
      <div class="panel-body">

        <div class="row">
          <div class="col-lg-3">
            <label>Cliente</label>
            <input class="form-control" value="{{ $pc->idcliente }}" readonly>
          </div>
          <div class="col-lg-3">
            <label>Sucursal</label>
            <input class="form-control" value="{{ $pc->idsucursal }}" readonly>
          </div>
          <div class="col-lg-3">
            <label>Apertura</label>
            <input class="form-control" value="{{ $apertura->idapertura }}" readonly>
          </div>
          <div class="col-lg-3">
            <label>Caja</label>
            <input class="form-control" value="{{ $apertura->idcaja }}" readonly>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row" style="padding: 0 1em;">
    <div class="col-lg-12">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-condensed table-hover" id="tabla_cuotas">
          <thead style="background-color:#ffd966">
            <tr>
              <th style="text-align:center;">Pagar</th>
              <th>Cuota</th>
              <th>Vencimiento</th>
              <th style="text-align:right;">Capital (saldo)</th>
              <th style="text-align:right;">Interés Mora</th>
              <th style="text-align:right;">Interés Punitorio</th>
              <th style="text-align:right;">Exigible</th>
              <th style="text-align:right;">Monto a aplicar</th>
            </tr>
          </thead>
          <tbody>
          @foreach($cuotas as $c)
            @php
              $exigible = (float)$c->saldo_cuota + (float)$c->monto_interes_mora + (float)$c->monto_interes_punitorio;
            @endphp
            <tr>
              <td style="text-align:center;">
                <input type="checkbox" class="chk"
                       data-exigible="{{ $exigible }}"
                       data-id="{{ $c->idproducto_cliente_cuota }}">
              </td>
              <td>{{ $c->cuota }}</td>
              <td>{{ \Carbon\Carbon::parse($c->fecha_vto_cuota)->format('d/m/Y') }}</td>
              <td style="text-align:right;">{{ number_format($c->saldo_cuota, 0, ',', '.') }}</td>
              <td style="text-align:right;">{{ number_format($c->monto_interes_mora, 0, ',', '.') }}</td>
              <td style="text-align:right;">{{ number_format($c->monto_interes_punitorio, 0, ',', '.') }}</td>
              <td style="text-align:right;"><b>{{ number_format($exigible, 0, ',', '.') }}</b></td>
              <td style="text-align:right;">
                <input type="number" class="form-control monto"
                       min="0" step="1"
                       value="0"
                       disabled>
              </td>
            </tr>
          @endforeach
          </tbody>

          <tfoot>
            <tr>
              <td colspan="7" style="text-align:right;"><b>Total a cobrar</b></td>
              <td style="text-align:right;">
                <b><span id="total_view">0</span></b>
              </td>
            </tr>
          </tfoot>

        </table>
      </div>

      <button type="submit" class="btn btn-primary" id="btn_generar" disabled>
        Generar Cobro
      </button>

      <a href="javascript:history.back()" class="btn btn-default">Volver</a>
    </div>
  </div>

</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
function formatGs(n){
  try { return Number(n).toLocaleString('es-ES'); } catch(e){ return n; }
}

function recalcularTotal(){
  let total = 0;
  $('#tabla_cuotas tbody tr').each(function(){
    const chk = $(this).find('.chk');
    const monto = $(this).find('.monto');

    if(chk.is(':checked')){
      total += parseInt(monto.val() || '0', 10);
    }
  });

  $('#total_view').text(formatGs(total));
  $('#btn_generar').prop('disabled', total <= 0);
}

$(document).ready(function(){

  $('.chk').on('change', function(){
    const tr = $(this).closest('tr');
    const monto = tr.find('.monto');
    const exigible = parseInt($(this).data('exigible') || '0', 10);

    if($(this).is(':checked')){
      monto.prop('disabled', false);
      monto.val(exigible); // por defecto paga todo exigible
    } else {
      monto.prop('disabled', true);
      monto.val(0);
    }

    recalcularTotal();
  });

  $('.monto').on('input', function(){
    const tr = $(this).closest('tr');
    const chk = tr.find('.chk');
    const exigible = parseInt(chk.data('exigible') || '0', 10);

    let v = parseInt($(this).val() || '0', 10);
    if(v < 0) v = 0;
    if(v > exigible) v = exigible;
    $(this).val(v);

    recalcularTotal();
  });

  // Al enviar, armamos arrays idproducto_cliente_cuota[] y monto_aplicar[]
  $('form').on('submit', function(e){
    // limpiar inputs ocultos anteriores
    $('input[name="idproducto_cliente_cuota[]"]').remove();
    $('input[name="monto_aplicar[]"]').remove();

    $('#tabla_cuotas tbody tr').each(function(){
      const chk = $(this).find('.chk');
      const monto = $(this).find('.monto');

      if(chk.is(':checked')){
        const idCuota = chk.data('id');
        const m = parseInt(monto.val() || '0', 10);

        $('<input>').attr({type:'hidden', name:'idproducto_cliente_cuota[]', value:idCuota}).appendTo('form');
        $('<input>').attr({type:'hidden', name:'monto_aplicar[]', value:m}).appendTo('form');
      }
    });

    // validación rápida
    const totalTxt = $('#total_view').text().replace(/\./g,'');
    if(parseInt(totalTxt || '0',10) <= 0){
      e.preventDefault();
      alert('Seleccione al menos una cuota con monto mayor a 0.');
    }
  });

});
</script>

@endsection
