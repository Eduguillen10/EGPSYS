@extends ('layouts.admin')
@section ('contenido')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Detalle de Nota de Remision de Compra</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

<div class="row tm-detail-row">
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Nota</label>
            <p>{{ $remision->idremisionc }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Nro. Comprobante</label>
            <p>{{ $remision->nro_comprobante }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Orden</label>
            <p>{{ $remision->idordencompra }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Fecha Registro</label>
            <p>{{ $remision->fecha ? date('d/m/Y', strtotime($remision->fecha)) : '-' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Fecha Remision</label>
            <p>{{ $remision->fecha_remision ? date('d/m/Y', strtotime($remision->fecha_remision)) : '-' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Estado</label>
            <p>{{ $remision->estado }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Sucursal</label>
            <p>{{ $remision->sucursal }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Deposito</label>
            <p>{{ $remision->deposito }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
        <div class="form-group">
            <label>Proveedor</label>
            <p>{{ $remision->proveedor }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>RUC</label>
            <p>{{ $remision->ruc }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
        <div class="form-group">
            <label>Direccion</label>
            <p>{{ $remision->direccion }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
        <div class="form-group">
            <label>Motivo Traslado</label>
            <p>{{ $remision->motivo_traslado }}</p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-12">
        <div class="form-group">
            <label>Chofer</label>
            <p>{{ $remision->chofer ?: '-' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Doc. Chofer</label>
            <p>{{ $remision->documento_chofer ?: '-' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Vehiculo</label>
            <p>{{ $remision->vehiculo ?: '-' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Chapa</label>
            <p>{{ $remision->chapa ?: '-' }}</p>
        </div>
    </div>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 tm-detail-full">
        <div class="form-group">
            <label>Observacion</label>
            <p>{{ $remision->observacion ?: '-' }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color:#ffd966">
                            <th>Item</th>
                            <th>Producto</th>
                            <th>Cantidad Orden</th>
                            <th>Cantidad Remision</th>
                            <th>Precio Compra</th>
                        </thead>
                        <tbody>
                            @foreach($detalles as $det)
                                <tr>
                                    <td>{{ $det->items }}</td>
                                    <td>{{ $det->producto }}</td>
                                    <td>{{ \App\Helpers\NumberFormatter::cantidad($det->cantidad_orden) }}</td>
                                    <td>{{ \App\Helpers\NumberFormatter::cantidad($det->cantidad) }}</td>
                                    <td>{{ number_format($det->precio_compra, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('nota_remision_compra.index') }}" class="btn btn-default">
    <i class="fa fa-arrow-left"></i> Volver
</a>
@if(!in_array(strtoupper(trim((string) $remision->estado)), ['CANCELADO', 'ANULADO', 'ANULADA', 'A'], true))
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete-{{ $remision->idremisionc }}">
        Anular
    </button>
    @php $nr = $remision; @endphp
    @include('compras.nota_remision.modal')
@endif

@endsection
