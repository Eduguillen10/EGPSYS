@extends('layouts.admin')

@section('contenido')
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <h3>Cuotas - Crédito #{{ $productocliente->idproducto_cliente }}</h3>

    {{-- Mensajes --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors && $errors->any())
      <div class="alert alert-danger">
        <ul style="margin:0;padding-left:18px;">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</div>

{{-- Resumen del crédito --}}
<div class="row" style="margin-top:10px;">
  <div class="col-lg-12">
    <div class="panel panel-primary">
      <div class="panel-heading">Datos del crédito</div>
      <div class="panel-body">
        <div class="row">
          <div class="col-lg-4">
            <strong>Cliente:</strong>
            {{ $productocliente->cliente ?? '' }}
            @if(!empty($productocliente->num_documento))
              ({{ $productocliente->num_documento }})
            @endif
          </div>
          <div class="col-lg-4">
            <strong>Producto:</strong>
            {{ ($productocliente->codigo ?? '') }} - {{ ($productocliente->producto ?? '') }}
          </div>
          <div class="col-lg-4">
            <strong>Condición:</strong>
            {{ $productocliente->condicion_desc ?? '' }}
          </div>
        </div>

        <div class="row" style="margin-top:8px;">
          <div class="col-lg-3"><strong>Sucursal:</strong> {{ $productocliente->sucursal ?? '' }}</div>
          <div class="col-lg-3"><strong>Depósito:</strong> {{ $productocliente->deposito ?? '' }}</div>
          <div class="col-lg-3"><strong>Fecha:</strong> {{ $productocliente->fecha ?? '' }}</div>
          <div class="col-lg-3"><strong>Vencimiento:</strong> {{ $productocliente->fecha_vencimiento ?? '' }}</div>
        </div>

        <div class="row" style="margin-top:8px;">
          <div class="col-lg-3"><strong>Total venta:</strong> {{ number_format((int)($productocliente->total_venta ?? 0), 0, ',', '.') }}</div>
          <div class="col-lg-3"><strong>Entrega:</strong> {{ number_format((int)($productocliente->entrega ?? 0), 0, ',', '.') }}</div>
          <div class="col-lg-3"><strong>Total cuotas:</strong> {{ (int)($productocliente->total_cuotas ?? 0) }}</div>
          <div class="col-lg-3"><strong>Saldo total:</strong> {{ number_format((int)($productocliente->total_saldo ?? 0), 0, ',', '.') }}</div>
        </div>

        @if(!empty($productocliente->concepto))
        <div class="row" style="margin-top:8px;">
          <div class="col-lg-12"><strong>Concepto:</strong> {{ $productocliente->concepto }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Tabla de cuotas --}}
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Cuotas</div>
      <div class="panel-body table-responsive">
        <table class="table table-striped table-bordered table-condensed table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Vencimiento</th>
              <th>Monto</th>
              <th>Saldo</th>
              <th>Ref.</th>
              <th>Estado</th>
              <th style="width: 280px;">Pagar</th>
              <th style="width: 380px;">Editar</th>
              <th style="width: 120px;">Eliminar</th>
            </tr>
          </thead>
          <tbody>
            @foreach($productoclientecuota as $c)
              @php
                $idcuota = $c->idproducto_cliente_cuota;
                $monto   = (int)($c->monto_cuota ?? 0);
                $saldo   = (int)($c->saldo_cuota ?? 0);
                $estado  = $c->estado ?? '';
              @endphp

              <tr>
                <td>{{ $c->cuota }}</td>
                <td>{{ $c->fecha_vto_cuota }}</td>
                <td>{{ number_format($monto, 0, ',', '.') }}</td>
                <td>
                  <strong>{{ number_format($saldo, 0, ',', '.') }}</strong>
                </td>
                <td>{{ $c->refuerzo }}</td>
                <td>{{ $estado }}</td>

                {{-- Pagar --}}
                <td>
                  @if($saldo > 0)
                    <form method="POST" action="{{ url('ventas/producto_clientes/cuotas/pagar') }}" class="form-inline">
                      @csrf
                      <input type="hidden" name="idproducto_cliente_cuota" value="{{ $idcuota }}">
                      <div class="input-group" style="width: 220px;">
                        <input type="number" name="monto_pago" class="form-control input-sm"
                               min="1" max="{{ $saldo }}" value="{{ $saldo }}"
                               placeholder="Monto">
                        <span class="input-group-btn">
                          <button type="submit" class="btn btn-success btn-sm">Pagar</button>
                        </span>
                      </div>
                    </form>
                  @else
                    <span class="label label-success">Pagado</span>
                  @endif
                </td>

                {{-- Editar (inline) --}}
                <td>
                  <form method="POST" action="{{ url('ventas/producto_clientes/cuotas/'.$idcuota) }}">
                    @csrf
                    @method('PUT')

                    <div class="row" style="margin:0;">
                      <div class="col-xs-4" style="padding-left:0;">
                        <input type="number" name="monto_cuota" class="form-control input-sm"
                               value="{{ $monto }}" min="0" placeholder="Monto">
                      </div>
                      <div class="col-xs-4" style="padding-left:0;">
                        <input type="number" name="saldo_cuota" class="form-control input-sm"
                               value="{{ $saldo }}" min="0" placeholder="Saldo">
                      </div>
                      <div class="col-xs-4" style="padding-left:0;padding-right:0;">
                        <input type="text" name="refuerzo" class="form-control input-sm"
                               value="{{ $c->refuerzo }}" maxlength="1" placeholder="R/N">
                      </div>
                    </div>

                    <div class="row" style="margin:6px 0 0 0;">
                      <div class="col-xs-8" style="padding-left:0;">
                        <input type="date" name="fecha_vto_cuota" class="form-control input-sm"
                               value="{{ $c->fecha_vto_cuota }}">
                      </div>
                      <div class="col-xs-4" style="padding-left:0;padding-right:0;">
                        <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Guardar</button>
                      </div>
                    </div>
                  </form>
                </td>

                {{-- Eliminar --}}
                <td>
                  <form method="POST" action="{{ url('ventas/producto_clientes/cuotas/eliminar/'.$idcuota) }}"
                        onsubmit="return confirm('¿Eliminar esta cuota?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                  </form>
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>

        <div style="margin-top:10px;">
          <a href="{{ url('ventas/productos_clientes/'.$productocliente->idproducto_cliente) }}" class="btn btn-default">
            Volver
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection
