@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Listado de Nota de Remision <a href="{{ route('nota_remision_venta.create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
        @include('ventas.nota_remision.search')

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

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Nro. Remision</th>
                    <th>Fecha</th>
                    <th>Sucursal</th>
                    <th>Cliente</th>
                    <th>Nro. Doc</th>
                    <th>Factura</th>
                    <th>Motivo</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Chofer</th>
                    <th>Vehiculo</th>
                    <th>Recepcion</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach ($nota_remision as $nr)
                    <tr>
                        <td>{{ $nr->idnota_remision_venta }}</td>
                        <td>{{ $nr->nro_remision ?? '-' }}</td>
                        <td>{{ $nr->fecha_emision ? date('d/m/Y', strtotime($nr->fecha_emision)) : '-' }}</td>
                        <td>{{ $nr->sucursal }}</td>
                        <td>{{ $nr->cliente }}</td>
                        <td>{{ $nr->num_documento }}</td>
                        <td>{{ $nr->nro_factura }}</td>
                        <td>{{ $nr->motivo_traslado ?? 'Venta' }}</td>
                        <td>{{ $nr->fecha_inicio_traslado ? date('d/m/Y', strtotime($nr->fecha_inicio_traslado)) : '-' }}</td>
                        <td>{{ $nr->fecha_fin_traslado ? date('d/m/Y', strtotime($nr->fecha_fin_traslado)) : '-' }}</td>
                        <td>{{ trim($nr->chofer) ?: '-' }}</td>
                        <td>{{ $nr->vehiculo ?? '-' }}</td>
                        <td>
                            @if($nr->fecha_entrega)
                                {{ date('d/m/Y', strtotime($nr->fecha_entrega)) }}<br>
                                <small>{{ $nr->recibido_por ?? '-' }}</small>
                            @else
                                <span class="label label-warning">Pendiente</span>
                            @endif
                        </td>
                        <td>@include('ventas.partials.estado', ['estado' => $nr->estado])</td>
                        <td>
                            <a href="{{ route('nota_remision_venta.show', $nr->idnota_remision_venta) }}">
                                <button class="btn btn-primary">Detalles</button>
                            </a>
                            <a href="{{ route('nota_remision_venta.comprobante', $nr->idnota_remision_venta) }}" target="_blank">
                                <button class="btn btn-warning">Imprimir Nota de Remision</button>
                            </a>
                            @if(!in_array(strtoupper(trim((string)$nr->estado)), ['A', 'ANULADO', 'ANULADA'], true))
                                <a href="{{ route('nota_remision_venta.edit', $nr->idnota_remision_venta) }}">
                                    <button class="btn btn-success">Recepcion</button>
                                </a>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete-{{ $nr->idnota_remision_venta }}">
                                    Anular
                                </button>
                            @endif
                        </td>
                    </tr>
                    @include('ventas.nota_remision.modal')
                @endforeach
            </table>
        </div>

        {{ $nota_remision->appends(request()->all())->render() }}
    </div>
</div>
@endsection
