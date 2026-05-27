@extends('layouts.admin')
@section('contenido')

@if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

<div class="row">
  <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
      <label for="idpresupuestocompra">Nº Presupuesto</label>
      <p>{{ $presupuesto->idpresupuestocompra }}</p>
    </div>
  </div>

  <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
      <label for="idsucursal">Sucursal</label>
      <p>{{ $presupuesto->descripcion }}</p> </div>
  </div>

  <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
      <label for="idproveedor">Proveedor</label>
      <p>{{ $presupuesto->razonsocial }}</p> </div>
  </div>

  <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
      <label for="ruc">RUC</label>
      <p>{{ $presupuesto->ruc }}</p> </div>
  </div>

  <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
    <div class="form-group">
      <label for="idpedidocompra">Número Pedido</label>
      <p>{{ $presupuesto->idpedidocompra }}</p>
    </div>
  </div>

  <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    <div class="form-group">
      <label for="usuario">Usuario</label>
      <p>{{ $presupuesto->usuario }}</p>
    </div>
  </div>

  <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
    <div class="form-group">
      <label for="observacion">Observacion</label>
      <p>{{ $presupuesto->observacion }}</p>
    </div>
  </div>

</div>

<form action="{{ url('compras/presupuesto/' . $presupuesto->idpresupuestocompra) }}" method="POST" autocomplete="off" id="presupuestoForm">
  @method('PUT')
  @csrf

  <div class="row">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Detalle del Presupuesto</h3>
      </div>
      <div class="panel-body">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
          <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
            <thead style="background-color:#ffd966">
              <th>Items</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio Compra</th>
            </thead>
            <tbody>
              @foreach ($detalles as $det)
                <tr>
                  <td>
                    <input type="hidden" name="idpresupuestocompra_detalle[]" value="{{ $det->idpresupuestocompra_detalle }}">
                    <input type="hidden" name="idproducto[{{ $det->idpresupuestocompra_detalle }}]" value="{{ $det->idproducto }}">
                    {{ $det->items }}
                  </td>
                  <td>{{ $det->producto }} {{ $det->marcas }}</td>
                  <td><input type="number" name="cantidad[{{ $det->idpresupuestocompra_detalle }}]" value="{{ $det->cantidad }}" min="1" required></td>
                  <td><input type="number" name="precio[{{ $det->idpresupuestocompra_detalle }}]" value="{{ $det->precio }}" min="1" required></td>
                  <td>{{ number_format($det->cantidad * $det->precio, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
          <div class="form-group">
            <button class="btn btn-success" type="submit">Actualizar</button>
            <button class="btn btn-light" onclick="window.location.href='{{ url('compras/presupuesto/create') }}'" type="button">
              <i class="fa fa-arrow-left"></i> Volver
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('presupuestoForm').addEventListener('submit', function(event) {
        var preciosCompra = document.querySelectorAll('input[name^="precio_compra["]');

for (var i = 0; i < preciosCompra.length; i++) {
  if (!preciosCompra[i].value) {
    alert('Todos los precios de compra deben tener un valor.');
    event.preventDefault();
    return;
  }
}
    });
  });
</script>
@endsection

@endsection
