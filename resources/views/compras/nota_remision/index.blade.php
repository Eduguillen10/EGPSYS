@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>
            Listado de Nota de Remision de Compra
            <a href="{{ route('nota_remision_compra.create') }}"><button class="btn btn-success">Nuevo</button></a>
            <span>Total de Registros: {{ $total }}</span>
        </h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-warning">{{ session('info') }}</div>
        @endif

        @include('compras.nota_remision.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Nro. Comprobante</th>
                    <th>Fecha Remision</th>
                    <th>Sucursal</th>
                    <th>Proveedor</th>
                    <th>RUC</th>
                    <th>Deposito</th>
                    <th>Orden</th>
                    <th>Motivo</th>
                    <th>Chofer</th>
                    <th>Vehiculo</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($nota_remision as $nr)
                    <tr>
                        <td>{{ $nr->idremisionc }}</td>
                        <td>{{ $nr->nro_comprobante }}</td>
                        <td>{{ $nr->fecha_remision ? date('d/m/Y', strtotime($nr->fecha_remision)) : '-' }}</td>
                        <td>{{ $nr->sucursal }}</td>
                        <td>{{ $nr->proveedor }}</td>
                        <td>{{ $nr->ruc }}</td>
                        <td>{{ $nr->deposito }}</td>
                        <td>{{ $nr->idordencompra }}</td>
                        <td>{{ $nr->motivo_traslado }}</td>
                        <td>{{ $nr->chofer ?: '-' }}</td>
                        <td>{{ trim(($nr->vehiculo ?: '') . ' ' . ($nr->chapa ?: '')) ?: '-' }}</td>
                        <td>{{ $nr->estado }}</td>
                        <td>
                            <a href="{{ route('nota_remision_compra.show', $nr->idremisionc) }}">
                                <button class="btn btn-primary">Detalles</button>
                            </a>
                            @if(!in_array(strtoupper(trim((string) $nr->estado)), ['CANCELADO', 'ANULADO', 'ANULADA', 'A'], true))
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete-{{ $nr->idremisionc }}">
                                    Anular
                                </button>
                            @endif
                        </td>
                    </tr>
                    @include('compras.nota_remision.modal')
                @endforeach
            </table>
        </div>

        {{ $nota_remision->appends(request()->all())->render() }}
    </div>
</div>
@endsection
